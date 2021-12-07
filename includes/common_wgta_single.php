<?php

#
# Display both mc level from both strands (combined for each CpG site). Also show coverage.
#
# Modified by Eran Mukamel, June 2018

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
	$query = "SELECT if(strand='+',position+1,position) as start, if(strand='+',position+1,position)+1 as end, 
		class, 10*sum(h) as coverage, sum(mc) as mcsum, sum(h) as hsum FROM $table 
		WHERE assembly = '$assembly' AND position BETWEEN $left and $right GROUP BY class,start,end ORDER BY start ASC";	

	if (cache_exists($query)) cache_stream($query);
	
	//Populate the returning array
	$result = array();
	$counts = array();
	$d = query($query);

	$COV = "COV";
	while ($r = mysql_fetch_row($d))
	{
		$start = $r[0] + 0;
		$end   = $r[1] + 0;
		$class = $r[2];
		$coverage = $r[3] + 0;
		$mc = $r[4]+0;
		$h = $r[5]+0;

		//If the class is new then create a series to represent it
		if (!isset($result[$class]))
		{
			$result[$class] = array();
			$result[$COV] = array();
		}
		if (!isset($counts[$class]))
		{
			$counts[$class] = array();
		}
					
		//Determine the rounded gpos of the feature
		// $gpos = "" . round($start * $pixels / $bases) * $bases / $pixels;
		$pixelsu = 1;
		$gpos = "" . round($start * $pixelsu / $bases) * $bases / $pixelsu;
		
		//Add an entry in the data series if necessary
		if (!isset($result[$class][$gpos]))
		{
			$result[$class][$gpos] = array($gpos+0,0,0,0);
		}
		if (!isset($result[$COV][$gpos]))
		{
			$result[$COV][$gpos] = array($gpos+1,0,0,0);
		}
		if (!isset($counts[$class][$gpos]))
		{
			$counts[$class][$gpos] = array(0,0);
		}
		
		//Update values if needed
		$counts[$class][$gpos][0] += $mc;
		$counts[$class][$gpos][1] += $h;
		$result[$class][$gpos][1] = max($result[$class][$gpos][1], $end-$start);
		$result[$class][$gpos][2] = 100 * $counts[$class][$gpos][0]/$counts[$class][$gpos][1];
		$result[$COV][$gpos][1] = max($result[$COV][$gpos][1], $end-$start);
		$result[$COV][$gpos][2] = 10*$counts[$class][$gpos][1];
		if ($class == 'CGdmr') {
			$result[$class][$gpos][1] = max($result[$COV][$gpos][1], $end-$start);
			$result[$class][$gpos][2] = 10 * $counts[$class][$gpos][1];
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
	cache_create($query,$result,true,$table);
}
error('Invalid action requested: ' . $action);

?>
