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
	getIndelQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels, true);
}

error('Invalid action requested: ' . $action);

?>
