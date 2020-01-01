<?php

/*
	This is a temporary solution (fix later)
*/
require_once 'common.php';

if ($action == 'syndicate')
{
	if (isset($title)) $syndication['service']['title'] = $title;
	if (isset($info))  $syndication['service']['description'] = $info;
	respond($syndication);
}

if ($action == 'range')
{
	if ($append_assembly)
	{
		$table .= $assembly;
	}
	if (1.0 * $bases / $pixels >= 5.0)
	{
		get_histogram($table, $assembly, $left, $right, $bases, $pixels);
	}
	else if (1.0 * $bases / $pixels >= 0.2)
	{
		get_boxes($table, $assembly, $left, $right, false);
	}
	else
	{
		get_boxes($table, $assembly, $left, $right, false);
	}
}

//Get read depth as histogram
function get_histogram($table, $assembly, $left, $right, $bases, $pixels)
{
	if ($table[0] == '+')
	{
		$table = substr($table,1);
		$query = "select 'read', strand, start, end, 1, 1, end-start+1, 0 from $table where (assembly='chr$assembly' or assembly='$assembly') and start <= $right and end >= $left";
	}
	else
	{
		$query = "select 'read', '+', start, end, 1, 1, end-start+1, 0 from $table where (assembly='chr$assembly' or assembly='$assembly') and start <= $right and end >= $left";
	}
	
	if (cache_exists($query)) cache_stream($query);

	$result = array();
	$unit = round($bases / $pixels);

	$d = query($query);
	
	while ($r = mysql_fetch_row($d))
	{
		$class  = $r[0];
		$strand = $r[1];
		$start  = $r[2] + 0;
		$end    = $r[3] + 0;
		$count  = $r[4] + 0;
		$copies = $r[5] + 0;
		$lenA   = $r[6] + 0;
		$lenB   = $r[7] + 0;
				
		//If the class is new then create a series to represent it
		if (!isset($result[$class]))
		{
			$result[$class] = array();
		}
							
		//Determine the range of x positions covered by the read
		$a1 = floor($start * $pixels / $bases);
		$a2 = ceil(($start+$lenA) * $pixels / $bases);
		
		$b1 = floor(($end-$lenB) * $pixels / $bases);
		$b2 = ceil($end * $pixels / $bases);
		
		//For each of the x positions, convert to a genome position and add to the count
		for ($x=$a1; $x<=$b2; $x++)
		{
			if ($x > $a2 && $x < $b1) continue;
			
			$gpos = $x * $bases / $pixels;
		
			if (!isset($result[$class]["$gpos"]))
			{
				$result[$class]["$gpos"] = array($gpos,0,0);
			}
			
			$amt = 0;
			
			if ($gpos < $start)
			{
				$amt = $gpos + $unit - $start;
			}
			else if ($gpos + $unit > $end && $gpos < $end)
			{
				$amt = $end - $gpos;
			}
			else
			{
				$amt = $unit;
			}
			
			$amt *= $copies;
			$amt = round($amt/($bases/$pixels));
			
			if ($strand == '+')
			{
				$result[$class]["$gpos"][1] += $amt;
			}
			else 
			{
				$result[$class]["$gpos"][2] += $amt;
			}
		}
	}
	
	//Simplify the data stream
	foreach ($result as $class => $data)
	{
		$clean = array();
		
		foreach ($data as $datum)
		{
			$clean[] = $datum;
		}
		$result[$class] = $clean;
	}

	//Create cache and stream to user
	cache_create($query, $result, true, $table);
}

//Get reads as boxes without sequence information
function get_boxes($table, $assembly, $left, $right, $seq)
{
	if ($table[0] == '+')
	{
		$table = substr($table,1);
		$query = "select 0, 'read', strand, start, end, end-start+1, 0, '', '' from $table where (assembly='chr$assembly' or assembly='$assembly') and start <= $right and end >= $left order by start asc, end desc";
	}
	else
	{
		$query = "select 0, 'read', '+', start, end, end-start+1, 0, '', '' from $table where (assembly='chr$assembly' or assembly='$assembly') and start <= $right and end >= $left order by start asc, end desc";
	}
	if (cache_exists($query)) cache_stream($query);
	
	$d = query($query);

	$result = array();
	$counter = 0;
	
	while ($r = mysql_fetch_row($d))
	{
		$id     = $counter++;
		$class  = $r[1];
		$strand = $r[2] == '+' ? 'watson' : 'crick';
		$start  = $r[3] + 0;
		$len    = ($r[4] + 0) - $start;
		$len1   = $r[5] + 0;
		$len2   = $r[6] + 0;
		$seq1   = $r[7];
		$seq2   = $r[8];
				
		if (!isset($result[$class]))
		{
			$result[$class] = array
			(
				'watson' => array(), 
				'crick'  => array()
			);
		}
		$result[$class][$strand][] = array($id, $start, $len, $len1, $len2, $seq1, $seq2);
	}
	
	//Create cache and stream to user
	cache_create($query, $result, true, $table);
}

error('Invalid action requested: ' . $action);

?>