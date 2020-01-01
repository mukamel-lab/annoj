<?php
/*
A collection of routines for making fetchers easier to develop. This file should be included at the start of all fetchers
*/
function error($message)
{
	$array = array(
		'success' => false,
		'message' => $message
	);
	echo json_encode($array);
	exit();
}

//Respond with a JSON string
function respond($data)
{
	$array = array(
		'success' => true,
		'data' => $data
	);
	echo json_encode($array);
	exit();
}	

//Ensure that the value is a safe string
function clean_string($string)
{
	if (strlen($string) > 100000) return false;
	return addslashes(trim($string));
}

//Ensure that the value is a safe integer
function clean_int($int)
{
	$int += 0;
	if (!is_int($int)) return false;
	if ($int < 0) return false;
	return $int;
}

//Ensure that the value is a safe float
function clean_float($float)
{
	$float += 0;
	if (!is_float($float)) return false;
	if ($float < 0) return false;
	return $float;
}

//Do a database query bombing on error or no results
/*
function query($query)
{
	global $link;
	$d = mysqli_query($query, $link);
	if (mysqli_error()) error('A problem occurred when querying the database -> ' . mysqli_error());
	if (mysqli_num_rows($d) == 0) respond('');
	return $d;
}

//Process a mysql result and return as JSON
function process($d)
{
	$data = array();

	while ($r = mysqli_fetch_row($d)) {
		$data[] = $r;
	}
	respond($data);
}
*/

//Determine if a cache exists
function cache_exists($query, $table = '')
{
	global $cache_dir;
	$id = md5($query) . '.gz';
	
	if (!$table)
	{
		return file_exists($cache_dir . $id);
	}
	return file_exists($cache_dir . $table . '/' . $id);
	
}

//Create a cache file using a query and its associated result
function cache_create($query, &$data, $autostream = false, $table = '')
{
	if ($autostream && cache_exists($query, $table))
	{
		cache_stream($query, $table);
		return;
	}
	global $cache_dir;
	
	if ($table)
	{
		if (!is_dir($cache_dir . $table))
		{
			mkdir($cache_dir . $table);
		}
	}
	
	$fn = $cache_dir.($table ? $table . '/' : '').md5($query).'.gz';
	
	$array = array(
		'success' => true,
		'data' => $data
	);
	$string = json_encode($array);
	
	$fp = gzopen($fn, 'w');	
	gzwrite($fp, $string);
	gzclose($fp);
	
	if ($autostream) cache_stream($query, $table);
}

//Stream a cache to the user
function cache_stream($query, $table = '')
{
	if (!cache_exists($query, $table))
	{
		$array = array(
			'success' => false,
			'message' => 'Server tried to stream result data that does not exist'
		);
		die(json_encode($array));
	}
	global $cache_dir;
	
	$fn = $cache_dir . ($table ? $table . '/' : '') . md5($query) . '.gz';

	$fp = gzopen($fn, 'r');
	while (!gzeof($fp)) echo gzgets($fp);
	gzclose($fp);
	exit();
}

//cache string 	Author: Huaming Chen
function cache_string($query, $table = '')
{
	global $cache_dir; $rn = '';
	$fn = $cache_dir . ($table ? $table . '/' : '') . md5($query) . '.gz';
	if (file_exists($fn))
	{
		$fp = gzopen($fn, 'r');
		while (!gzeof($fp)) $rn .= gzgets($fp);
		gzclose($fp);
	}
	return $rn;
}

//Check if a simple cache exists. Author: Huaming Chen
function simple_cache_exists($query, $table)
{
	global $simple_cache_dir, $query_string; $fn="";
	if ($simple_cache_dir!='' && strrpos($table, '/')!=-1)
		$table = substr($table, strrpos($table, '/')+1);
	if (isset($query_string) && $query_string == 'Simple')
		$fn = $simple_cache_dir.$table. '/'.$query.'.gz';
	else $fn = $simple_cache_dir.$table. '/'.md5($query).'.gz';
	if(file_exists($fn)) return true;
	else return false;
}

//Create a simple cache file. Author: Huaming Chen
function simple_cache_create($query, &$data, $table, $force)
{
	global $simple_cache_dir, $query_string; $fn="";
	if ($simple_cache_dir!='' && strrpos($table, '/')!=-1)
		$table = substr($table, strrpos($table, '/')+1);
	if (isset($query_string) && $query_string == 'Simple')
		$fn = $simple_cache_dir.$table. '/'.$query.'.gz';
	else $fn = $simple_cache_dir.$table. '/'.md5($query).'.gz';
	$array = array(
		'success' => true,
		'data' => $data
	);
	$string = json_encode($array);

	$fp = gzopen($fn, 'w');
	gzwrite($fp, $string);
	gzclose($fp);
	return ;
}

//cache_stream for simple modules. Author: Huaming Chen
function simple_cache_stream($query, $table)
{
	global $simple_cache_dir, $query_string; $fn = "";
	if ($simple_cache_dir!='' && strrpos($table, '/')!=-1) 
		$table = substr($table, strrpos($table, '/')+1);
	if (isset($query_string) && $query_string == 'Simple')
		$fn = $simple_cache_dir.$table. "/".$query.".gz";
	else $fn = $simple_cache_dir.$table."/".md5($query).".gz";
	if(file_exists($fn))
	{
		$fp = gzopen($fn, 'r');
		while(!gzeof($fp)) echo gzgets($fp);
		gzclose($fp);
	}
	else respond('');
	exit();
}

//cache_string for simple modules. Author: Huaming Chen
function simple_cache_string($query, $table)
{
	global $simple_cache_dir, $query_string; $fn=''; $rn='';
	if ($simple_cache_dir!='' && strrpos($table, '/')!=-1)
		$table = substr($table, strrpos($table, '/')+1);
	if (isset($query_string) && $query_string == 'Simple')
		$fn = $simple_cache_dir.$table. '/'.$query.'.gz';
	else $fn = $simple_cache_dir.$table.'/'.md5($query).'.gz';
	if(file_exists($fn))
	{
		$fp = gzopen($fn, 'r');
		while(!gzeof($fp)) $rn .= gzgets($fp);
		gzclose($fp);
	}
	return $rn;
}

//Check if a proxy cache exists. Author: Huaming Chen
function proxy_cache_exists ($qs)
{
	if (file_exists($qs.'.gz')) return true;
	return false;
}

//cache_stream for proxy url modules. Author: Huaming Chen
function proxy_cache_stream ($qs)
{
	$fn = $qs.'.gz';
	if (file_exists($fn))
	{
		$fp = gzopen($fn, 'r');
		while(!gzeof($fp)) echo gzgets($fp);
		gzclose($fp);
	}
	exit();
}

//cache_stream for proxy url modules. Author: Huaming Chen
function proxy_cache_string ($qs)
{
	$fn = $qs.'.gz'; $rn = '';
	if (file_exists($fn))
	{
		$fp = gzopen($fn, 'r');
		while(!gzeof($fp)) $rn .= gzgets($fp);
		gzclose($fp);
	}
	return $rn;
}

//Create a proxy cache file. Author: Huaming Chen
function proxy_cache_create ($qs, $ds)
{
	if (strncasecmp($qs, '{"success":false,', 16)!=0)
	{
		$fn = $qs.'.gz';
		$fp = gzopen($fn, 'w');
		gzwrite($fp, $ds);
		gzclose($fp);
	}
}

//add index to table. Author: Huaming Chen
function index_exists($pdo, $table, $key='start')
{
	$query = "show index from ".$table." where key_name = '".$key."'";
	$stmt = $pdo->query($query);
	if ( $row = $stmt->fetch(PDO::FETCH_BOTH) )
	{
		print_r ($row);
		return true;
	}
	else return false;
}

function add_index_exec($pdo, $table, $key='start', $cols='start')
{
	$query = "alter table ".$table." add index ".$key." (".$cols.")";
	$pds = $pdo->prepare($query);
	$pds->execute();
	echo "add_index: ".$key." => ".$cols;
}

function add_index($pdo, $table, $key='start', $cols='start')
{
	if (!index_exists($pdo, $table, $key)) add_index_exec($pdo, $table, $key, $cols);
}

//Get max range from table
function max_range($pdo, $table)
{
	global $cache_dir;
	if (!is_dir($cache_dir.'table_infos'))
		mkdir ($cache_dir.'table_infos');
	$fn = $cache_dir.'table_infos/max_range-'.$table;
	if (file_exists($fn))
	{
		$inf = fopen($fn, "r");
		$max = fgets($inf);
		return intval($max)+1;
	}

	$query = 'select max(end-start) from '.$table;
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	if ($row = $stmt->fetch(PDO::FETCH_NUM))
	{
		$max = $row[0];
		$outf = fopen($fn, "w");
		fprintf($outf, "%s", $max);
		fclose($outf);
		return intval($max)+1;
	}
	return 1;
}

?>
