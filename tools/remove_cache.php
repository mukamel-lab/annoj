<?PHP
ini_set('display_errors', 'On');
ini_set('max_execution_time', 28800);

require_once '../includes/global_settings.php';
require_once 'util_module.php';
require_once 'login_session2.php';

$err_star = "<font color=red>*</font>";
$err_no = '';
$err_msg = '';
$succ_msg = '';
$fail_msg = '';

if (isset($_REQUEST['Submit']) && $_REQUEST['Submit']=='Retrieve')
{
	if (!isset($_REQUEST['Save_File']) ||  $_REQUEST['Save_File']=='')
	{ $err_no = 'Save_File'; $err_msg = ' No filename specified'; }
	else
	{
		$df = implode(".", array_diff(explode(".", $_REQUEST['Save_File']), array('PHP', 'php')));
		$page_path = $aj2.'/pages/'.$aj_user.'/';
		$fn = $_SERVER['DOCUMENT_ROOT'].$page_path.'.'.$df.'.db';
		if (!file_exists($fn)) { $err_no = 'Save_File'; $err_msg = ' File does not exist'; }
		else if (($inf = fopen($fn, "r"))==NULL) { $err_no = 'Save_File'; $err_msg = ' Can not open file'; }
		else
		{
			$HK = array();
			$HK['Track_URL'] = array();

			while (($sn=fgets($inf))!=NULL)
			{
				$sa = explode(' =:= ', $sn);
				if ($sa[0]=='Keys' || $sa[0]=='keys')
				{
					$sk = explode(' =,= ', trim($sa[1]));
					$ky = array();
					foreach ($sk as $i => $k) $ky[$k]=$i;
				}
				else if ($sa[0]=='Track')
				{
					$tk = explode(' =,= ', trim($sa[1]));
					$HK['Track_URL'][] = $tk[$ky['Track_URL']];
				}
			}
			fclose ($inf);
			$_REQUEST['Track_URL'] = implode(PHP_EOL, $HK['Track_URL']);
		}
	}
}

$tables = array();

if (isset($_REQUEST['Submit']) && $_REQUEST['Submit']=='Remove Caches')
{
	if ($_REQUEST['Track_URL'] == '') { $err_no = 'Track_URL'; $err_msg = ' Must be filled.'; }
	else
	{
		$track_url = explode(PHP_EOL, trim($_REQUEST['Track_URL']));
		$no = count($track_url);

		for ($i=0; $i<$no; $i++)
		{
			//echo $track_url[$i]."<br>";
			$tv = explode("/", trim($track_url[$i]));
			//print_r($tv);
			if($tv[3]!=$aj_user && $_SESSION['aj_group']!='admin') 
			{
				$err_msg .= $track_url[$i]." ".$err_star."caches can not be removed<br>".PHP_EOL;
				continue;
			}	
			if (($ta=getTableInfo($_SERVER['DOCUMENT_ROOT'].trim($track_url[$i])))==false)
			{
				$fail_msg .= '<nobr>'.$track_url[$i].' not exist or can not open file.</nobr><br>'.PHP_EOL;
				unset($track_url[$i]); unset($track_type[$i]);
			}
			else
			{
				$ta['Track_URL'] = $track_url[$i];
				$tables[]=$ta;
			}						

		}
		$no = count( $tables);
		//echo "<pre>";
		for ($i=0; $i<$no; $i++)
		{
			if ($tables[$i]['Append']==true)
				$table = $tables[$i]['Table'].'*';
			else  $table = $tables[$i]['Table'];
			$rmv_cmd = "rm -rf ".$cache_dir.$table;
			//echo $rmv_cmd.PHP_EOL;
			exec($rmv_cmd);
			if ($err_no=='') $err_no='Success';
			$err_msg .= $tables[$i]['Track_URL']." ".$table." caches are removed<br>".PHP_EOL;
		}
		//echo "</pre>";
	}
}
?>
<head>
<link type='text/css' rel='stylesheet' href='<?PHP echo $aj2 ?>/css/util.css' />
</head>
<body>
<P>
<table cellspacing="5" border="0" height="40" width="580" align=center>
<tr><td width=24%></td><td style="font-family: Verdana, sans-serif, Arial; font-size: 1.1em; font-weight: bold; color: #408040;">jBuider : Caching Removal</td></tr>
</table>
<?PHP if (isset($_REQUEST['Submit']) && $err_no!='') { ?>
<P>
<table class="TBL" cellspacing="5" border="0" align=center>
<tr><td class="THL" colspan="3">&nbsp;Removing Cache Result</td></tr>
<tr><td class="TDL" width="20%"><?PHP if ($err_no=='Success') echo 'Success:'; else echo 'Error:'; ?></td><td class="TDL" colspan="2">
<?PHP if ($err_no=='Success') echo $err_msg; else echo $err_no.': '.$err_msg.'. '.$err_star.' marked below.'; ?>
</td></tr></table>
<?PHP } ?>

<form method="POST">
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
<tr><td class="THL" colspan="3">&nbsp;Removing Track Caches</td></tr>
<tr><td class="TDL" width="20%" height="30" valign="bottom">Config URL: <?PHP if ($err_no=='Save_File') echo $err_star; ?></td><td class="TDL" width="60%"><INPUT class="CEL" TYPE="text" id="x_Save_File" NAME="Save_File" id="x_Save_File" VALUE="<?PHP if (isset($_REQUEST['Save_File'])) echo $_REQUEST['Save_File']; ?>">
</td><td class="TDL"> 
<select class="RCEL" NAME="Save_File_Options" onChange="document.getElementById('x_Save_File').value=this.value;">
<option value="">Choose from</option>
<?PHP
        $fn = "ls ".$_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/.*.db';
        $inf = popen($fn, "r");
        while($sn=fgets($inf))
        {
                $zn = trim(substr($sn, strrpos($sn,'/')+2));
                $en = substr($zn,0,strrpos($zn,'.db')).'.php';
                echo '<option value="'.$en.'">'.$en.'</option>'.PHP_EOL;
        }
        pclose($inf);
?>
</select>

</td></tr>
<tr><td class="TDL">Track URL: <?PHP if ($err_no=='Track_URL') echo $err_star; ?></td><td class="TDL" width="60%">
<TEXTAREA class="CEL" ROWS="16" id="x_Track_URL" NAME="Track_URL"><?PHP if (isset($_REQUEST['Track_URL'])) echo $_REQUEST['Track_URL']; ?></TEXTAREA></td><td class="TDL"><?PHP if ($err_no=='Track_URL') echo $err_star.$err_msg; ?></td></tr>
</td></tr>
</table>

<P>
<table class="noborder" width="580" cellspacing="5" height="40" align=center>
<tr><td>
<INPUT TYPE="SUBMIT" Name="Logout" value="Logout" style="width:60px;height:22px"> &nbsp;
<INPUT TYPE="BUTTON" Name="Reset" onClick="document.getElementById('x_Track_URL').value=''; document.getElementById('x_Save_File').value='';" value="Clear" style="width:60px;height:22px">
</td><td align="center" width="40%">
<INPUT TYPE="SUBMIT" Name="Submit" value="Remove Caches" style="width:120px;height:25px;font-weight:bold">
</td><td align="right" width="30%">
<INPUT TYPE="SUBMIT" Name="Submit" value="Retrieve" style="width:75px;height:22px">
</td></tr></table>
</form>


