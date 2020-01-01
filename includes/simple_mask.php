<?php
require_once 'simple.php';

if ($action == 'syndicate')
{
	respond($syndication);
}

//require_once 'common_PDO.php';
require_once 'simple_query.php';

if ($action == 'range')
{
	getSimpleMaskTrackQuery ($table, $assembly, $left, $right, $bases, $pixels);
}

error('Invalid action requested: ' . $action);

?>
