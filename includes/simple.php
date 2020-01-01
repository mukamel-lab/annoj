<?php

require_once 'common_func.php';

/*
A collection of routines for making fetchers easier to develop. This file should be included at the start of all fetchers
*/
if (!function_exists('json_encode'))
{
	include 'json.php';
	$json = new Services_JSON();

	function json_encode($obj) {
		global $json;
		return $json->encode($obj);
	}
	function json_decode($s) {
		global $json;
		return $json->decode($s);
	}
}

include_once 'simple_global_settings.php';
//if (!isset($ibase)) $ibase=0;

if (count($_GET) == 0) {
	$_GET = $_POST;
}

if (!isset($_GET['action'])) {
	error('Illegal access. A valid action must be specified');	
}

//Get the action
$action = clean_string($_GET['action']);

//Global syndication parameters (override in fetchers as necessary)
include_once 'common_info.php';

//Walk function
include_once 'simple_walks.php';

//Check input parameters if the action is a range request
if ($action == 'range')
{
	$assembly = isset($_GET['assembly']) ? clean_string($_GET['assembly']) : false; 
	$left     = isset($_GET['left'])     ? clean_int($_GET['left'])        : false; 
	$right    = isset($_GET['right'])    ? clean_int($_GET['right'])       : false; 
	$bases    = isset($_GET['bases'])    ? clean_int($_GET['bases'])       : false;
	$pixels   = isset($_GET['pixels'])   ? clean_int($_GET['pixels'])      : false;
	
	if ($assembly === false) {
		error('Illegal assembly value in range request');
	}
	if ($left === false) {
		error('Illegal left value in range request');
	}
	if ($right === false) {
		error('Illegal right value in range request');
	}
	if ($bases === false) {
		error('Illegal base value in range request');
	}
	if ($pixels === false) {
		error('Illegal pixel value in range request');
	}
}

?>
