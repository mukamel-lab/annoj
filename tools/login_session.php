<?PHP

if(session_id() == '') session_start();

if(isset($_REQUEST['Logout']) && $_REQUEST['Logout']=='Logout')
{
	if (isset($_SESSION['aj_user']))
	{
		unset($_SESSION['aj_user']);
		session_destroy();
	}
	echo "Warning: You are logout now. Thanks!";
	exit (0);
}

$_SESSION['aj_self'] = $_SERVER['PHP_SELF'];
$aj2 = aj_self ($_SERVER['PHP_SELF']);

if (!isset($_SESSION['aj_user'])) 
{
	$_SESSION['aj_root'] = $aj2;
	http_redirect('login_module.php');
}

$aj_user = $_SESSION['aj_user'];

?>
