<?PHP
require_once 'common_func.php';
require_once 'json.php';

//doSimplifyBox ($results)
function doSimplifyBox ($results)
{
	$result = array();
	foreach ($results as $class => $data)
	{
		$result[$class] = array();
		foreach ($data as $strand => $boxes)
		{
			$result[$class][$strand] = array();
			unset($obox);
			foreach ($boxes as $box)
			{
				if (isset($obox) && $box[1]==$obox[1] && $box[2]==$obox[2] && $box[5]==$obox[5])
				{
					$obox[4] += $box[4];
				}
				else
				{
					if (isset($obox)) $result[$class][$strand][]=$obox;
					$obox = $box; $obox[4] += 0;
				}
			}
			if (isset($obox)) $result[$class][$strand][]=$obox;
		}
	}
	return $result;
}

//doSimplifyClean ($result, $unit);
function doSimplifyClean ($results, $unit)
{
	$result = array();
	foreach ($results as $class => $data)
	{
		$clean = array(); $cleanW = array();

		foreach ($data as $datum)
		{
			$clean[] = $datum;
		}
		$n = count($clean);
		for($i=0; $i<$n; $i++)
		{
			$a = $clean[$i];
			for ($j=1; $j<$n-$i; $j++)
			{
				$b = $clean[$i+$j];
				if (!((round(($b[0]-$a[0]),1)==round($j*$unit,1)) && $b[1]==$a[1] && $b[2]==$a[2])) break;
			}
			if ($j>1) $c = array($a[0],$j*$unit,$a[1],$a[2]);
			else $c = $a;
			if ($a[1]!=0 || $a[2]!=0) $cleanW[] = $c;
			$i += $j-1;
		}

		$result[$class] = $cleanW;
	}
	return $result;
}

//doSimpleIntensityHistogramCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
function doSimpleIntensityHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	global $ibase;
	global $alg;
	if (!isset($ibase)) $ibase=0;
	$query = getQueryString($module, $dest, $assembly, $left, $right, $bases, $pixels);

	$result = array();
	$unit = $bases / $pixels;

	$class = 'read';
	//$result[$class] = array();

	$xfloor = floor($left * $pixels / $bases);
	$xceil = ceil($right * $pixels / $bases);

	foreach ($rows as $r)
	{
		//data input as: id, assembly, strand, start, end, class, copies, places
		//	  idx:  0,	1,      2,     3,   4,     5,      6,     7
		$strand = $r[2];
		$start  = $r[3] + $ibase;
		$end    = $r[4] + $ibase;
		if (!isset($r[6])) $copies = 1;
		else $copies = $r[6] + 0;
		if (!isset($r[7])) $places = 1;
		else $places = $r[7] + 0;
		if ('' != $r[5]) $class = $r[5];

		//Determine the range of x positions covered by the read
		$x1 = floor($start * $pixels / $bases);
		$x2 = $x1 + ceil(($end-$start+1) * $pixels / $bases) - 1;

		if ($x1<$xfloor) $x1=$xfloor;
		if ($x2>$xceil) $x2=$xceil;
		//For each of the x positions, convert to a genome position and add to the count
		for ($x=$x1; $x<=$x2; $x++)
		{
			$gpos = $x * $bases / $pixels;
			if (!isset($result[$class])) $result[$class] = array();
			if (!isset($result[$class]["$gpos"]))
				$result[$class]["$gpos"] = array($gpos,0,0);

			$amt = 0;
			if ($gpos < $start)
			{
				if ($gpos + $unit > $end) $amt = $end - $start;
				else $amt = $gpos + $unit - $start;
			}
			else if ($gpos + $unit > $end && $gpos < $end) $amt = $end - $gpos;
			else $amt = $unit;

			if ($alg=='Coverage') $amt = ceil($amt*$copies/$unit);
			else if ($alg=='Count') $amt = $copies;
			else $amt *= $copies;

			if ($strand == '+')
				$result[$class]["$gpos"][1] += $amt;
			else
				$result[$class]["$gpos"][2] += $amt;
		}
	}

	//Simplify the data stream
	$result = doSimplifyClean ($result, $unit);

	//Create cache
	simple_cache_create($query, $result, $dest, false);
	unset($result);
	return true;
}

//doSimpleReadsHistogramCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
function doSimpleReadsHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	global $ibase, $alg;
	if (!isset($ibase)) $ibase=0;

	$query = getQueryString($module, $dest, $assembly, $left, $right, $bases, $pixels);

	$result = array();
	$unit = round($bases / $pixels);
	if ($unit<1) return;

	$class = 'read';
	$result[$class] = array();

	$xfloor = floor($left * $pixels / $bases);
	$xceil = ceil($right * $pixels / $bases);
	
	foreach ($rows as $r)
	{
		//data input as: id, assembly, strand, start, end, sequence, copies, places
		//	  idx:  0,	1,      2,     3,     4,	5,      6,    7
		$strand = $r[2];
		$start  = $r[3] + $ibase;
		$end    = $r[4] + $ibase;
		if (!isset($r[6])) $copies = 1;
		else $copies = $r[6] + 0;
		if (!isset($r[7])) $places = 1;
		else $places = $r[7] + 0;

		//Determine the range of x positions covered by the read
		$x1 = floor($start * $pixels / $bases);
		$x2 = $x1 + ceil(($end-$start+1) * $pixels / $bases) - 1;

		if ($x1<$xfloor) $x1=$xfloor;
		if ($x2>$xceil) $x2=$xceil;
		//For each of the x positions, convert to a genome position and add to the count
		for ($x=$x1; $x<=$x2; $x++)
		{
			$gpos = $x * $bases / $pixels;

			if (!isset($result[$class]["$gpos"]))
				$result[$class]["$gpos"] = array($gpos,0,0);

			$amt = 0;
			if ($gpos < $start)
			{
				if ($gpos + $unit > $end) $amt = $end - $start;
				else $amt = $gpos + $unit - $start;
			}
			else if ($gpos + $unit > $end && $gpos < $end) $amt = $end - $gpos;
			else $amt = $unit;

			if ($alg=='Coverage') $amt = ceil($amt*$copies/$unit);
			else if ($alg=='Count') $amt = $copies;
			else $amt *= $copies;

			if ($strand == '+')
				$result[$class]["$gpos"][1] += $amt;
			else
				$result[$class]["$gpos"][2] += $amt;
		}
	}

	//Simplify the data stream
	$result = doSimplifyClean ($result, $unit);

	//Create cache
	simple_cache_create($query, $result, $dest, false);
	unset($result);
	return true;
}

//doSimpleReadsBoxes
function doSimpleReadsBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels, $seq)
{
	global $ibase;
	if (!isset($ibase)) $ibase=0;

	$query = getQueryString($module, $dest, $assembly, $left, $right, $bases, $pixels);

	$class = 'read';
	$result = array();
	$result[$class] = array ( 'watson' => array(), 'crick'  => array() );
	$idx = array ( '+' => 'watson', '-' => 'crick');

	//while ($r = mysqli_fetch_row($d))
	foreach ($rows as $r)
	{
		//data input as: id, assembly, strand, start, end, sequence, copies, places
		//	  idx:  0,	1,      2,     3,     4,	5,      6,    7
		if ($seq==false || !isset($r[5])) $r[5]='';
		else if ($r[2]=='-') $r[5]=strrev($r[5]);
		if (isset($r[6]) && $r[6]!='') $copies = $r[6];
		else $copies = 1; 

		$result[$class][$idx[$r[2]]][] = array($r[0], $r[3]+$ibase, $r[4]-$r[3]+1, 1, $copies, $r[5]);
	}

	//doSimplifyBox
	$result = doSimplifyBox($result);

	//Create cache and stream to user
	simple_cache_create($query, $result, $dest, false);
	unset( $result);
	return true;
}

//doSimpleSNPsBoxes
function doSimpleSNPsBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	global $ibase;
	if (!isset($ibase)) $ibase=0;

	$query = getQueryString($module, $dest, $assembly, $left, $right, $bases, $pixels);

	$unit = round($bases / $pixels);
	if ($unit<1) return;

	$class = 'SNP';
	$result = array();
	$idx = array ( '+' => 'watson', '-' => 'crick');

	foreach ($rows as $r)
        {
		//data input as: id, assembly, strand, start, end, class, sequence
		//        idx:  0,      1,      2,     3,     4,        5,      6
        	$class = $r[5]; $dir = $r[2]; $wc = $idx[$dir];
		if (!isset($r[6])) $seq = '';
		else $seq = $r[6];
		if (!isset($result[$class]))
			$result[$class] = array ('watson' => array(), 'crick' => array());
		$result[$class][$wc][] = array($r[0], $r[3]+$ibase, $r[4]-$r[3]+1, 1, 1, $seq); 	 
	}

	//doSimplifyBox
	//$result = doSimplifyBox($result);
	foreach ($result as $class => $data)
	{
		foreach ($data as $wc => $reads)
		{
			$no = count($reads); $in=0;
			if (!isset($result[$class][$wc][0])) continue;
			$fl = floor($result[$class][$wc][0][1]/$unit);
			for ($i=1; $i<$no; $i++)
			{
				$fln = floor($result[$class][$wc][$i][1]/$unit);
				if ($fl==$fln)
				{
					unset ($result[$class][$wc][$i]);
					$result[$class][$wc][$in][5] = '';
				}
				else $in = $i;
				$fl = $fln;
			}
		}
	}
		
	//Create cache and stream to user
	simple_cache_create($query, $result, $dest, false);
	unset( $result);
	return true;
}

//doSimpleSmallReadsHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
function doSimpleSmallReadsHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	return doSimpleReadsHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
}

//doSimpleSmallReadsBoxes
function doSimpleSmallReadsBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels, $seq)
{
	return doSimpleReadsBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels, $seq);
}

//doSimplePairedEndHistogram 
function doSimplePairedEndHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	global $ibase, $alg;
	if (!isset($ibase)) $ibase=0;

	$query = getQueryString($module, $dest, $assembly, $left, $right, $bases, $pixels);

	$result = array();
	$unit = round($bases / $pixels);
	if ($unit<1) return;

	$class = 'read';
	$result[$class] = array();

	//while ($r = mysqli_fetch_row($d))
	foreach ($rows as $r)
	{
		//data input as: id, assembly, strand, start, end, seqA, seqB, copies, places
		//	  idx:  0,	1,      2,     3,     4,    5,    6,      7,    8
		$strand = $r[2];
		$start  = $r[3] + $ibase;
		$end    = $r[4] + $ibase;
		$lenA   = strlen($r[5]);
		$lenB   = strlen($r[6]);

		if (!isset($r[7])) $copies = 1;
		else $copies = $r[7] + 0;
		if (!isset($r[8])) $places = 1;
		else $places = $r[8] + 0;

		//Determine the range of x positions covered by the read
		$a1 = floor($start * $pixels / $bases);
		$a2 = ceil(($start+$lenA) * $pixels / $bases);

		$b1 = floor(($end-$lenB) * $pixels / $bases);
		$b2 = ceil($end * $pixels / $bases);

		//For each of the x positions, convert to a genome position and add to the count
		for ($x=$a1; $x<=$b2; $x++)
		{
			//if ($x > $a2 && $x < $b1) continue;
			if ($x > $a2 && $x < $b1) $x = $b1;

			$gpos = $x * $bases / $pixels;

			if (!isset($result[$class]["$gpos"]))
			{
				$result[$class]["$gpos"] = array($gpos,0,0);
			}

			$amt = 0;

			if ($gpos < $start)
			{
				if ($gpos + $unit > $end) $amt = $end - $start;
				else $amt = $gpos + $unit - $start;
			}
			else if ($gpos + $unit > $end && $gpos < $end)
			{
				$amt = $end - $gpos;
			}
			else
			{
				$amt = $unit;
			}

			if ($alg=='Coverage') $amt = ceil($amt*$copies/$unit);
			else if ($alg=='Count') $amt = $copies;
			else $amt *= $copies;

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
	$result = doSimplifyClean ($result, $unit);

	//Create cache and stream to user
	simple_cache_create($query, $result, $dest, false);
	unset( $result);
	return true;
}

function doSimplePairedEndBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels, $seq)
{
	global $ibase;
	if (!isset($ibase)) $ibase=0;

	$query = getQueryString($module, $dest, $assembly, $left, $right, $bases, $pixels);

	$class = 'read';
	$result = array();
	$result[$class] = array ( 'watson' => array(), 'crick'  => array() );
	$idx = array ( '+' => 'watson', '-' => 'crick');


	//while ($r = mysqli_fetch_row($d))
	foreach ($rows as $r)
	{
		//data input as: id, assembly, strand, start, end, seqA, seqB, copies, places
		//	  idx:  0,	1,      2,     3,   4,    5,    6,      7,    8
		$lnA = strlen($r[5]); $lnB = strlen($r[6]);
		if ($seq==false) { $r[5]=''; $r[6]=''; }

		$result[$class][$idx[trim($r[2])]][] = array($r[0], $r[3]+$ibase, $r[4]-$r[3]+1, $lnA, $lnB, $r[5], $r[6]);
	}

	//Create cache and stream to user
	simple_cache_create($query, $result, $dest, false);
	unset($result);
	unset($stmt);
	return true;
}

//doSimpleMethCaching
function doSimpleMethCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	global $ibase;
	if (!isset($ibase)) $ibase=0;

	$query = getQueryString($module, $dest, $assembly, $left, $right, $bases, $pixels);

	$result = array();

	//while ($r = mysqli_fetch_row($d))
	foreach ($rows as $r)
	{
		//data input as: id, assembly, strand, start, end, class,  mc,   h
		//	  idx:  0,	1,      2,     3,   4,     5,   6,   7
		$watson = $r[2] == '+';
		$score  = round((100*$r[6]) / ($r[7]+0));

		$start = $r[3] + $ibase;
		$class = $r[5];
		$above = $watson ? $score : 0;
		$below = $watson ? 0 : $score;

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
			$result[$class][$gpos] = array($gpos+0,1,0,0);
		}

		//Update values if needed
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
	simple_cache_create($query, $result, $dest, false);
	unset( $result);
	return true;
}


//doSimpleModelsCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
function doSimpleModelsCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	global $ibase, $keepSmall;
	if (!isset($ibase)) $ibase=0;

	$query = getQueryString($module, $dest, $assembly, $left, $right, $bases, $pixels);

	$data = array();

	//while ($r = $stmt->fetch(PDO::FETCH_NUM))
	foreach ($rows as $r)
	{
		//data input as: id, assembly, strand, start, end, class,  parent
		//	  idx:  0,	1,      2,     3,   4,     5,      6
		$start = $r[3] + $ibase;
		$ln = $r[4]-$r[3]+1;
		$parent = (isset($r[6]))?$r[6]:'';

		//Skip if too small to be seen anyway if not kept
		if (!$keepSmall && $parent!='' && round($ln*$pixels/$bases)==0) continue;

		//parent, id, strand, class, start, length
		$data[]=array($parent, $r[0], $r[2], $r[5], $start, $ln);
	}
	simple_cache_create($query, $data, $dest, false);
	unset($data);
	return true;
}

//doSimpleMaskCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
function doSimpleMaskCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	doSimpleModelsCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
}

//doSimpleAlignsCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
function doSimpleAlignsCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	doSimpleModelsCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
}

//doSimpleIntensityCaching
function doSimpleIntensityCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	doSimpleIntensityHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
}

//doSimpleMicroarrayCaching
function doSimpleMicroarrayCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	doSimpleReadsHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
}

//doSimpleReadsCaching
function doSimpleReadsCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	if (1.0 * $bases / $pixels >= 5.0)
		doSimpleReadsHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
	else if (1.0 * $bases / $pixels >= 0.2)
		doSimpleReadsBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels, false);
	else doSimpleReadsBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels, true);
}

//doSimplePairedEndCaching
function doSimplePairedEndCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	if (1.0 * $bases / $pixels >= 5.0)
		doSimplePairedEndHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
	else if (1.0 * $bases / $pixels >= 0.2)
		doSimplePairedEndBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels, false);
	else doSimplePairedEndBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels, true);
}

//doSimpleSmallReadsCaching
function doSimpleSmallReadsCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	if (1.0 * $bases / $pixels >= 5.0)
		doSimpleSmallReadsHistogram ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
	else if (1.0 * $bases / $pixels >= 0.2)
		doSimpleSmallReadsBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels, false);
	else doSimpleSmallReadsBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels, true);
}

//doSimpleSNPsCaching
function doSimpleSNPsCaching ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels)
{
	doSimpleSNPsBoxes ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
}

//doSimpleCaches
function doSimpleCaches ($module, $dest, &$rows, $assembly, $left, $right, $bases, $pixels, $force=false)
{
	//print_r($rows);
	$query = getQueryString($module, $dest, $assembly, $left, $right, $bases, $pixels);
	if (!$force  && simple_cache_exists($query, $dest)) return;
	$doSimpleModuleCaches = "do".$module."Caching";
	$doSimpleModuleCaches ($module, $dest, $rows, $assembly, $left, $right, $bases, $pixels);
}

//getSimpleString
function getSimpleString ($module, $table, $assembly, $left, $right, $bases, $pixels)
{
	global $query_string;
	$ln = ($right-$left+1)/1000;
	switch ($ln)
	{
		case 1: 
			$st = $left/1000; $lc = "A"; break;
		case 10: 
			$st = $left/10000; $lc = "B"; break;
		case 100:
			$st = $left/100000; $lc = "C"; break;
		case 1000:
			$st = $left/1000000; $lc = "D"; break;
		case 10000:
			$st = $left/10000000; $lc = "E"; break;
		default:
			 $st = $left/1000; $lc = "Z"; break;
	}
	
	if ($module=='SimpleReads' || $module=='SimplePairedEnd' || $module=='SimpleSmallReads'
		|| $module=='ReadsTrack' || $module=='PairedEndTrack' || $module=='SmallReadsTrack')
	{
		if (1.0 * $bases / $pixels >= 5.0) $query = $assembly.":".$st.$lc."H";
		else if (1.0 * $bases / $pixels >= 0.2)  $query = $assembly.":".$st.$lc."X";
		else {
			if ($lc!="A") $query = $assembly.":".$st.$lc."X";
			else $query = $assembly.":".$st.$lc."Q";
		}
	}
	else $query = $assembly.":".$st.$lc."X";
	return $query;
}
	
//getQueryString
function getQueryString($module, $table, $assembly, $left, $right, $bases, $pixels)
{
	global $query_string;
	if (isset($query_string) && $query_string == "Simple")
		return getSimpleString ($module, $table, $assembly, $left, $right, $bases, $pixels);
	else return getMySQLString($module, $table, $assembly, $left, $right, $bases, $pixels);
}

//getMySQLString
function getMySQLString($module, $table, $assembly, $left, $right, $bases, $pixels)
{
	global $query_string;
	if (!isset($query_string) || $query_string != "MySQL0") $old = false;
	else $old = true;
	if (strstr($table, "/")!=NULL) $table = substr($table, strrpos($table, '/')+1);
	$idx = ''; $read = "";
	if (!$old) $idx = " force index (start)";

	if ($module=='SimpleModels' || $module=='SimpleMask' || $module=='SimpleAligns')
	{
		$query = "select parent, id, strand, class, start, end-start+1 from $table$idx where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";	
	}
	else if ($module=='SimpleReads')
	{
		if ($old) $read = " 'read',"; 
		if (1.0 * $bases / $pixels >= 5.0)
		{
			$query = "select$read start, end, strand, 1, 1 from $table$idx where assembly = '$assembly' and start <= $right and end >= $left";
		}
		else 
		{
			if (1.0 * $bases / $pixels >= 0.2) $seq = "''";
			else $seq = "sequence";
			$query = "select id,$read start, end, strand, $seq, 1, 1 from $table$idx where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";	
		}
	}
	else if ($module=='SimplePairedEnd')
	{
		if ($old) $read = " 'read',";
		if (1.0 * $bases / $pixels >= 5.0)
		{
			$query = "select$read strand, start, end, 1, 1, length(sequenceA), length(sequenceB) from $table$idx where assembly = '$assembly' and start <= $right and end >= $left";	
		}
		else
		{
			if (1.0 * $bases / $pixels >= 0.2) $seq = "'', ''"; 
			else $seq = "sequenceA, sequenceB";
			$query= "select id,$read strand, start, end, length(sequenceA), length(sequenceB), $seq from $table$idx where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";	
		}
	}
	else if ($module=='SimpleSmallReads')
	{
		if ($old) $read = " 'read',";
		if (1.0 * $bases / $pixels >= 5.0)
		{
			$query = "select$read start, end, strand, locations, copies from $table$idx where assembly = '$assembly' and start <= $right and end >= $left";
		}
		else
		{
			if (1.0 * $bases / $pixels >= 0.2) $seq = "''";
			else $seq = "sequence";
			$query = "select id,$read start, end, strand, $seq, copies, locations from $table$idx where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";
		}	
	}
	else if ($module=='SimpleMeth')
	{
		if (!$old) $idx = " force index (position)"; 
		$query = "select position, class, strand, mc, h from $table$idx where assembly = '$assembly' and position <= $right and position >= $left order by position asc";
	}
	else if ($module=='SimpleSNPs')
	{
		if (!$old) $idx = " force index (position)";
		$query = "select id, class, start, end, sequence, 1, 1, locations from $table$idx where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";
	}
	return $query;
}

//SimplePolicy
function getSimplePolicy ($module)
{
	if ($module=='SimpleReads' || $module=='SimplePairedEnd' || $module=='SimpleSmallReads') 
		return getPolicy ('ReadsTrack');
	if ($module=='SimpleModels') return getPolicy ('ModelsTrack');
	if ($module=='SimpleMask') return getPolicy ('MaskTrack');
	if ($module=='SimpleAligns') return getPolicy ('AlignsTrack');
	if ($module=='SimpleMeth') return getPolicy ('MethTrack');
	if ($module=='SimpleMicroarray') return getPolicy ('MicroarrayTrack');
	if ($module=='SimpleIntensity') return getPolicy ('IntensityTrack');
	if ($module=='SimpleSNPs') return getPolicy ('SNPsTrack');

	echo "\tError : ".$module." is not included.".PHP_EOL;
	exit();
}	

//ModelsTrack
function getSimpleModelsTrackQuery ($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = getQueryString('SimpleModels', $table, $assembly, $left, $right, $bases, $pixels);
	simple_cache_stream($query, $table);
}

//MaskTrack
function getSimpleMaskTrackQuery ($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = getQueryString('SimpleMask', $table, $assembly, $left, $right, $bases, $pixels);
	simple_cache_stream($query, $table);
}

//AlignsTrack
function getSimpleAlignsTrackQuery ($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = getQueryString('SimpleAligns', $table, $assembly, $left, $right, $bases, $pixels);
	simple_cache_stream($query, $table);
}

//IntensityTrack
function getSimpleIntensityTrackQuery ($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = getQueryString('SimpleIntensity', $table, $assembly, $left, $right, $bases, $pixels);
	simple_cache_stream($query, $table);
}

//MicroarrayTrack
function getSimpleMicroarrayTrackQuery ($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = getQueryString('SimpleMicroarray', $table, $assembly, $left, $right, $bases, $pixels);
	simple_cache_stream($query, $table);
}

//ReadsTrack
function getSimpleReadsTrackQuery ($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = getQueryString('SimpleReads', $table, $assembly, $left, $right, $bases, $pixels);
	simple_cache_stream($query, $table);
}

//PairedEndTrack
function getSimplePairedEndTrackQuery ($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = getQueryString('SimplePairedEnd', $table, $assembly, $left, $right, $bases, $pixels);
	simple_cache_stream($query, $table);
}

//SNPsTrack
function getSimpleSNPsTrackQuery ($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = getQueryString('SimpleSNPs', $table, $assembly, $left, $right, $bases, $pixels);
	simple_cache_stream($query, $table);
}

//MethTrack
function getSimpleMethTrackQuery ($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = getQueryString('SimpleMeth', $table, $assembly, $left, $right, $bases, $pixels);
	simple_cache_stream($query, $table);
}

//SmallReadsTrack
function getSimpleSmallReadsTrackQuery ($table, $assembly, $left, $right, $bases, $pixels)
{
	$query = getQueryString('SimpleSmallReads', $table, $assembly, $left, $right, $bases, $pixels);
	simple_cache_stream($query, $table);
}

//Get track policy 
function getPolicy ($module)
{
	$p_1_100 = array('bases' => 1, 'pixels' => 100, 'cache' => 1000, 'run' => 0);
	$p_1_10 = array('bases' => 1, 'pixels' => 10, 'cache' => 1000, 'run' => 0);
	$p_1_1 = array('bases' => 1, 'pixels' => 1, 'cache' => 10000, 'run' => 0);
	$p_10_1 = array('bases' => 10, 'pixels' => 1, 'cache' => 100000, 'run' => 0);
	$p_100_1 = array('bases' => 100, 'pixels' => 1, 'cache' => 1000000, 'run' => 0);
	$p_1000_1 = array('bases' => 1000, 'pixels' => 1, 'cache' => 10000000, 'run' => 0);
	$p_10000_1 = array('bases' => 10000, 'pixels' => 1, 'cache' => 100000000, 'run' => 0);

	if ($module==='ReadsTrack' || $module==='PairedEndTrack' || $module==='SmallReadsTrack' || $module==='HiCTrack')
		$policy = array($p_1_100, $p_1_1, $p_10_1, $p_100_1, $p_1000_1, $p_10000_1);
	else if ($module==='IntensityTrack')
		$policy = array($p_1_1, $p_10_1, $p_100_1, $p_1000_1, $p_10000_1);
	else if ($module==='ModelsTrack')
		$policy = array($p_1_1, $p_10_1, $p_100_1, $p_1000_1);
	else if ($module==='MaskTrack' || $module==='AlignsTrack' || $module==='SNPsTrack')
		$policy = array($p_1_1, $p_10_1, $p_100_1, $p_1000_1);
	else if ($module==='MicroarrayTrack' || $module==='MethTrack')
		$policy = array($p_1_1, $p_10_1, $p_100_1, $p_1000_1, $p_10000_1);
	else return false;

	return $policy;
}
 
?>
