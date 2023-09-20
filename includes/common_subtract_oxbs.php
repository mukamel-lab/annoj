<?php

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
		$table_BS .= $assembly;
		$table_OxBS .= $assembly;
	}
	// $query = "select position as start, position + 1 as end, class, if(strand='+',round(100*mc/h),0) as score_above, if (strand='+',0,round(100*mc/h)) as score_below from $table where assembly = '$assembly' and position between $left and $right order by start asc";	
	$query = "SELECT tBS.position AS start, tBS.position+1 as end, tBS.class as class,
  if (tBS.strand='+',max(round(100*( cast(tBS.mc/tBS.h AS DEC(5,5)) - cast(tOxBS.mc/tOxBS.h AS DEC(5,5))))),0) as score_above, 
  if (tBS.strand='+',0,max(round(100*( cast(tBS.mc/tBS.h AS DEC(5,5)) - cast(tOxBS.mc/tOxBS.h AS DEC(5,5)))))) as score_below,
    tBS.strand as strand1
  FROM $table_BS AS tBS
  INNER JOIN $table_OxBS AS tOxBS 
  ON (tBS.position = tOxBS.position AND tBS.strand=tOxBS.strand)
  WHERE tBS.position between $left and $right 
  GROUP BY start, class, strand1
  order by start asc";	
	
	if (cache_exists($query)) cache_stream($query);
	
	//Populate the returning array
	$result = array();
	$d = query($query);

	while ($r = mysql_fetch_row($d))
	{
		$start = $r[0] + 0;
		$end   = $r[1] + 0;
		$class = $r[2];
		$above = max($r[3] + 0,0);
		$below = max($r[4] + 0,0);
		
		//If the class is new then create a series to represent it
		if (!isset($result[$class]))
		{
			$result[$class] = array();
		}
		
		//Do nothing if there is no data to store
		if ($above == 0 && $below == 0) continue;
					
		//Determine the rounded gpos of the feature
		$gpos = "" . round($start * $pixels / $bases) * $bases / $pixels;
		
		//Add an entry in the data series if necessary
		if (!isset($result[$class][$gpos]))
		{
			$result[$class][$gpos] = array($gpos+0,0,0,0);
		}	
		
		//Update values if needed
		$result[$class][$gpos][1] = max($result[$class][$gpos][1], $end-$start);
		$result[$class][$gpos][2] = max($result[$class][$gpos][2], $above);
		$result[$class][$gpos][3] = max($result[$class][$gpos][3], $below);
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
	cache_create($query,$result,true,$table_BS);
}
error('Invalid action requested: ' . $action);

?>
