<?PHP
include_once '../includes/global_settings.php';
require_once 'util_module.php';

if (!isset($_POST))
{
	echo "Error: You have no permission to access this page. Thanks!";
	exit (0);
}

if (isset($_REQUEST['user_logout']))
{
	session_destroy(); 
	echo "Warning: You are logout now. Thanks!";
	exit (0);
}

if(session_id() == '') session_start();

$user = $pass = $group = '';

if (isset($_REQUEST['user_group'])) $group = $_REQUEST['user_group'];
if (isset($_REQUEST['user_name'])) 
{
	//$user = $_REQUEST['user_name'];
	$userg = explode(':', $_REQUEST['user_name']);
	$user = trim($userg[0]);
	if (isset($userg[1])) $group = trim($userg[1]);
}
if (isset($_REQUEST['user_pwd'])) $pass = $_REQUEST['user_pwd'];
if (isset($_REQUEST['user_page'])) $self = $_REQUEST['user_page'];


if (user_confirmation($user, $pass, $group))
{
	header('location: '.$self);
}
else
{
	if ($self!='') 
	{
		$_SESSION['aj_self'] = $self;
	}
	$_SESSION['aj_msg'] = "\tError: Login attempt failed. Please re-input.<br> <br> ".PHP_EOL;

	http_redirect('login_module.php');
}
?>
