<?PHP
if (!isset($_SESSION['aj_self']))
{
	echo "Warning: You have no permission to load this page. Bye!";
	exit (0);
}
$self = $_SESSION['aj_self'];
$aj2='..'; $group='';
if (isset($_SESSION['aj_root'])) $aj2=$_SESSION['aj_root'];
if (isset($_SESSION['aj_group'])) $group=$_SESSION['aj_group'];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=encoding">
<title>jBuilder Tool Login</title>
<link rel="stylesheet" href="<?PHP echo $aj2; ?>/css/styles.css" />
</head>

<body class="noborder">
<table border=0 align="center" width="580">
<tr><td border=0 align=center height=80 valign=bottom>
<?PHP if (isset($_SESSION['aj_msg'])) echo '<font style="font-family: Verdana, sans-serif, Arial; font-size: 1.0em; color: #408040;">'.$_SESSION['aj_msg'].'</font>'; $_SESSION['aj_msg']=''; ?>
</td></tr>
<tr><td border=0 align=center>
<form method="post" action="<?PHP echo $aj2; ?>/tools/login_confirm.php">
<table style="font-family: Verdana, sans-serif, Arial; font-size: 0.8em; color: #408040;">
<tr><td align="right" valign=bottom height=40 style="padding-bottom:4px;">Username: &nbsp; <input type="text" name="user_name" style="width:140px" onmouseover="this.focus();"> </td></tr>
<tr><td align="right" height=30>Password: &nbsp; <input type="password" name="user_pwd" style="width:140px" onmouseover="this.focus();"> </td></tr>
<tr><td height=50 valign="middle" align="center">
<input type='hidden' name="user_page" value='<?PHP echo $self; ?>'>
<?PHP if (isset($group)) { ?>
<input type='hidden' name="user_group" value='<?PHP echo $group; ?>'>
<?PHP } ?>
<input type=submit value="Login" style="width:100px;height:25px;"> 
&nbsp; </td></tr>
</table>
</form>
</td></tr>
</table>
</body>
</html>
