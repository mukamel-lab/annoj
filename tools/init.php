<?PHP
$err_msg=''; $fail_msg=''; $succ_msg='';

$gf = '../includes/global_settings.php';
$sgf = '../includes/simple_global_settings.php';
$xgf = '../includes/.global_settings';

if (file_exists($gf))
{
	echo "Waring: jBuilder has been initialized. Exit().".PHP_EOL;
	exit (0);
}

if (isset($_REQUEST['InitalSubmit']) && $_REQUEST['InitalSubmit']=='Initialization')
{
	if (!isset($_REQUEST['Cache_Path']) || $_REQUEST['Cache_Path']=='') $err_msg = "Cache path is empty."; 
	else if (!isset($_REQUEST['MyUser']) || $_REQUEST['MyUser']=='') $err_msg = "MySQL user is empty.";
	else if (!isset($_REQUEST['MyCode']) || $_REQUEST['MyCode']=='') $err_msg = "MySQL password is empty.";
	
	else if (!isset($_REQUEST['User_ID']) || $_REQUEST['User_ID']=='') $err_msg = "Admin ID is empty.";
	else if (!isset($_REQUEST['User_Name']) || $_REQUEST['User_Name']=='') $err_msg = "Admin name is empty.";
	else if (!isset($_REQUEST['User_Mail']) || $_REQUEST['User_Mail']=='') $err_msg = "Admin email is empty.";

	else if (!isset($_REQUEST['Group']) || $_REQUEST['Group']=='') $err_msg = "Admin group is empty.";
	else if (!isset($_REQUEST['Priority']) || $_REQUEST['Priority']=='') $err_msg = "Admin priority is empty.";

	if ($err_msg == '')
	{
		$cache_dir = $_REQUEST['Cache_Path'];
		if (!file_exists($cache_dir))
		{
			if (!mkdir($cache_dir, 0755, true))
			{
				$err_msg = 'error';
				$fail_msg = 'Can not create Cache_dir '.$cache_dir;
			}
		}
		else
		{
			$test_dir = $cache_dir.'/test.test.test';
			if (!mkdir($test_dir, 0700, true))
			{
				$err_msg = 'error';
				 $fail_msg = 'Cache dir '.$cache_dir.' can\'t be writable.';
			}
			else rmdir ($test_dir);
		}
	}

	if ($err_msg == '')
	{
		if (!isset($_REQUEST['Proxy_Path']) || $_REQUEST['Proxy_Path']=='')
			$proxy_dir = $cache_dir;
		else $proxy_dir = $_REQUEST['Proxy_Path'];

		if (!file_exists($proxy_dir))
		{
			if (!mkdir($proxy_dir, 0755, true))
			{
				$err_msg = 'error';
				$fail_msg = 'Can not create Proxy_cache_dir '.$proxy_dir;
			}
		}
		else
		{
			$test_dir = $proxy_dir.'/test.test.test';
			if (!mkdir($test_dir, 0700, true))
			{
				$err_msg = 'error';
				$fail_msg = 'Proxy cache dir '.$proxy_dir.' can\'t be writable.';
			}
			else rmdir ($test_dir);
		}
	}

	if ($err_msg == '')
	{
		if (!file_exists($cache_dir.'/.htaccess/') && !mkdir($cache_dir.'/.htaccess/', 0777, true))
		{
			$err_msg = 'error';
			$fail_msg = 'Can not create path '.$cache_dir.'/.htaccess/';
		}
	}

	if ($err_msg == '')
	{
		$access_path = $cache_dir.'/.htaccess/.htaccess_md5_db';

		$user = trim($_REQUEST['User_ID']);
		$name = trim($_REQUEST['User_Name']);  
		$email = trim($_REQUEST['User_Mail']);  
		$group = trim($_REQUEST['Group']);  
		$priority = trim($_REQUEST['Priority']);  

		$nu = $user."\t\t".$group."\t".$name."\t".$email."\t".$priority;

		if (($outf=fopen($access_path.".tmp.".$user, "w"))==null)
		{
			$err_msg = 'error';
			$fail_msg = 'Can not create new user to '.$access_path.".tmp.".$user;
		}
		else
		{
			fprintf($outf, "%s", $nu);
			fclose($outf);
		}
	}

	if ($err_msg == '')
	{
		if (trim($_REQUEST['Server'])=='') $server = 'localhost';
		else $server = trim($_REQUEST['Server']);
		$myusr = trim($_REQUEST['MyUser']);
		$mycode = trim($_REQUEST['MyCode']);
		$cache_dir = trim($_REQUEST['Cache_Path']);

		if (($outf=fopen($gf, "w"))==null)
		{
			$err_msg = 'error';
			$fail_msg = 'Can not create global setting file';
		}
		else
		{
			$ng = '<?PHP'.PHP_EOL.PHP_EOL.'if (!isset($server)) $server = \''.$server.'\';'.PHP_EOL.'if (!isset($user)) $user = \''.$myusr.'\';'.PHP_EOL.'if (!isset($pass)) $pass = \''.$mycode.'\';'.PHP_EOL.PHP_EOL;
			$ng .= 'if (!isset($cache_dir)) $cache_dir = \''.$cache_dir.'\';'.PHP_EOL;
			if ($proxy_dir!=$cache_dir) $ng .= '$proxy_cache_dir = \''.$proxy_dir.'\';'.PHP_EOL;
			$ng .= PHP_EOL.'if (!isset($ibase)) $ibase=0;'.PHP_EOL.PHP_EOL.'?>'.PHP_EOL;
			fprintf($outf, "%s", $ng);
			fclose($outf);
		}

		if ($err_msg == '')
        	{
			if (($outf=fopen($sgf, "w"))==null)
			{
				$err_msg = 'error';
				$fail_msg = 'Can not create simple global setting file';
				exec ('rm '.$gf);
			}
			else
			{
				$ng = '<?PHP'.PHP_EOL.PHP_EOL.'$simple_cache_dir = \'\';'.PHP_EOL.'if (!isset($query_string)) $query_string = \'Simple\';'.PHP_EOL.PHP_EOL.'?>'.PHP_EOL;
				fprintf($outf, "%s", $ng);
				fclose($outf);
			}
		}
	}

	if ($err_msg == '')
	{
			
		$err_msg = 'No Error'; $succ_msg = 'jBuilder is set up successfully. Please <a href=index.php>sign in</a>.';
		if (($outf=fopen($xgf, "w"))!=null)
		{
			if (isset($_REQUEST['Cache_Path'])) fprintf($outf, "%s : %s", 'Cache_Path', $_REQUEST['Cache_Path'].PHP_EOL);
                        if (isset($_REQUEST['Proxy_Path'])) fprintf($outf, "%s : %s", 'Proxy_Path', $_REQUEST['Proxy_Path'].PHP_EOL);

                        if (isset($_REQUEST['Server'])) fprintf($outf, "%s : %s", 'Server', $_REQUEST['Server'].PHP_EOL);
                        if (isset($_REQUEST['MyUser'])) fprintf($outf, "%s : %s", 'MyUser', $_REQUEST['MyUser'].PHP_EOL);
                        if (isset($_REQUEST['MyCode'])) fprintf($outf, "%s : %s", 'MyCode', $_REQUEST['MyCode'].PHP_EOL);

                        if (isset($_REQUEST['User_ID'])) fprintf($outf, "%s : %s", 'User_ID', $_REQUEST['User_ID'].PHP_EOL);
                        if (isset($_REQUEST['User_Name'])) fprintf($outf, "%s : %s", 'User_Name', $_REQUEST['User_Name'].PHP_EOL);
                        if (isset($_REQUEST['User_Mail'])) fprintf($outf, "%s : %s", 'User_Mail', $_REQUEST['User_Mail'].PHP_EOL);
                        if (isset($_REQUEST['Group'])) fprintf($outf, "%s : %s", 'Group', $_REQUEST['Group'].PHP_EOL);
                        if (isset($_REQUEST['Priority'])) fprintf($outf, "%s : %s", 'Priority', $_REQUEST['Priority'].PHP_EOL);
                        
			fclose($outf);
		}
	}
}
else
{
	if (file_exists($xgf) && ($inf=fopen($xgf, "r"))!=null)
	{
		while ($sn = fgets($inf))
		{
			$vn = explode (" : ", $sn);
			$_REQUEST[trim($vn[0])]=trim($vn[1]);
		}
		fclose($inf);
	}
}

function jk ($d)
{
	if (isset($_REQUEST[$d])) echo $_REQUEST[$d];
	else echo '';
}
?>
<head>
	<link type='text/css' rel='stylesheet' href='../css/util.css' />
</head>
<body>
<table border="0" width="580" height="40" align=center>
	<tr align=center><td style="font-family: Verdana, sans-serif, Arial; font-size: 1.15em; font-weight: bold; color: #408040;">jBuilder Initialization &nbsp;&nbsp; </td></tr>
</table>

<?PHP if ($err_msg!='') { ?>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Administration Information">
<tr><td class="THL" colspan="3" height="25">&nbsp; jBuilder Initialization </td></tr>
<?PHP if ($succ_msg!='') { ?>
<tr><td class="TDL" width="20%">Success:</td><td class="TDL" width="60%"><?PHP echo $succ_msg ?></td><td class="TDL"></td></tr>
<?PHP } ?>
<?PHP if ($fail_msg!='') { ?>
<tr><td class="TDL" width="20%">Failed:</td><td class="TDL" width="60%"><?PHP echo $fail_msg ?></td><td class="TDL"></td></tr>
<?PHP } ?>
<?PHP if ($err_msg!='No Error') { ?>
<tr><td class="TDL" width="20%">Error:</td><td class="TDL" width="60%"><?PHP echo $err_msg.' '; ?></td><td class="TDL"></td></tr>
<?PHP } ?>
</table><?PHP } ?>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">

<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Security Information">
	<tr><td class="THL" colspan="3" height="25">&nbsp;Caches</td></tr>
	<tr height=30 valign=bottom><td class="TDL" width="20%">Cache Dir:</td>
		<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" NAME="Cache_Path" value="<?PHP jk('Cache_Path'); ?>"></td><td class="TDL"></td></tr>
	<tr height=35 valign=middle><td class="TDL" width="20%">Proxy Cache Dir:</td>
		<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" NAME="Proxy_Path" value="<?PHP jk('Proxy_Path'); ?>"></td><td class="TDL"></td></tr>

</table>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J MySQL Information">
	<tr><td class="THL" colspan="3" height="25">&nbsp;MySQL</td></tr>
	<tr height=30 valign=bottom><td class="TDL" width="20%">MySQL Server:</td>
		<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" id="x_Server" NAME="Server" value="<?PHP jk('Server'); ?>"></td><td class="TDL"></td></tr>
	<tr height=30 valign=bottom><td class="TDL" width="20%">MySQL User:</td>
		<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" NAME="MyUser" value="<?PHP jk('MyUser'); ?>"></td><td class="TDL"></td></tr>
	<tr height=36 valign=middle><td class="TDL" width="20%">MySQL Password:</td>
		<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" NAME="MyCode" value="<?PHP jk('MyCode'); ?>"></td><td class="TDL"></td></tr>
</table>
<P>

<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Adminitrator Information">
	<tr><td class="THL" colspan="3" height="25">&nbsp;Administrator</td></tr>
	<tr height=30 valign=bottom><td class="TDL" width="20%">Admin ID:</td>
		<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" id="x_User_ID" NAME="User_ID" value="<?PHP jk('User_ID'); ?>"></td><td class="TDL"></td></tr>
	<tr height=30 valign=bottom><td class="TDL" width="20%">Admin Name:</td>
		<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" NAME="User_Name" value="<?PHP jk('User_Name'); ?>"></td><td class="TDL"></td></tr>
	<tr height=30 valign=bottom><td class="TDL">E-mail:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="User_Mail" value="<?PHP jk('User_Mail'); ?>"></td><td class="TDL"></td></tr>
	<tr height=30 valign=bottom><td class="TDL">Group:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Group" id="x_Group" value="<?PHP jk('Group'); ?>"><td class="TDL">
	</td></tr>
	<tr height=36 valign=middle><td class="TDL">Priority:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Priority" value="<?PHP jk('Priority'); ?>"><td class="TDL"></td></tr>
	</tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
	<tr><td width="30%" align="left">
		</td><td align="center" valign="bottom" width="40%">
		&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="InitalSubmit" value="Initialization" style="width:140px;height:25px;font-weight:bold">&nbsp; &nbsp;
		</td><td align="right" width="30%">
	</td></tr>
</table>
</form>

