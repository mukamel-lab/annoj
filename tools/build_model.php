<?PHP
ini_set('display_errors', 'On');
require_once 'util_module.php';
require_once 'login_session3.php';

//Check if error
$err_no = '';
$err_msg = '';
$succ_msg = '';
$fail_msg = '';

if (isset($_REQUEST['Action']) && $_REQUEST['Action']=='Retrieve')
{
	$record = trim($_REQUEST['Record']);
	$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$aj_user.'/.Record_'.$record.'.db';

	if (($inf=fopen($fn, 'r'))==NULL)
	{
		$err_msg = 'Can not open record.'.PHP_EOL;
	}
	else
	{
		$HK = array();
		while ($sn=fgets($inf))
		{
			$en = explode(' =:= ', $sn);
			$HK[trim($en[0])] = trim(implode(PHP_EOL, explode('	', trim($en[1]))));
		}
		fclose($inf);

		foreach($HK as $Key => $Value)
		{
			if ($Key=="Action") continue;
			$_REQUEST[trim($Key)] = $Value;
		}
		$err_msg = 'No Error';
		$succ_msg = $record." is successfully retrieved.".PHP_EOL;
	}
}

if (isset($_REQUEST['Action']) && $_REQUEST['Action']=='Build')
{
	$err_msg = aj_error_check();
	if ($err_msg == 'No Error')
	{
		$tables = explode(PHP_EOL, trim($_REQUEST['Table_List']));
		$titles = explode(PHP_EOL, trim($_REQUEST['Title_List']));
		if (isset($_REQUEST['Info_List']) && $_REQUEST['Info_List']!=NULL)
			$infos = explode(PHP_EOL, trim($_REQUEST['Info_List']));
		else $infos = $titles;
		if (strstr($_REQUEST['Include'], "custom_")!=NULL && isset($_REQUEST['Options_List']) && $_REQUEST['Options_List']!=NULL)
			$opts = explode(PHP_EOL, trim($_REQUEST['Options_List']));
		else { $opts = array(); $opts[]=''; }

		if (count($tables)!=count($opts))
		{
			$no = count($tables);
			$nt = count($opts);
			for ($i=$nt; $i<$no; $i++) $opts[]=$opts[0];
		}

		if (isset($_REQUEST['File_List']) && $_REQUEST['File_List']!=NULL)
			$files = explode(PHP_EOL, trim($_REQUEST['File_List']));
		else
		{
			$files = array();
			foreach ($tables as $fvar)
				$files[]=rtrim(substr(trim($fvar), strpos($fvar, '.')+1),'_-').".php";

		}

		$force = (isset($_REQUEST['Force']))?$_REQUEST['Force']:0;
		$path = ""; if (isset($_REQUEST['Path'])) $path = $_REQUEST['Path'];

		$head = '<?PHP';
		if (isset($_REQUEST['CORS'])) $head .= PHP_EOL.http_cors();
		if (isset($_REQUEST['Append_Assembly'])) $head .= PHP_EOL.'$append_assembly = true;';
		$head .= PHP_EOL.'$provider_name = \''.$_REQUEST['Provider_Name'].'\';';
		$head .= PHP_EOL.'$provider_email = \''.$_REQUEST['Provider_Mail'].'\';';
		if (isset($_REQUEST['Institution']) && $_REQUEST['Institution']!=NULL) 
			$head .= PHP_EOL.'$institution_name = \''.$_REQUEST['Institution'].'\';';
		if (isset($_REQUEST['Institution_Link']) && $_REQUEST['Institution_Link']!=NULL)
			$head .= PHP_EOL.'$institution_url = \''.$_REQUEST['Institution_Link'].'\';';
		if (isset($_REQUEST['Institution_Logo']) && $_REQUEST['Institution_Logo']!=NULL)
			$head .= PHP_EOL.'$institution_logo = \''.$_REQUEST['Institution_Logo'].'\';';
	
		$head .= PHP_EOL.'$species = \''.$_REQUEST['Organism'].'\';';
		if (isset($_REQUEST['Server']) && trim($_REQUEST['Server'])!=NULL)
			$head .= PHP_EOL.'$server = \''.$_REQUEST['Server'].'\';';

		$tail = PHP_EOL.'$aj2 = \''.$aj2.'\';';
		if (isset($_REQUEST['iBase']) && $_REQUEST['iBase']=='1') $tail .= PHP_EOL.'$ibase = 1;';
		$tail .= PHP_EOL.'require_once ($_SERVER[\'DOCUMENT_ROOT\'].$aj2.\'/includes/'.$_REQUEST['Include'].'\');'.PHP_EOL;

		$no = count($tables);

		if (isset($_REQUEST['Model']) && strstr($_REQUEST['Include'], '_models')!=NULL) 
		{
			$track_path = $aj2."/models/".$path; //$force=0;
		}
		else $track_path = $aj2."/tracks/".$aj_user."/".$path;
		if (!file_exists($_SERVER['DOCUMENT_ROOT'].$track_path)) mkdir ($_SERVER['DOCUMENT_ROOT'].$track_path, 0777, true);

		for ($i=0; $i<$no; $i++)
		{
			$fn = $_SERVER['DOCUMENT_ROOT'].$track_path."/".trim($files[$i],"?\r\n")."";
			if (strtolower(substr($fn,-4))!='.php') $fn .= '.php';
			if (file_exists($fn) && !$force) 
			{
				$fail_msg .= $track_path."/".$files[$i]."<br>".PHP_EOL;
				continue;
			} 	
			if (($outf = fopen($fn, "w"))!=NULL)
			{
				fprintf ($outf, "%s", $head);
				$midl = PHP_EOL.'$table = \''.trim($tables[$i]).'\';'.PHP_EOL.'$title = \''.addslashes(trim($titles[$i])).'\';'.PHP_EOL.'$info = \''.addslashes(trim($infos[$i])).'\';';
				if ($opts[$i]!='') $midl .= PHP_EOL.'$options = \''.trim($opts[$i]).'\';';
				fprintf ($outf, "%s", $midl);
				fprintf ($outf, "%s?>%s", $tail, PHP_EOL);
				fclose ($outf);
				$succ_msg .= $track_path."/".$files[$i]."<br>".PHP_EOL;
				$gn = $_SERVER['DOCUMENT_ROOT'].$track_path."/.genome_".trim($files[$i],"?\r\n")."";
				if (strtolower(substr($gn,-4))!='.php') $gn .= '.php';
				if (($outf2 = fopen($gn, "w"))!=NULL)
				{
					fprintf ($outf2, "%s", $_REQUEST['Genome_URL']);
				}
				fclose ($outf2);
			}
			else $fail_msg .= $track_path."/".$files[$i]."<br>".PHP_EOL;
		}

		$record = trim($_REQUEST['Record']);
		$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$aj_user.'/.Record_'.$record.'.db';

		if (!file_exists($fn) || $force)
		{
			if (!file_exists($_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$aj_user))
			{
				mkdir($_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$aj_user, 0775, true);
			}
			if (($outf=fopen($fn, 'w'))!=NULL)
			{
				foreach($_REQUEST as $Key => $Value)
				{
					fprintf($outf, "%s =:= %s", $Key, implode('	', explode(PHP_EOL, trim($Value))).PHP_EOL); 
				}
				fclose($outf);
			}
		}
	}
}

//Retrieve information from user default profile
if (isset($_REQUEST['Suffix'])) $fn = $_REQUEST['Suffix'];
else $fn = 'default';
retrieve_suffix($fn, 0);

?>
<head>
<link type='text/css' rel='stylesheet' href='<?PHP echo $aj2 ?>/css/util.css' />
</head>
<body>

<P>
<table border="0" width="580" height="40" align=center>
<tr height=8><td colspan=5></td></tr>
<tr><td colspan=5 align=center style="font-family: Verdana, sans-serif, Arial; font-size: 1.2em; font-weight: bold; align: center; color: #408040;">jBuilder : Build Model Track &nbsp; &nbsp; </td></tr>
</table>
<?PHP if ($err_msg!='') { ?>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Track Building Information">
<tr><td class="THL" colspan="3" height="25">&nbsp;Build Information </td></tr>
<?PHP if ($succ_msg!='') { ?>
<tr><td class="TDL" width="20%">Success:</td><td class="TDL" width="60%"><?PHP echo $succ_msg ?></td><td class="TDL"></td></tr>
<?PHP } ?>
<?PHP if ($fail_msg!='') { ?>
<tr><td class="TDL" width="20%">Failed:</td><td class="TDL" width="60%"><?PHP echo $fail_msg ?></td><td class="TDL"><?PHP err_echo('May exist'); ?></td></tr>
<?PHP } ?>
<?PHP if ($err_msg!='No Error') { ?>
<tr><td class="TDL" width="20%">Error:</td><td class="TDL" width="60%"><?PHP echo $err_msg.': '.err_echo().' marked below'; ?></td><td class="TDL"></td></tr>
<?PHP } ?>
</table>
<?PHP } ?>
<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Track Provider Information">
<tr><td class="THL" colspan="3" height="25">&nbsp;Author Information</td></tr>
<tr><td class="TDL" width="20%">Name:</td>
<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" NAME="Provider_Name" value="<?PHP aj_echo('Provider_Name'); ?>"></td><td class="TDL"><?PHP if ($err_msg=='Provider_Name') err_echo('Must be filled'); ?></td></tr>
<tr style="height:35px;valign:top;"><td class="TDL" style="align:top;">e-mail:</td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Provider_Mail" value="<?PHP aj_echo('Provider_Mail'); ?>"></td><td class="TDL"><?PHP if ($err_msg=='Provider_Mail') err_echo('Must be filled'); ?></td></tr>
<tr><td class="TDL">Institute:</td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Institution" value="<?PHP aj_echo('Institution'); ?>"><td class="TDL"></td></tr>
<tr><td class="TDL">Institute Link:</td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Institution_Link" value="<?PHP aj_echo('Institution_Link'); ?>"><td class="TDL"></td></tr>
<tr><td class="TDL">Institute Logo:</td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Institution_Logo" value="<?PHP aj_echo('Institution_Logo'); ?>"><td class="TDL"></td></tr>
</table>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Track Data Information">
<tr><td class="THL" colspan="3" height="25">&nbsp;Data Information</td></tr>
<tr><td class="TDL" width="20%">Organism: <?PHP if ($err_msg=='Organism') err_echo(); ?></td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Organism" id="x_Organism" value="<?PHP aj_echo('Organism'); ?>">
<td class="TDL"><?PHP read_genomes_x_info('organisms'); ?> </td></tr>
<tr><td class="TDL" width="20%">Server: <?PHP if ($err_msg=='Server') err_echo(); ?></td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Server" id="x_Server" value="<?PHP aj_echo('Server'); ?>">
<td class="TDL"><?PHP read_genomes_x_info('servers'); ?> </td></tr>
<tr><td class="TDL" width="20%">Table:</td>
<td class="TDL" width="60%" align=left><TEXTAREA class='CEL' NAME="Table_List" rows=2><?PHP if ($err_msg!='') aj_echo('Table_List'); ?></TEXTAREA>
<td class="TDL"><?PHP if ($err_msg=='Table_List') err_echo('Must be filled. The tables are seperated with new line.'); ?></td></tr>
<tr><td class="TDL" width="20%">Title:</td>
<td class="TDL" width="60%" align=left><TEXTAREA class='CEL' NAME="Title_List" rows=2><?PHP if ($err_msg!='') aj_echo('Title_List'); ?></TEXTAREA>
<td class="TDL"><?PHP if ($err_msg=='Title_List') err_echo('Must fil1. The titles are seperated with new line.'); else if ($err_msg=='Title_List_No') err_echo('Number of the title lines should be the same as table list.'); ?></td></tr>
<tr><td class="TDL" width="20%">Info:</td>
<td class="TDL" width="60%" align=left><TEXTAREA class='CEL' NAME="Info_List" rows=2><?PHP if ($err_msg!='') aj_echo('Info_List'); ?></TEXTAREA>
<td class="TDL"><?PHP if ($err_msg=='Info_List_No') err_echo('Number of the info lines should be 0 or the same as table list.'); ?></td></tr>
<tr><td class="TDL" width="20%">Include: <?PHP if ($err_msg=='Include') err_echo(); else if ($err_msg=='Include_Not') err_echo ('None!'); ?></td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Include" id="x_Include" value="<?PHP aj_echo('Include'); ?>">
<td class="TDL"><?PHP read_genomes_x_info('includes'); ?> </td></tr>
<tr><td class="TDL" width="20%">Options:</td>
<td class="TDL" width="60%" align=left><TEXTAREA class='CEL' NAME="Options_List" id='x_OPTIONS_LIST' rows=2><?PHP if ($err_msg!='') aj_echo('Options_List'); ?></TEXTAREA><td class="TDL"></td></tr>
</table>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Track Genome Mark">
<tr><td class="TDL" width="20%">Genome: <?PHP if ($err_msg=='Genome_URL') err_echo(); ?></td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Genome_URL" id="x_Genome_URL" value="<?PHP aj_echo('Genome_URL'); ?>">
<td class="TDL"><?PHP read_genomes_x_info('genomes'); ?> </td></tr>
</table>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Track Save Information">
<tr><td class="THL" colspan="3" height="25">&nbsp;Destination</td></tr>
<tr><td class="TDL" width="20%">Save path:</td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Path" value="<?PHP aj_echo('Path'); ?>"><td class="TDL"></td></tr>
<tr><td class="TDL" width="20%">Save as:</td>
<td class="TDL" width="60%" align=left><TEXTAREA class='CEL' NAME="File_List" rows=2><?PHP if ($err_msg!='') aj_echo('File_List'); ?></TEXTAREA><td class="TDL"></td></tr>
<tr height=30 valign="middle"><td class="TDL">Retrieve ID:</td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Record" id="x_Record" value="<?PHP aj_echo('Record'); ?>"><td class="TDL">
<select class="RCEL" NAME="Record_Options" onChange="document.getElementById('x_Record').value=this.value;">
<option value="">Choose from</option>
<?PHP
	$fn = "ls ".$_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$aj_user.'/.Record_*.db';
	$inf = popen($fn, "r");
	while($sn=fgets($inf))
	{
		$zn = trim(substr($sn, strpos($sn, ".Record_")+8));
		$en = substr($zn,0,strrpos($zn,'.db'));
		echo '<option value="'.$en.'">'.$en.'</option>'.PHP_EOL;
	}
	pclose($inf);
?>
</select> 
</td></tr>

</table>
<P>
<table border="0" width="580" cellpadding="5" align=center>
<tr><td colspan="3" style="font-family: Verdana, sans-serif, Arial; font-size: .7em; color: #404040;">
<INPUT TYPE="CHECKBOX" <?PHP if (isset($_REQUEST['Append_Assembly'])) echo 'checked'; ?> NAME="Append_Assembly" value=true> Append assembly &nbsp;&nbsp;&nbsp;&nbsp;
<INPUT TYPE="CHECKBOX" NAME="iBase" Value="1" <?PHP if (isset($_REQUEST['iBase']) && $_REQUEST['iBase']=='1') echo 'checked'; ?>> Indix Zero  &nbsp;&nbsp;&nbsp;&nbsp;
<INPUT TYPE="CHECKBOX" NAME="Force" value="1"> Force to overwrite  &nbsp;&nbsp;&nbsp;
<?PHP if (isset($_SESSION['aj_group']) && $_SESSION['aj_group'] == 'admin') { ?>
<INPUT TYPE="CHECKBOX" NAME="CORS" value="1"> Allow CORS
<INPUT TYPE="HIDDEN" NAME="Model" value="1"> 
<?PHP } ?>
</td></tr>
<tr><td height=55 valign="CENTER">
<INPUT TYPE="SUBMIT" NAME="Logout" VALUE="Logout" style="width:70px"> </td><td align="center">
<INPUT TYPE="SUBMIT" NAME="Action" VALUE="Build" style="width:90px;font-weight:bold;"> </td><td align="right">
<INPUT TYPE="SUBMIT" NAME="Action" VALUE="Retrieve" style="width:76px">
</td></tr>
</table>
</form>

