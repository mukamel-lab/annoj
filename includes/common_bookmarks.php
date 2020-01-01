<?PHP
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With');

require_once 'common.php';

if (isset($_SERVER['HTTP_REFERER']))
{
	$ip = $_SERVER['REMOTE_ADDR'];
	$ref = $_SERVER['HTTP_REFERER'];
	$dir = $cache_dir.'/.bookmarks/'.md5($ref).'/';

	if ($action=='bookmarks_save')
	{
		if (!file_exists($dir)) mkdir ($dir, 0755, true);
		file_put_contents($dir.$ip, $_REQUEST['bookmarks']);
		echo '{"success":true,"message":"Bookmarks saved"}';
	}

	if ($action=='bookmarks_load')
	{
		if (file_exists($dir.$ip))
			echo file_get_contents($dir.$ip);
		else if (file_exists($dir.'default'))
			echo file_get_contents($dir.'default');
		else echo '[]';
	}
}
?>
