<?PHP
require_once 'simple.php';

	if (!isset($_GET['action']) || !isset($_GET['action2']) || !isset($_SERVER['HTTP_REFERER']))
		error('Illegal access. A valid action must be specified');

	//Get the action
	$qa = array();  $data = array();
	$action2 = clean_string($_GET['action2']);
	
	//assembly=1      left=0  right=99999     bases=10	pixels=1 
	$qa['action'] = $action;
	$qa['assembly'] = $assembly;
	$qa['left'] = $left;
	$qa['right'] = $right;
	$qa['bases'] = $bases;
	$qa['pixels'] = $pixels;
	$urls = array_filter(explode(",", $_GET['urls']));
	$names = explode(" - ", $_GET['table']);
	$tracktypes = array_filter(explode(",", $_GET['tracktype']));

	$ua = parse_url($_SERVER['HTTP_REFERER']);
	$n = count($urls);
	for ($i=0; $i<$n; $i++)
	{
		$url = $urls[$i];
		if (strncmp($url, "http://", 7)==0) ;
		else if (strncmp($url, "https://", 8)==0) ;
		else if ($url!='' && $url[0]=='/')
		{
			if (!isset($ua['scheme'])) $url = $_SERVER['DOCUMENT_ROOT'].$url;
			else $url = $ua['scheme'].'://'.$ua['host'].$url;
		}
		else
		{
			if (strstr($ua['path'], "/")!=null)
				$ph = substr($ua['path'], 0, strrpos($ua['path'], "/"));
			if (!isset($ua['scheme'])) $url = clean_url_parent ($ph.'/'.$url);
			else $url = clean_url_parent ($ua['scheme'].'://'.$ua['host'].$ph.'/'.$url);
		}
		$urls[$i] = $url;
		$qa['table'] = $names[$i+1];
		$qa['tracktype'] = $tracktypes[$i];
		$query = http_build_query($qa);
		$fn = $url."?".$query;
		$se = efile_get_contents($fn);
		if (isset($se['err'])) $urls[$n+$i] = $se['err']; 
		else $urls[$n+$i] = $fn;
		$d = json_decode($se['str']);
		foreach ($d as $k => $v)
		{
			if ($k=='data') $data[] = $v;
		}
	}

	$u = $bases/$pixels;
	$ln = ceil(($right-$left+1)/$u);
	$p = array ();
	$q = array ();
	$z = 0;
	foreach ($data as $c => $d)
	{
		foreach ($d as $k => $v)
		{
			if (!isset($p[$k])) $p[$k] = array ();
			if (!isset($q[$k])) $q[$k] = array ();
			$p[$k][$z] = array_fill(0, $ln, 0);
			$q[$k][$z] = array_fill(0, $ln, 0);
			foreach ($v as $m)
			{
				$n = count($m);
				$o = ($m[0]-$left)/$u;
				if ($n==3)
				{
					$p[$k][$z][$o]=$m[1];
					$q[$k][$z][$o]=$m[2];
				}
				else if ($n==4)
				{
					$w = $m[1]/$u;
					for ($i=0; $i<$w; $i++)
					{
						$p[$k][$z][$o+$i]=$m[2];
						$q[$k][$z][$o+$i]=$m[3];
					}
				}
			}
		}
		$z++;
	}

	if ($action2 == "Merge")
	{
		$result = array();
		foreach ($p as $k => $v)
		{
			$n = count($p[$k]);
			for ($j=0; $j<$n; $j++)
			{
				if (!isset($result[$k])) $class = $k;
				else $class = $k.$j;
				$result[$class] = array();
				for ($i=0; $i<$ln; $i++)
				{
					if ($p[$k][$j][$i]<=0 && $q[$k][$j][$i]<=0) continue;
					$gpos = $left + $i*$u;
					$result[$class]["$gpos"] = array($gpos,0,0);
					$result[$class]["$gpos"][1] = $p[$k][$j][$i];
					$result[$class]["$gpos"][2] = $q[$k][$j][$i];
				}
			}
		}
		$result = doSimplifyClean ($result, $u);
		$array = array('success' => true, 'data' => $result);
		$string = json_encode($array);
		echo $string;

		unset($data); unset($p); unset($q); unset($result); unset($array); 
	}
	else if ($action2 == "Summation" || $action2 == "Subtract" || $action2 == "Intersection")
	{
		if ($action2 == "Summation")
		{
			$ps = array();
			foreach ($p as $k => $v)
			{
				$ps[$k] = array_fill(0, $ln, 0);
				$m = count($p[$k]);
				for ($i=0; $i<$ln; $i++)
				{
					for ($j=0; $j<$m; $j++)
					{
						$ps[$k][$i] += $v[$j][$i];
					}
				}
			}
			$qs = array();
			foreach ($q as $k => $v)
                        {
                                $qs[$k] = array_fill(0, $ln, 0);
                                $m = count($q[$k]);
                                for ($i=0; $i<$ln; $i++)
                                {
                                        for ($j=0; $j<$m; $j++)
                                        {
                                                $qs[$k][$i] += $v[$j][$i];     
                                        }
                                }
                        }
		}
		else if ($action2 == "Subtract")
		{
			$ps = array();
                        foreach ($p as $k => $v)
                        {
                                $ps[$k] = array_fill(0, $ln, 0);
                                for ($i=0; $i<$ln; $i++)
                                {
					$ps[$k][$i] = ($v[0][$i]>$v[1][$i]) ? $v[0][$i]-$v[1][$i] : 0;
                                }
                        }
                        $qs = array();
                        foreach ($q as $k => $v)
                        {
                                $qs[$k] = array_fill(0, $ln, 0);
                                for ($i=0; $i<$ln; $i++)
                                {
					$qs[$k][$i] = ($v[0][$i]>$v[1][$i]) ? $v[0][$i]-$v[1][$i] : 0;
                                }
                        }
		}
		else if ($action2 == "Intersection")
		{
			$ps = array();
                        foreach ($p as $k => $v)
                        {
                                $ps[$k] = array_fill(0, $ln, 0);
                                for ($i=0; $i<$ln; $i++)
                                {
                                        $ps[$k][$i] = ($v[0][$i]>$v[1][$i]) ? $v[1][$i] : $v[0][$i];
                                }
                        }
                        $qs = array();
                        foreach ($q as $k => $v)
                        {
                                $qs[$k] = array_fill(0, $ln, 0);
                                for ($i=0; $i<$ln; $i++)
                                {
                                        $qs[$k][$i] = ($v[0][$i]>$v[1][$i]) ? $v[1][$i] : $v[0][$i];
                                }
                        }
		}

		$result = array();
		foreach ($p as $k => $v)
		{
			$result[$k] = array();
			for ($i=0; $i<$ln; $i++)
			{
				if ($ps[$k][$i]<=0 && $qs[$k][$i]<=0) continue;
				$gpos = $left + $i*$u;
				$result[$k]["$gpos"] = array($gpos,0,0);
				$result[$k]["$gpos"][1] = $ps[$k][$i];
				$result[$k]["$gpos"][2] = $qs[$k][$i];
			}
		}
		$result = doSimplifyClean ($result, $u);
		$array = array('success' => true, 'data' => $result);
		$string = json_encode($array);
		echo $string;
	
		unset($data); unset($p); unset($q); unset($ps); unset($qs);
		unset($result); unset($array); unset($qa);
	}

	//file_put_contents("/tmp/analysis2", print_r($urls, true)."\n".$string);
	//respond("");

function efile_get_contents ($fn)
{
	$se = array();
	set_error_handler(create_function('$severity, $message, $file, $line',
		'throw new ErrorException($message, $severity, $severity, $file, $line);'));
	try { $se['str'] = file_get_contents($fn, true); }
	catch (Exception $e) { $se['err'] = $e->getMessage(); }
	restore_error_handler();
	return $se;
}

function clean_url_parent ($url)
{
	$uc = explode ('/', $url);
	$nc = count($uc);
	for ($j=1;$j<$nc;$j++)
	{
		if ($uc[$j]=='.') unset($uc[$j]);
		else if ($uc[$j]=='..')
		{
			unset($uc[$j]);
			$z=$j;
			while (!isset($uc[$z]) && $z>=0) $z--;
			if ($z>=0 && isset($uc[$z])) unset($uc[$z]);
		}
	}
	return implode('/', $uc);
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
?>
