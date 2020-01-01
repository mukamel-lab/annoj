<?PHP
require_once 'login_session.php';
if (isset($_SESSION['aj_priority']) && $_SESSION['aj_priority'][0]=='0')
{
	echo "\tWarning: You have no permission to run this script.".PHP_EOL;
	unset($_SESSION['aj_user']); unset($_SESSION['aj_group']);
	exit (0);
}

if (!isset($_SESSION['aj_root']) || aj_self($_SERVER['PHP_SELF'])!=rtrim($_SESSION['aj_root'],"/"))
{
	$_SESSION['aj_msg'] = "\tError: The aj_root is changed. Please re-login :<br> <br> ".PHP_EOL;
	unset($_SESSION['aj_user']); unset($_SESSION['aj_root']); unset($_SESSION['aj_group']);
	$_SESSION['aj_self'] = $_SERVER['PHP_SELF'];
        http_redirect('login_module.php');
	exit (0);
}

?>
