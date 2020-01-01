<?PHP

$aj2 = '/aj2';
require_once ($_SERVER['DOCUMENT_ROOT'].$aj2.'/tools/util_module.php');

$priority = 'Public';
$visitor = 'admin';

if(session_id() == '') session_start();
$_SESSION['aj_self'] = $_SERVER['PHP_SELF'];
$_SESSION['aj_root'] = $aj2;
if ($priority!='Public' && !isset($_SESSION['aj_user'])) http_redirect('login_module.php');

if (!verify_visitor($priority, $visitor))
{
	echo "Warning : You have no permission for this page.".PHP_EOL;
	if (isset($_SESSION['aj_user'])) unset($_SESSION['aj_user']);
	exit (0);
}
?>
<html>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
	<title>hg38_Gencode_v24</title>

	<!-- ExtJS Dependencies -->
	<link type='text/css' rel='stylesheet' href='/aj2/css/ext-all.css' />
	<script type='text/javascript' src='/aj2/js/ext-base-3.2.js'></script>
	<script type='text/javascript' src='/aj2/js/ext-all-3.2.js'></script>

	<!-- Anno-J -->
	<link type='text/css' rel='stylesheet' href='/aj2/css/viewport.css' />
	<link type='text/css' rel='stylesheet' href='/aj2/css/plugins.css' />
	<link type='text/css' rel='stylesheet' href='/aj2/css/salk.css' />
	<script type='text/javascript' src='/aj2/js/excanvas.js'></script>
	<script type='text/javascript' src='/aj2/js/aj-min9-src.js'></script>

	<!-- Favicon -->
	<link rel='icon' href='/aj2/img/aj.ico' type='image/x-icon'>
	<link rel='shortcut icon' href='/aj2/img/aj.ico' type='image/x-icon'>

	<!-- Config -->
	<script type='text/javascript' src='/aj2/pages/jul307/hg38_Gencode_v24.js'></script>


</head>

<body>

	<!-- Message for people who do not have JS enabled -->
	<noscript>
		<table id='noscript'><tr>
			<td><img src='http://www.annoj.org/img/logo.jpg' /></td>
			<td>
				<P><h4>1. Anno-Browser: [Authors: Julian Tonti-Filippini and Tao Wang]</h4></P>
				<p>Anno-J cannot run because your browser is currently configured to block JavaScript.</p>
				<p>To use the application please access your browser settings or preferences, turn JavaScript support back on, and then refresh this page.</p>
				<p>Thank you, and enjoy the application! </p>
				<br>
				<P><h4>2. From Muddy Road To Speed Way: [Author: Huaming Chen]</h4></P>
				<P>All of the data loading scripts are re-coded to speed up the browser's performace. Now you could enjoy your browsing of genomes in a 'super fast' way.
				<P>This page and its configuration javascript are generated by Anno-J Management Tools.
			</td></tr>
		</table>
	</noscript>

	<!-- You can insert Google Analytics here if you wish -->

</body>

</html>
