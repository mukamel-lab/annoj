<?php
require_once 'simple.php';

if ($action == 'syndicate')
{
	respond($syndication);
}

//require_once 'common_PDO.php';

if ($action == 'lookup')
{
	$limit = isset($_GET['limit']) ? clean_int($_GET['limit'])    : false;
	$query = isset($_GET['query']) ? clean_string($_GET['query']) : false;
	$start = isset($_GET['start']) ? clean_int($_GET['start'])    : false;

	if ($limit === false) error('Illegal limit value in lookup request');
	if ($query === false) error('Illegal query value in lookup request');
	if ($start === false) error('Illegal start value in lookup request');

	$count = 0;
	$list = array();

	$fn = $table.'/models.txt';
	if (file_exists($fn))
	{
		if ($inf=fopen($fn, "r"))
		{
			while ($sn=fgets($inf))
			{
				if (strstr($sn, $query)!=NULL)
				{
					$sa = explode("\t", trim($sn));
					if ($count>=$start && $count < ($start+$limit))
					{
						$sp = substr($sa[4], 0, 120).'...';
						$list[] = array ('id' => $sa[0], 'assembly' => $sa[1], 'start' => $sa[2], 'end' => $sa[3], 'description' => $sp);
					}
					$count++;
				}
			}
			fclose($inf);
		}
	}
	die(json_encode(array('success' => true, 'count' => $count, 'rows' => $list)));
}

/*
if ($action == 'walk')
{
	$id = isset($_GET['id']) ? clean_string($_GET['id']) : false;
	if ($id === false) error('Illegal id provided in walk request');
	$dir = isset($_GET['dir']) ? clean_string($_GET['dir']) : false;
	if ($dir === false || ($dir != 'F' && $dir != 'R')) error('Illegal walk direction in the request');
	
	$pr = null; $nr = null; $r;
	$fn = $table.'/models.txt';
	if (file_exists($fn))
	{
		if ($inf=fopen($fn, "r"))
		{
			while ($sn=fgets($inf))
			{
				$sa = explode("\t", trim($sn));
				if ($sa[0] == $id)
				{
					$nr = fgets($inf);
					break;
				}
				$pr = $sn;
			}
		}
		if ($dir == 'F' && $nr != '')
		{
			$sa = explode("\t", trim($nr));
			$r = array ('id' => $sa[0], 'assembly' => $sa[1], 'start' => $sa[2], 'end' => $sa[3]);
		}
		if ($dir == 'R' && $pr != '')
		{
			$sa = explode("\t", trim($pr));
			$r = array ('id' => $sa[0], 'assembly' => $sa[1], 'start' => $sa[2], 'end' => $sa[3]);
		}
	}
	respond($r);
}
*/

if ($action == 'describe')
{
	$id = isset($_GET['id']) ? clean_string($_GET['id']) : false;
	if ($id === false) error('Illegal id provided in request');

	$count = 0;
	$fn = $table.'/models.txt';
	if (file_exists($fn))
	{
		if ($inf=fopen($fn, "r"))
		{
			while ($sn=fgets($inf))
			{
				$sa = explode("\t", trim($sn));
				if ($sa[0] == $id)
				{
					$r = array ('id' => $sa[0], 'assembly' => $sa[1], 'start' => $sa[2], 'end' => $sa[3], 'description' => (isset($sa[4]))?$sa[4]:'');
					$count++; break;
				}
			}
		}
	}
	if ($count == 0) respond(null);
	respond($r);
}

require_once 'simple_query.php';

if ($action == 'range')
{
	getSimpleModelsTrackQuery ($table, $assembly, $left, $right, $bases, $pixels);
}

error('Invalid action requested: ' . $action);

?>
