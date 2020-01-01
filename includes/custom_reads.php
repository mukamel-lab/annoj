<?php

require_once 'common.php';

if ($action == 'syndicate')
{
	respond($syndication);
}

include 'common_PDO.php';
require_once 'common_query.php';

if ($action == 'range')
{
	if (isset($append_assembly) && $append_assembly) $table .= $assembly;
	if (!isset($options)) $options = '';
	getCustomReadsTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels, true, $options);
}

error('Invalid action requested: ' . $action);
?>
