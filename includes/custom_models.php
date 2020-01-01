<?php
require_once 'common.php';

if ($action == 'syndicate')
{
	respond($syndication);
}

require_once 'common_PDO.php';

if ($action == 'lookup')
{
	$limit = isset($_GET['limit']) ? clean_int($_GET['limit'])    : false;
	$query = isset($_GET['query']) ? clean_string($_GET['query']) : false;
	$start = isset($_GET['start']) ? clean_int($_GET['start'])    : false;

	if ($limit === false) error('Illegal limit value in lookup request');
	if ($query === false) error('Illegal query value in lookup request');
	if ($start === false) error('Illegal start value in lookup request');

	$qstmt = "select id, assembly, start, end, description from $table where parent is null and concat(id,description) like '%$query%'"; 
	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();
	$count = $stmt->rowCount();
	
	$qstmt = "select id, assembly, start, end, concat(substring(description,1,120), '...') as description from $table where parent is null and concat(id,description) like '%$query%' order by id asc limit $start,$limit";
	$stmt = $pdo->prepare($qstmt);
	$stmt->execute(); 
	$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
	die(json_encode(array(
		'success' => true,
		'count' => $count,
		'rows' => $list
	)));	
}

if ($action == 'describe')
{
	$id = isset($_GET['id']) ? clean_string($_GET['id']) : false;
	if ($id === false) error('Illegal id provided in request');
	$qstmt = "select id, assembly, start, end, description from ".$table." where id = ?";
	$stmt = $pdo->prepare($qstmt);

        $stmt->bindParam(1, $id, PDO::PARAM_STR); 
	$stmt->execute();

	if (($count = $stmt->rowCount()) == 0) respond(null);

	$r = $stmt->fetch(PDO::FETCH_ASSOC);
	respond($r);
}	

require_once 'common_query.php';

if ($action == 'range')
{
	if (isset($append_assembly) && $append_assembly) $table .= $assembly;
	if (!isset($options)) $options='';
	getCustomModelsTrackQuery ($pdo, $table, $assembly, $left, $right, $bases, $pixels,  true, $options);
}

error('Invalid action requested: ' . $action);

?>
