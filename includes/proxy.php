<?php
require_once 'common_func.php';
include_once 'global_settings.php';
require_once 'simple_query.php';

$_GET = $_REQUEST;

if (!isset($_GET['action'])) 
	error('Illegal access. A valid action must be specified');	

//Get the action
$action = clean_string($_GET['action']);

//Get proxy address (url)
$url = $_SERVER['QUERY_STRING'];
if (strstr($url,'&')!=null) $url = substr ($url, 0, strpos($url, '&'));

$ua = parse_url($url);

$ud = $proxy_cache_dir.'/'.$ua['host'].'/'.urlencode($ua['path']);

if (!file_exists($ud))
	error ('Illegal URL address. A valid url, which is proxy set up, must be specified');


if (!isset($ua['scheme']) || !isset($ua['host']) || trim($ua['scheme'])=='' || trim($ua['host'])=='')
	error('Illegal proxy address. A valid url must be specified');

/*
test:

$action = 'describe';
$_GET['id']='AT1G01040.1';

$action = 'lookup';
$_GET['query']='AT1G0104';
$_GET['limit'] = 10;
$_GET['start'] = 0;

$action = 'range';
$_GET['assembly']='2';
$_GET['left']=20000;
$_GET['right']=29999;
$_GET['bases']=1;
$_GET['pixels']=1;
*/

if ($action == 'describe' || $action == 'lookup')
{
	if ($action == 'lookup')
	{
		$limit = isset($_GET['limit']) ? clean_int($_GET['limit'])    : false;
		$query = isset($_GET['query']) ? clean_string($_GET['query']) : false;
		$start = isset($_GET['start']) ? clean_int($_GET['start'])    : false;

		if ($limit === false) error('Illegal limit value in lookup request');
		if ($query === false) error('Illegal query value in lookup request');
		if ($start === false) error('Illegal start value in lookup request');

		$qa = array ('action' => 'lookup', 'query' => $query, 'limit' => $limit, 'start' => $start);
	}
	else
	{
		$id = isset($_GET['id']) ? clean_string($_GET['id']) : false;
		if ($id === false) error('Illegal id provided in request');

		$qa = array ('action' => 'describe', 'id' => $id);
	}

	$contxt = array ('http' => array ('method' => 'POST', 'content' => http_build_query($qa)));
	$ds = file_get_contents($url, 0, stream_context_create($contxt));
        if ($ds === false) error ('Error in connection with proxy url');
        echo $ds;
}
else if ($action == 'syndicate')
{
	$qs = $ud.'/syndicate';
	if (proxy_cache_exists ($qs)) proxy_cache_stream ($qs);

	$qa = array ('action' => 'syndicate');
        $contxt = array ('http' => array ('method' => 'POST', 'content' => http_build_query($qa)));
        $ds = file_get_contents($url, 0, stream_context_create($contxt));
        if ($ds === false) error ('Error in connection with proxy url');
	echo $ds;

        $ch = $ud.'/.cache';
        //if (file_exists($ch) && '1' == file_get_contents($ch)) 		//always caching syndicate data no matter cache settings
	proxy_cache_create ($qs, $ds);
}
else if ($action == 'range')
{
	$assembly = isset($_GET['assembly']) ? clean_string($_GET['assembly']) : false; 
	$left     = isset($_GET['left'])     ? clean_int($_GET['left'])	       : false; 
	$right    = isset($_GET['right'])    ? clean_int($_GET['right'])       : false; 
	$bases    = isset($_GET['bases'])    ? clean_int($_GET['bases'])       : false;
	$pixels   = isset($_GET['pixels'])   ? clean_int($_GET['pixels'])      : false;
	
	if ($assembly === false) error('Illegal assembly value in range request');
	if ($left === false) error('Illegal left value in range request');
	if ($right === false) error('Illegal right value in range request');
	if ($bases === false) error('Illegal base value in range request');
	if ($pixels === false) error('Illegal pixel value in range request');

	$hd = getSimpleString('ProxyTrack', '', $assembly, $left, $right, $bases, $pixels);
	$qs = $ud.'/'.$hd;
	if (proxy_cache_exists ($qs)) proxy_cache_stream ($qs);

	$qa = array ('action' => 'range', 'assembly' => $assembly, 'left' => $left, 'right' => $right, 'bases' => $bases, 'pixels' => $pixels);
	$contxt = array ('http' => array ('method' => 'POST', 'content' => http_build_query($qa)));
	$ds = file_get_contents($url, 0, stream_context_create($contxt));
	if ($ds === false) error ('Error in connection with proxy url');
	echo $ds;

	$ch = $ud.'/.cache'; $gm = $ud.'/.type';
	if (file_exists($ch) && file_get_contents($ch)=='1' && file_get_contents($gm)!='ProxyGenome') proxy_cache_create ($qs, $ds);
	exit ();
}
else error ('Invalid action requested: ' . $action);
?>
