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
	$smoothing = 100;
	if ($append_assembly)
	{
		$table .= $assembly;
	}
	$query = "select position as start, position + 1 as end, class, h as coverage, mc, strand from $table where assembly = '$assembly' and position between $left and $right order by start asc";
		//$query = "select start, end, class, score_above, score_below from $table where assembly = '$assembly' and start <= $right and end >= $left order by start asc";

	if (cache_exists($query)) cache_stream($query);

	//Populate the returning array
	$result = array();
	$d = query($query);

	while ($r = mysql_fetch_row($d))
	{
		$start = $r[0] + 0;
		$end   = $r[1] + 0;
		$class = $r[2];
		$h = $r[3] + 0;
		$mc = $r[4] + 0;
		$strand = $r[5];

		//If the class is new then create a series to represent it
		if (!isset($result[$class]))
		{
			$result[$class] = array();
		}

		//Determine the rounded gpos of the feature
		$gpos = "" . round($start * $pixels / $bases) * $bases / $pixels;

		//Add an entry in the data series if necessary
		if (!isset($result[$class][$gpos]))
		{
			$result[$class][$gpos] = array($gpos+0,0,0,0,0,0);
		}

		//Update values if needed
		$result[$class][$gpos][1] = max($result[$class][$gpos][1], $end-$start);
		if ($strand=='+')
		{
			$result[$class][$gpos][2] += $mc;
			$result[$class][$gpos][3] += $h;
		} else
		{
			$result[$class][$gpos][4] += $mc;
			$result[$class][$gpos][5] += $h;
		}
	}

	//Simplify the data stream
	foreach ($result as $class => $data)
	{
		$clean = array();

		foreach ($data as $datum)
		{
			$mc = array(0,0);
			$h = array(0,0);
			if ( isset($smoothing) && $smoothing > 0 ) {
				foreach ($data as $datum2) {
					if (abs($datum2[0]-$datum[0])<=$smoothing)
					{
						$mc[0]+=$datum2[2];
						$h[0]+=$datum2[3];
						$mc[1]+=$datum2[4];
						$h[1]+=$datum2[5];
					}
				}
			}
			else
			{
				$mc[0]=$datum[2];
				$h[0]=$datum[3];
				$mc[1]=$datum[4];
				$h[1]=$datum[5];
			}

			$max_h=max($h); // Max over the two strands
			$max_mc=max($mc);
			if (!(($max_mc==0) || ($max_h<2) || ($max_h<9 && $max_mc<2) || ($max_h<34 && $max_mc<3) || ($max_h>=34 && $max_mc<4)))
			{
				$datum_out = array($datum[0],$datum[1],0,0);
				$datum_out[2] = 100*$mc[0]/max($h[0],1);
				$datum_out[3] = 100*$mc[1]/max($h[1],1);
				$clean[] = $datum_out;
			}
		}

		$result[$class] = $clean;
	}


	// //Simplify the data stream
	// foreach ($result as $class => $data)
	// {
	// 	$clean = array();
	//
	// 	foreach ($data as $datum)
	// 	{
	// 		$clean[] = $datum;
	// 	}
	// 	$result[$class] = $clean;
	// }

	//Create cache and stream to user
	cache_create($query,$result,true,$table);
}
error('Invalid action requested: ' . $action);

?>
