<?PHP
require_once 'common_func.php';
require_once 'json.php';

//ModelsTrack
function getModelsTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels,  $auto=true)
{
	global $ibase;

	$query = "select parent, id, strand, class, start, end-start+1 from $table force index (start) where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";
	//$query = "select parent, id, strand, class, start, end-start+1 from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";

	if (cache_exists($query, $table))
	{
		if ($auto) cache_stream($query, $table);
		return false;
	}

	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$max = max_range($pdo, $table);
	$lefts = $left - $max; if ($lefts<0) $lefts=0;
	$qstmt = "select parent, id, strand, class, start, end-start+1 from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";

	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	$data = array();

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		$r[4] += $ibase;
		$r[5] += 0;
		
		//Skip if too small to be seen anyway
		if ($r[0] && round($r[5] * $pixels / $bases) == 0) continue;
		$data[] = $r;
	}
	cache_create($query, $data, $auto, $table);
	unset($data);
	return true;
}


//CustomModelsTrack
function getCustomModelsTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels,  $auto=true, $options)
{
        global $ibase;

        $query = "select parent, id, strand, class, start, end-start+1 from $table force index (start) where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";


        if (cache_exists($query, $table))
        {
                if ($auto) cache_stream($query, $table);
                return false;
        }

        if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
        $max = max_range($pdo, $table);
        $lefts = $left - $max; if ($lefts<0) $lefts=0;

	if ($options=='')
	{
        	$qstmt = "select parent, id, strand, class, start, end-start+1 from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";
	}
	else
	{
		//options options="parent:,id:,class:read;strand:+";
                //default options="" or "parent:parent,id:id,strand:strand,class:class";
		$dp = explode(',', $options);
		$opts = array();
		foreach($dp as $pa)
		{
			$dpa = explode(':',$pa);
			if (trim($dpa[0])!='') $opts[trim($dpa[0])]=trim($dpa[1]);
		}
		$qstmt = "select ";
		if (isset($opts['parent'])) $qstmt .= "null as ";
		$qstmt .= "parent, ";
		if (isset($opts['id'])) $qstmt .= "null as ";
		$qstmt .= "id, ";
		if (isset($opts['strand'])) $qstmt .= "'".$opts['strand']."' as ";
		$qstmt .= "strand, ";
		if (isset($opts['class'])) $qstmt .= "'".$opts['class']."' as ";
		$qstmt .= "class, start, end-start+1 from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";
	}

        $stmt = $pdo->prepare($qstmt);
        $stmt->execute();

        $data = array();

        //while ($r = mysqli_fetch_row($d))
        while ($r = $stmt->fetch(PDO::FETCH_NUM))
        {
                $r[4] += $ibase;
                $r[5] += 0;
		if ($r[1]==null || $r[1]=='') $r[1]=$assembly."_".$r[4];

                //Skip if too small to be seen anyway
                if ($r[0] && round($r[5] * $pixels / $bases) == 0) continue;
                $data[] = $r;
        }
        cache_create($query, $data, $auto, $table);
        unset($data);
        return true;
}


//MaskTrack
function getMaskTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels,  $auto=true)
{
	return getModelsTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels,  $auto);
}

//AlignsTrack
function getAlignsTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels,  $auto=true)
{
	return getModelsTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels,  $auto);
}

//ReadsTrack
function getReadsTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto=true)
{
	if (1.0 * $bases / $pixels >= 5.0)
	{
		getReadsHistogram($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto);
	}
	else if (1.0 * $bases / $pixels >= 0.2)
	{
		getReadsBoxes($pdo, $table, $assembly, $left, $right, false, $auto);
	}
	else
	{
		getReadsBoxes($pdo, $table, $assembly, $left, $right, true, $auto);
	}
}

//Get read depth as histogram
function getReadsHistogram($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto)
{
	global $ibase;
	$query = "select start, end, strand, 1, 1 from $table force index (start) where assembly = '$assembly' and start <= $right and end >= $left";
	//$query = "select start, end, strand, 1, 1 from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and end >= $left";
	//$query = "select 'read', start, end, strand, 1, 1 from $table where assembly = '$assembly' and start <= $right and end >= $left";

	if (cache_exists($query, $table)) 
	{
		if($auto) cache_stream($query, $table);
		else return false;
	}

	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$max = max_range($pdo, $table);
	$lefts = $left - $max; if ($lefts<0) $lefts=0;
	$qstmt =  "select start, end, strand, 1, 1 from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and end >= $left";

	$result = array();
	$unit = round($bases / $pixels);

	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	$class = 'read';
	$result[$class] = array();

	$xfloor = floor($left * $pixels / $bases);
	$xceil = ceil($right * $pixels / $bases);

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		//$class  = $r[0];
		$start  = $r[0] + $ibase;
		$end    = $r[1] + $ibase;
		$strand = $r[2];
		$count  = $r[3] + 0;
		$copies = $r[4] + 0;

		//Determine the range of x positions covered by the read
		$x1 = floor($start * $pixels / $bases);
		$x2 = ceil($end * $pixels / $bases);

		if ($x1<$xfloor) $x1=$xfloor;
		if ($x2>$xceil) $x2=$xceil;
		//For each of the x positions, convert to a genome position and add to the count
		for ($x=$x1; $x<=$x2; $x++)
		{
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
	cache_create($query, $result, $auto, $table);
	unset($result);
	return true;
}

//Get reads as boxes (flag for including sequence information)
function getReadsBoxes($pdo, $table, $assembly, $left, $right, $seq, $auto)
{
	global $ibase;
	$seq = $seq ? "sequence" : "''";

	$query = "select id, start, end, strand, $seq, 1, 1 from $table force index (start) where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";
	//$query = "select id, start, end, strand, $seq, 1, 1 from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";

	if (cache_exists($query, $table))
	{
		if ($auto) cache_stream($query, $table);
		else return false;
	}

	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$max = max_range($pdo, $table);
	$lefts = $left - $max; if ($lefts<0) $lefts=0;
	$qstmt = "select id, start, end, strand, $seq, 1, 1 from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";

	$class = 'read';
	$result = array();
	$result[$class] = array ( 'watson' => array(), 'crick'  => array() );
	$idx = array ( '+' => 'watson', '-' => 'crick');

	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		if ($r[3]=='-') $r[4]=strrev($r[4]);
		$result[$class][$idx[$r[3]]][] = array($r[0], $r[1]+$ibase, $r[2]-$r[1], $r[6]+0, $r[5]+0, $r[4]);
	}

	//Create cache and stream to user
	cache_create($query, $result, $auto, $table);
	unset( $result);
	return true;
}


//PairedEndTrack
function getPairedEndTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto=true )
{
	if (1.0 * $bases / $pixels >= 5.0)
	{
		getPairedEndHistogram($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto);
	}
	else if (1.0 * $bases / $pixels >= 0.2)
	{
		getPairedEndBoxes($pdo, $table, $assembly, $left, $right, false, $auto);
	}
	else
	{
		getPairedEndBoxes($pdo, $table, $assembly, $left, $right, true, $auto);
	}
}

//Get read depth as histogram
function getPairedEndHistogram($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto)
{
	global $ibase;
	$query = "select strand, start, end, 1, 1, length(sequenceA), length(sequenceB) from $table force index (start) where assembly = '$assembly' and start <= $right and end >= $left";
	//$query = "select 'read', strand, start, end, 1, 1, length(sequenceA), length(sequenceB) from $table where assembly = '$assembly' and start <= $right and end >= $left";

	if (cache_exists($query, $table)) 
	{
		if ($auto) cache_stream($query, $table);
		return false;
	}

	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$max = max_range($pdo, $table);
	$lefts = $left - $max; if ($lefts<0) $lefts=0;
	$qstmt = "select strand, start, end, 1, 1, length(sequenceA), length(sequenceB) from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and end >= $left"; 
	$result = array();
	$unit = round($bases / $pixels);

	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	$class = 'read';
	$result[$class] = array();

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		//$class  = $r[0];
		$strand = $r[0];
		$start  = $r[1] + $ibase;
		$end    = $r[2] + $ibase;
		$count  = $r[3] + 0;
		$copies = $r[4] + 0;
		$lenA   = $r[5] + 0;
		$lenB   = $r[6] + 0;

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
	cache_create($query, $result, $auto, $table);
	unset( $result);
	return true;
}

//Get reads as boxes without sequence information
function getPairedEndBoxes($pdo, $table, $assembly, $left, $right, $seq, $auto)
{
	global $ibase;
	if ($seq) $query= "select id, strand, start, end, length(sequenceA), length(sequenceB), sequenceA, sequenceB from $table force index (start) where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";
	else $query = "select id, strand, start, end, length(sequenceA), length(sequenceB), '', '' from $table force index (start) where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";
	//if ($seq) $query= "select id, strand, start, end, length(sequenceA), length(sequenceB), sequenceA, sequenceB from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";
	//else $query = "select id, strand, start, end, length(sequenceA), length(sequenceB), '', '' from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";

	if (cache_exists($query, $table))
	{
		if ($auto)  cache_stream($query, $table);
		return false;
	}
	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$max = max_range($pdo, $table);
	$lefts = $left - $max; if ($lefts<0) $lefts=0;
	if ($seq) $qstmt = "select id, strand, start, end, length(sequenceA), length(sequenceB), sequenceA, sequenceB from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";
	else $qstmt = "select id, strand, start, end, length(sequenceA), length(sequenceB), '', '' from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";


	$class = 'read';
	$result = array();
	$result[$class] = array ( 'watson' => array(), 'crick'  => array() );
	$idx = array ( '+' => 'watson', '-' => 'crick');

	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		$result[$class][$idx[$r[1]]][] = array($r[0], $r[2]+$ibase, $r[3]-$r[2], $r[4]+0, $r[5]+0, $r[6], $r[7]);
	}

	//Create cache and stream to user
	cache_create($query, $result, $auto, $table);
	unset($result);
	unset($stmt);
	return true;
}


//MethTrack
function getMethTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto=true)
{
	global $ibase;
	$query = "select position, class, strand, mc, h from $table force index (position) where assembly = '$assembly' and position <= $right and position >= $left order by position asc";

	if (cache_exists($query, $table)) 
	{
		if ($auto) cache_stream($query, $table);
		else return false;
	}

	//Populate the returning array
	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$result = array();
	$stmt = $pdo->prepare($query);
	$stmt->execute();

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		$watson = $r[2] == '+';
		$score  = round((100*$r[3]) / ($r[4]+0));

		$start = $r[0] + $ibase;
		$class = $r[1];
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
	cache_create($query, $result, $auto, $table);
	unset( $result);
	return true;
}


//SmallReadsTrack
function getSmallReadsTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto=true)
{
	if (1.0 * $bases / $pixels >= 5.0)
	{
		getSmallReadsHistogram($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto);
	}
	else if (1.0 * $bases / $pixels >= 0.2)
	{
		getSmallReadsBoxes($pdo, $table, $assembly, $left, $right, false, $auto);
	}
	else
	{
		getSmallReadsBoxes($pdo, $table, $assembly, $left, $right, true, $auto);
	}
}

//Get read depth as histogram
function getSmallReadsHistogram($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto)
{
	global $ibase;
	$query = "select start, end, strand, locations, copies from $table force index (start) where assembly = '$assembly' and start <= $right and end >= $left";
	//$query = "select start, end, strand, locations, copies from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and end >= $left";
	//$query = "select 'read', start, end, strand, locations, copies from $table where assembly = '$assembly' and start <= $right and end >= $left";

	if (cache_exists($query, $table)) 
	{
		if ($auto) cache_stream($query, $table);
		else return false;
	}
	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$max = max_range($pdo, $table);
	$lefts = $left - $max; if ($lefts<0) $lefts=0;
	$qstmt = "select start, end, strand, locations, copies from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and end >= $left";

	$class = 'read';
	$result = array();
	$result[$class] = array ();

	$unit = round($bases / $pixels);
	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		//$class  = $r[0];
		$start  = $r[0] + $ibase;
		$end    = $r[1] + $ibase;
		$strand = $r[2];
		$count  = $r[3] + 0;
		$copies = $r[4] + 0;

		//Determine the range of x positions covered by the read
		$x1 = floor($start * $pixels / $bases);
		$x2 = ceil($end * $pixels / $bases);

		//For each of the x positions, convert to a genome position and add to the count
		for ($x=$x1; $x<=$x2; $x++)
		{
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
	cache_create($query, $result, $auto, $table);
	unset( $result);
	return true;
}

//Get reads as boxes (flag for including sequence information)
function getSmallReadsBoxes($pdo, $table, $assembly, $left, $right, $seq, $auto)
{
	global $ibase;
	$seq = $seq ? "sequence" : "''";
	$query = "select id, start, end, strand, $seq, copies, locations from $table force index (start) where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";
	//$query = "select id, start, end, strand, $seq, copies, locations from $table force index (start) where assembly='$assembly' and start <= $right and start > lefts and end >= $left order by start asc, end desc";

	if (cache_exists($query, $table)) 
	{
		if ($auto) cache_stream($query, $table);
		return false;
	}
	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$max = max_range($pdo, $table);
	$lefts = $left - $max; if ($lefts<0) $lefts=0; 
	$qstmt = "select id, start, end, strand, $seq, copies, locations from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";

	$class = 'read';
	$result = array();
	$result[$class] = array ( 'watson' => array(), 'crick'  => array() );
	$idx = array ( '+' => 'watson', '-' => 'crick');

	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		if ($r[3]=='-') $r[4]=strrev($r[4]);
		$result[$class][$idx[$r[3]]][] = array($r[0], $r[1]+$ibase, $r[2]-$r[1], $r[6]+0, $r[5]+0, $r[4]);
	}

	//Create cache and stream to user
	cache_create($query, $result, $auto, $table);
	unset( $result);
	return true;
}

//indel
function getIndelQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto=true)
{
	global $ibase;
	$query = "select position, class, counts from $table force index (position) where assembly = '$assembly' and position <= $right and position >= $left order by position asc";

	if (cache_exists($query, $table)) 
	{
		if ($auto) cache_stream($query, $table);
		else return false;
	}

	//Populate the returning array
	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$result = array();
	$stmt = $pdo->prepare($query);
	$stmt->execute();

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		$watson = $r[1] == 'SNP';
		$score  = $r[2];

		$start = $r[0] + $ibase;
		$class = $r[1];
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
	cache_create($query, $result, $auto, $table);
	unset( $result);
	return true;
}


//SNP
function getSNPQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto=true)
{
	global $ibase;
	$query = "select start, substring(sequence,1,1) from $table force index (start) where assembly = '$assembly' and start <= $right and end >= $left";
	//$query = "select start, substring(sequence,1,1) from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and and end >= $left";

	if (cache_exists($query, $table)) 
	{
		if ($auto) cache_stream($query, $table);
		return false;
	}
	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$max = max_range($pdo, $table);
	$lefts = $left - $max; if ($lefts<0) $lefts=0;
	$qstmt = "select start, substring(sequence,1,1) from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and end >= $left";

	//Populate the returning array
	$result = array();

	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		$start = $r[0] + $ibase;
		$class = $r[1];

		if ($class == '.') continue;

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
			$result[$class][$gpos] = array($gpos+0,0,0);
		}

		//Update the count
		$result[$class][$gpos][1]++;
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
	cache_create($query, $result, $auto, $table);
	unset( $result);
	return true;
}


//CustomReadsTrack
function getCustomReadsTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto=true, $options='')
{
	if (1.0 * $bases / $pixels >= 5.0)
	{
		getCustomReadsHistogram($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto, $options);
	}
	else if (1.0 * $bases / $pixels >= 0.2)
	{
		getCustomReadsBoxes($pdo, $table, $assembly, $left, $right, false, $auto, $options);
	}
	else
	{
		getCustomReadsBoxes($pdo, $table, $assembly, $left, $right, true, $auto, $options);
	}
}

//Get read depth as histogram
function getCustomReadsHistogram($pdo, $table, $assembly, $left, $right, $bases, $pixels, $auto, $options)
{
	global $ibase;
	$query = "select start, end, strand, 1, 1 from $table force index (start) where assembly = '$assembly' and start <= $right and end >= $left";
	//$query = "select start, end, strand, 1, 1 from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and end >= $left";
	//$query = "select 'read', start, end, strand, 1, 1 from $table where assembly = '$assembly' and start <= $right and end >= $left";

	if (cache_exists($query, $table)) 
	{
		if($auto) cache_stream($query, $table);
		else return false;
	}

	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$max = max_range($pdo, $table);
	$lefts = $left - $max; if ($lefts<0) $lefts=0;
	$class = 'read';
	if ($options=='')
	{
		$qstmt =  "select start, end, strand, 1, 1 from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and end >= $left";
	}
	else
	{
		$dp = explode(',', $options);
                $opts = array();
                foreach($dp as $pa)
                {
                        $dpa = explode(':',$pa);
                        if (trim($dpa[0])!='') $opts[trim($dpa[0])]=trim($dpa[1]);
                }
		//options options="class:read;strand:+;count:places;copies:copies;";
		//default options="class:read;strand:strand;count:1;copies:1;";
		if (isset($opts['class']) && $opts['class']!='') $class=$opts['class'];
		$qstmt =  "select start, end, ";
		if (isset($opts['strand']) && $opts['strand']!='strand') $qstmt .= "'".$opts['strand']."' as ";
		$qstmt .= "strand, ";
		if (isset($opts['count']) && $opts['count']!='1') $qstmt .= $opts['count'].", ";
		else $qstmt .= "1, ";
		if (isset($opts['copies']) && $opts['copies']!='1') $qstmt .= $opts['copies']." ";
		else $qstmt .= "1 ";  
		$qstmt .= "from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and end >= $left";
	}

	$result = array();
	$unit = round($bases / $pixels);

	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	//$class = 'read';
	$result[$class] = array();
	$xfloor = floor($left * $pixels / $bases);
        $xceil = ceil($right * $pixels / $bases);

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		//$class  = $r[0];
		$start  = $r[0] + $ibase;
		$end    = $r[1] + $ibase;
		$strand = $r[2];
		$count  = $r[3] + 0;
		$copies = $r[4] + 0;

		//Determine the range of x positions covered by the read
		$x1 = floor($start * $pixels / $bases);
		$x2 = ceil($end * $pixels / $bases);

		if ($x1<$xfloor) $x1=$xfloor;
		if ($x2>$xceil) $x2=$xceil;
		//For each of the x positions, convert to a genome position and add to the count
		for ($x=$x1; $x<=$x2; $x++)
		{
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
	cache_create($query, $result, $auto, $table);
	unset($result);
	return true;
}

//Get reads as boxes (flag for including sequence information)
function getCustomReadsBoxes($pdo, $table, $assembly, $left, $right, $seq, $auto, $options)
{
	global $ibase;
	$seq = $seq ? "sequence" : "''";

	$query = "select id, start, end, strand, $seq, 1, 1 from $table force index (start) where assembly='$assembly' and start <= $right and end >= $left order by start asc, end desc";
	//$query = "select id, start, end, strand, $seq, 1, 1 from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";

	if (cache_exists($query, $table))
	{
		if ($auto) cache_stream($query, $table);
		else return false;
	}

	if (!isset($pdo) || !$pdo) include_once 'common_PDO_die.php';
	$max = max_range($pdo, $table);
	$lefts = $left - $max; if ($lefts<0) $lefts=0;
	$class = 'read';

	if ($options=='')
	{
		$qstmt = "select id, start, end, strand, $seq, 1, 1 from $table force index (start) where assembly='$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";
        }
	else
        {
                $dp = explode(',', $options);
                $opts = array();
                foreach($dp as $pa)
                {
                        $dpa = explode(':',$pa);
                        if (trim($dpa[0])!='') $opts[trim($dpa[0])]=trim($dpa[1]);
                }
                //options options="class:read;strand:+;count:places;copies:copies;sequence=sequenceA";
                //default options="class:read;strand:strand;count:1;copies:1;sequence=sequence";
                if (isset($opts['class']) && $opts['class']!='') $class=$opts['class'];
                $qstmt =  "select id, start, end, ";                
                if (isset($opts['strand']) && $opts['strand']!='strand') $qstmt .= "'".$opts['strand']."' as "; 
                $qstmt .= "strand, ";
		if ($seq=="sequence")
		{
			if (isset($opts['sequence']))
			{
				if ($opts['sequence']=='') $qstmt .= "'', ";
				else $qstmt .= $opts['sequence'].", ";
			}
			else $qstmt .= "$seq, ";   
		}
		else $qstmt .= "$seq, ";   
                if (isset($opts['count']) && $opts['count']!='1') $qstmt .= $opts['count'].", ";     
                else $qstmt .= "1, ";   
                if (isset($opts['copies']) && $opts['copies']!='1') $qstmt .= $opts['copies']." ";
                else $qstmt .= "1 ";
                $qstmt .= "from $table force index (start) where assembly = '$assembly' and start <= $right and start > $lefts and end >= $left order by start asc, end desc";
        }


	//$class = 'read';
	$result = array();
	$result[$class] = array ( 'watson' => array(), 'crick'  => array() );
	$idx = array ( '+' => 'watson', '-' => 'crick');

	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	//while ($r = mysqli_fetch_row($d))
	while ($r = $stmt->fetch(PDO::FETCH_NUM))
	{
		if ($r[3]=='-') $r[4]=strrev($r[4]);
		$result[$class][$idx[$r[3]]][] = array($r[0], $r[1]+$ibase, $r[2]-$r[1], $r[6]+0, $r[5]+0, $r[4]);
	}

	//Create cache and stream to user
	cache_create($query, $result, $auto, $table);
	unset( $result);
	return true;
}

?>
