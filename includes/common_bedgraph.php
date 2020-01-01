<?php

// Eran Mukamel
// Use a mysql table stored in wiggle format

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
	get_histogram($table, $assembly, $left, $right, $bases, $pixels);
}


//Get read depth as histogram
function get_histogram($table, $assembly, $left, $right, $bases, $pixels)
{

	// if ($table[0] == '+')
	// {
	// 	$table = substr($table,1);
	// };
	$query = "select start, end, counts  from $table where (assembly='chr$assembly' or assembly='$assembly') and (start <= $right) and (end >= $left)";
	
	// bases=100, pixels=1
	if (cache_exists($query)) cache_stream($query);

	$result = array();
	$unit = round($bases / $pixels);
	$d = query($query);
	$strand = '+';
	
	while ($r = mysql_fetch_row($d))
	{
		$start  = $r[0]+0;
		$end  = $r[1]+0;
		$count = $r[2]+0;
		$class = 'count';

		$lenA = $end-$start+1;
		$lenB = 0;

		//If the class is new then create a series to represent it
		if (!isset($result[$class]))
		{
			$result[$class] = array();
		}

		//Determine the range of x positions covered by the read
		$a1 = floor($start * $pixels / $bases);
		$b2 = ceil($end * $pixels / $bases);
		
		//For each of the x positions, convert to a genome position and add to the count
		for ($x=$a1; $x<=$b2; $x++)
		{
			// if ($x > $a2 && $x < $b1) continue;
			
			$gpos = $x * $bases / $pixels;
		
			if (!isset($result[$class]["$gpos"]))
			{
				$result[$class]["$gpos"] = array($gpos,0,0);
			}
			
			// $amt = 0;
			
			// if ($gpos < $start)
			// {
			// 	$amt = $gpos + $unit - $start;
			// }
			// else if ($gpos + $unit > $end && $gpos < $end)
			// {
			// 	$amt = $end - $gpos;
			// }
			// else
			// {
			// 	$amt = $unit;
			// }
			
			$amt = $count;
			// $amt *= $count;
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

error('Invalid action requested: ' . $action);

?>
