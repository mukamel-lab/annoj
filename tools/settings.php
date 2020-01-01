<?PHP
ini_set('display_errors', 'On');
include_once '../includes/global_settings.php';
require_once 'util_module.php';
require_once 'login_session2.php';

$err_no = '';
$err_msg = '';
$succ_msg = '';
$fail_msg = '';

if (isset($_REQUEST['GASSubmit']) && trim($_REQUEST['GAS'])!='')
{
	$gaf = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.google_analytics';
	$scpt = str_replace("\\", "", trim($_REQUEST['GAS']));
	file_put_contents($gaf, $scpt);
}
else if (isset($_REQUEST['VSubmit']) && trim($_REQUEST['Visitor'])!='')
{
	$vr = trim($_REQUEST['Visitor']); $ad=0;
	$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.visitors';
	if (file_exists($fn))
	{
		$vz = explode(PHP_EOL, trim(file_get_contents($fn)));
		$no = count($vz);
		for ($i=0; $i<$no; $i++)
		{
			if (trim($vz[$i])=='') { unset($vz[$i]); continue; }
                        if ($vr==trim($vz[$i]))
                        {
                                if ($_REQUEST['VSubmit']=='Delete')
                                {       $ad=-1; unset($vz[$i]);  }
                                else $ad=2;
                                break;
                        }
                }
                if ($i==$no)
                {
                        if ($_REQUEST['VSubmit']=='Delete') $ad=-2;
                        else
                        {
				$rn = user_add($vr, $aj_user, $aj_user, $_SESSION['aj_group'], '0777');
				if ($rn=='')
				{
					$vz[]=$vr; $ad=1;
				}
				else $ad=2; 
                        }
                }
        }
        else if ($_REQUEST['VSubmit']=='Add Visitor') { $vz = array(); $vz[]=$vr; $ad=1; }
        else $ad=-2;
	$outf = fopen($fn, "w");
        foreach ($vz as $th)
        {
                if (isset($th)) fprintf($outf, "%s", $th.PHP_EOL);
        }
        fclose ($outf);

        if ($ad==-2) $err_msg = "Visitor ID does not exist.";
        else if ($ad==2) $err_msg = "Visitor ID exists already.";
        else
        {
		if ($ad==1)
		{
			$err_msg = 'No Error';
			$succ_msg = "Visitor '".$_REQUEST['Visitor']."' is added.";
		}
		else if ($ad==-1)
		{
			$rn = user_del($vr, $aj_user,  $_SESSION['aj_group']);
			if ($rn=='')
			{
                		$err_msg = 'No Error';
				$succ_msg = "Visitor '".$_REQUEST['Visitor']."' is deleted.";
			}
			else $err_msg = $rn;
		}
        }

        if ($_REQUEST['VSubmit']=="Delete") $_REQUEST['SuffixSubmit']='Visitor Delete';
        else $_REQUEST['SuffixSubmit']='Visitor Add';
}
else if (isset($_REQUEST['SHDSubmit']) && trim($_REQUEST['SHD_Path'])!='')
{
	$np = trim($_REQUEST['SHD_Path']); $ad=0;
	$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$aj_user.'/.source_simple_home_directories';
	if (file_exists($fn))
	{
		$path = explode(PHP_EOL, trim(file_get_contents($fn)));
		$no = count($path);
		for ($i=0; $i<$no; $i++)
		{
			if (trim($path[$i])=='') { unset($path[$i]); continue; }
			if ($np==trim($path[$i]))
			{
				if ($_REQUEST['SHDSubmit']=='Delete') 
				{	$ad=-1; unset($path[$i]);  }
				else $ad=2;
				break; 	
			}
		}
		if ($i==$no)
		{
			if ($_REQUEST['SHDSubmit']=='Delete') $ad=-2;
			else 
			{ 	
				if (is_dir($np)) { $path[]=$np; $ad=1; }
				else ($ad=-2);
			}
		}
	}
	else if ($_REQUEST['SHDSubmit']=='Add Path') { $path = array(); $path[]=$np; $ad=1; }
	else $ad=-2;
	$outf = fopen($fn, "w");
	foreach ($path as $th)
	{
		if (isset($th)) fprintf($outf, "%s", $th.PHP_EOL);
	}
	fclose ($outf);

	if ($ad==-2) $err_msg = "Home directory does not exist.";
	else if ($ad==2) $err_msg = "Home directory exists already."; 
	else 
	{
		$err_msg = 'No Error';
		if ($ad==-1) $succ_msg = "Path '".$_REQUEST['SHD_Path']."' is deleted.";
		else if ($ad==1) $succ_msg = "Path '".$_REQUEST['SHD_Path']."' is added.";
	}

	if ($_REQUEST['SHDSubmit']=="Delete") $_REQUEST['SuffixSubmit']='Path Delete';
	else $_REQUEST['SuffixSubmit']='Path Save';
}
else if (isset($_REQUEST['SuffixSubmit']) && $_REQUEST['SuffixSubmit']=='Submit' && $_REQUEST['Suffix_File']!='')
{
	$sc=true;
	if (isset($_REQUEST['Default'])) $df = $_REQUEST['Default'];
	else $df = false;
	if (isset($_REQUEST['Provider_Name'])) $_REQUEST['Author']=$_REQUEST['Provider_Name'];
	if (isset($_REQUEST['Provider_Mail'])) $_REQUEST['Email']=$_REQUEST['Provider_Mail'];
	if (isset($_REQUEST['Suffix_File'])) $sc=save_suffix(trim($_REQUEST['Suffix_File']), $df);
	if ($sc==false) $fail_msg = 'Failed to save '.$_REQUEST['Suffix_File'].PHP_EOL;
	else { $err_msg = 'No Error'; $succ_msg = $_REQUEST['Suffix_File'].' is saved.'.PHP_EOL; $_REQUEST['SuffixSubmit']='Suffix Save'; }
}
else if (isset($_REQUEST['SuffixSubmit']) && $_REQUEST['SuffixSubmit']=='Retrieve' && $_REQUEST['Suffix_File']!='')
{
	$sc=true;
	if (isset($_REQUEST['Suffix_File'])) 
	{
		if (false==retrieve_suffix(trim($_REQUEST['Suffix_File']), true))
			$sc = retrieve_suffix('default', true);
	}
	else $sc = retrieve_suffix('default', true);
	if ($sc==false) $fail_msg = 'Failed to retrieve '.$_REQUEST['Suffix_File'].PHP_EOL;
	else { $err_msg = 'No Error'; $succ_msg = $_REQUEST['Suffix_File'].' is retrieved.'.PHP_EOL; $_REQUEST['SuffixSubmit']='Suffix Retrieve'; }
}
else if (isset($_REQUEST['SuffixSubmit']) && $_REQUEST['SuffixSubmit']=='Delete' && $_REQUEST['Suffix_File']!='')
{
	$sc=true;
	if (isset($_REQUEST['Suffix_File']) && $_REQUEST['Suffix_File']!='')
		$sc = delete_suffix($_REQUEST['Suffix_File']);
	if ($sc==false) $fail_msg = 'Failed to delete '.$_REQUEST['Suffix_File'].PHP_EOL;
        else { $err_msg = 'No Error'; $succ_msg = $_REQUEST['Suffix_File'].' is deleted.'.PHP_EOL; $_REQUEST['SuffixSubmit']='Suffix Removal'; }
}
else if (isset($_REQUEST['SSubmit']) && $_REQUEST['SID']!='')
{
	$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.allow_user_shares';
	$sid = $_REQUEST['SID']; $scl = $_REQUEST['SFD'];
	if (file_exists($fn))
        {
                $sa = explode(PHP_EOL, trim(file_get_contents($fn)));
                $no = count($sa);
                for ($i=0; $i<$no; $i++)
                {
			$en = explode("\t", $sa[$i]);
			if ($en[0]==$sid) unset($sa[$i]);
		}
	}
	if ($_REQUEST['SSubmit']=='Grant Share') $sa[] = $sid."\t".$scl;
	$sv = implode(PHP_EOL, $sa);
	file_put_contents($fn, $sv);
}
?>
<head>
	<link type='text/css' rel='stylesheet' href='<?PHP echo $aj2 ?>/css/util.css' />
</head>
<body>
<table border="0" width="580" height="40" align=center>
	<tr align=center><td style="font-family: Verdana, sans-serif, Arial; font-size: 1.15em; font-weight: bold;  color: #408040;">Settings, Preference &amp; Suffixes</td></tr>
</table>
<?PHP if ($err_msg!='') { ?>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Track Building Information">
<tr><td class="THL" colspan="3" height="25">&nbsp; <?PHP if (isset($_REQUEST['SuffixSubmit'])) echo $_REQUEST['SuffixSubmit']; ?>  </td></tr>
<?PHP if ($succ_msg!='') { ?>
<tr><td class="TDL" width="18%">Success:</td><td class="TDL" width="60%"><?PHP echo $succ_msg ?></td><td class="TDL"></td></tr>
<?PHP } ?>
<?PHP if ($fail_msg!='') { ?>
<tr><td class="TDL" width="18%">Failed:</td><td class="TDL" width="60%"><?PHP echo $fail_msg ?></td><td class="TDL"><?PHP err_echo('May exist'); ?></td></tr>
<?PHP } ?>
<?PHP if ($err_msg!='No Error') { ?>
<tr><td class="TDL" width="18%">Error:</td><td class="TDL" width="60%"><?PHP echo $err_msg ?></td><td class="TDL"></td></tr>
<?PHP } ?>
</table>
<?PHP } ?>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J User Information">
	<tr><td class="THL" colspan="3" height="25">&nbsp;Suffix Information</td></tr>
	<tr height=30 valign=bottom><td class="TDL" width="18%">Name:</td> 
		<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" NAME="Provider_Name" value="<?PHP aj_echo('Provider_Name'); ?>"></td><td class="TDL"><?PHP if ($err_msg=='Provider_Name') err_echo('Must be filled'); ?></td></tr>
	<tr><td class="TDL">e-mail:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Provider_Mail" value="<?PHP aj_echo('Provider_Mail'); ?>"></td><td class="TDL"><?PHP if ($err_msg=='Provider_Mail') err_echo('Must be filled'); ?></td></tr>
	<tr height=36 valign=bottom><td class="TDL">Institution:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Institution" value="<?PHP aj_echo('Institution'); ?>"><td class="TDL"></td></tr>
	<tr><td class="TDL">Institution Link:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Institution_Link" value="<?PHP aj_echo('Institution_Link'); ?>"><td class="TDL"></td></tr>
	<tr><td class="TDL">Institution Logo:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Institution_Logo" value="<?PHP aj_echo('Institution_Logo'); ?>"><td class="TDL"></td></tr>
	<tr height=30 valign=bottom><td class="TDL" width="18%">Organism: <?PHP if ($err_msg=='Organism') err_echo(); ?></td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Organism" id="x_Organism" value="<?PHP aj_echo('Organism'); ?>"></td>
		<td class="TDL"><?PHP read_genomes_x_info('organisms'); ?></td>
	</tr>

	<tr><td class="TDL" width="18%">Genome URL: <?PHP if ($err_no=='Genome_URL') echo $err_star; ?></td><td class="TDL" width="60%"><INPUT class="CEL" TYPE="text" id="x_Genome_URL" NAME="Genome_URL" VALUE="<?PHP if (isset($_REQUEST['Genome_URL'])) echo $_REQUEST['Genome_URL']; ?>"></td>
		<td class="TDL"><?PHP read_genomes_x_info('genomes'); ?>
	</td></tr>
        <tr><td class="TDL" width="18%">Bookmarks URL: <?PHP if ($err_no=='Bookmarks_URL') echo $err_star; ?></td><td class="TDL" width="60%"><INPUT class="CEL" TYPE="text" id="x_Bookmarks_URL" NAME="Bookmarks_URL" VALUE="<?PHP if (isset($_REQUEST['Bookmarks_URL'])) echo $_REQUEST['Bookmarks_URL']; ?>"></td>
                <td class="TDL"><?PHP read_genomes_x_info('bookmarks'); ?>
        </td></tr>
        <tr><td class="TDL" width="18%">Analysis URL: <?PHP if ($err_no=='Analysis_URL') echo $err_star; ?></td><td class="TDL" width="60%"><INPUT class="CEL" TYPE="text" id="x_Analysis_URL" NAME="Analysis_URL" VALUE="<?PHP if (isset($_REQUEST['Analysis_URL'])) echo $_REQUEST['Analysis_URL']; ?>"></td>
                <td class="TDL"><?PHP read_genomes_x_info('analyses'); ?>
        </td></tr>

	<tr height=36 valign=bottom><td class="TDL" width="18%">Location:</td><td class="TDL" colspan="2">
		 Chr: &nbsp;<INPUT class="CEL" TYPE="text" style="width:20px;text-align:right" NAME="Assembly" VALUE="<?PHP if (isset($_REQUEST['Assembly'])) echo $_REQUEST['Assembly']; else echo "1"; ?>">
		&nbsp;&nbsp; Pos: &nbsp;<INPUT class="CEL" TYPE="text" style="width:65px;text-align:right" NAME="Position" VALUE="<?PHP if (isset($_REQUEST['Position'])) echo $_REQUEST['Position']; else echo "1"; ?>">
		&nbsp;&nbsp; bases: &nbsp;<INPUT class="CEL" TYPE="text" style="width:30px;text-align:right" NAME="Bases" VALUE="<?PHP if (isset($_REQUEST['Bases'])) echo $_REQUEST['Bases']; else echo "20"; ?>">
		&nbsp;&nbsp; Pixels: &nbsp;<INPUT class="CEL" TYPE="text" style="width:20px;text-align:right" NAME="Pixels" VALUE="<?PHP if (isset($_REQUEST['Pixels'])) echo $_REQUEST['Pixels']; else echo "1"; ?>">
	</td></tr>
	<tr><td class="TDL" width="18%">Settings:</td><td class="TDL" colspan="2">
		Yaxis: &nbsp;<INPUT class="CEL" TYPE="text" style="width:25px;text-align:right" NAME="Yaxis" VALUE="<?PHP if (isset($_REQUEST['Yaxis'])) echo $_REQUEST['Yaxis']; else echo "20"; ?>">
		&nbsp;&nbsp; Baseline: &nbsp;<INPUT class="CEL" TYPE="text" style="width:25px;text-align:right" NAME="Baseline" VALUE="<?PHP if (isset($_REQUEST['Baseline']) && $_REQUEST['Baseline']=='1') echo '1'; else echo "0"; ?>">
	</td></tr>

	<tr><td class="TDL" width="18%">Priority:</td><td class="TDL" colspan="2">&nbsp;
		<INPUT TYPE="radio" NAME="Priority" VALUE="Public" <?PHP if (!isset($_REQUEST['Priority']) || $_REQUEST['Priority']=='Public') echo 'checked'; ?>> Public &nbsp;&nbsp;
		<INPUT TYPE="radio" NAME="Priority" VALUE="Protected" <?PHP if (isset($_REQUEST['Priority']) && $_REQUEST['Priority']=='Protected') echo 'checked'; ?>> Protected &nbsp;
		<INPUT TYPE="radio" NAME="Priority" VALUE="Private" <?PHP if (isset($_REQUEST['Priority']) && $_REQUEST['Priority']=='Private') echo 'checked'; ?>> Private
	</td></tr>
	<tr height=32 valign=top><td class="TDL" width="18%" height="30">Suffix Name: <?PHP if ($err_no=='Suffix_File') echo $err_star; ?></td><td class="TDL" width="60%"><INPUT class="CEL" TYPE="text" id="x_Suffix_File" NAME="Suffix_File" id="x_Suffix_File" VALUE="<?PHP if (isset($_REQUEST['Suffix_File'])) echo $_REQUEST['Suffix_File']; ?>"> </td><td class="TDL">
	<select class="RCEL" NAME="Suffix_File_Options" onChange="document.getElementById('x_Suffix_File').value=this.value;">
		<option value="">Choose from</option>
<?PHP
        $fn = "ls ".$_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.suffix_*.info';
        $inf = popen($fn, "r");
        while($sn=fgets($inf))
        {
                $zn = trim(substr($sn, strrpos($sn,'/')+9));
                $en = substr($zn,0,strrpos($zn,'.info')); 	//.'.php';
                echo '<option value="'.$en.'">'.$en.'</option>'.PHP_EOL;
        }
        pclose($inf);
?>
</select>

</td></tr>

</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
	<tr><td width="30%" align="left"> 
		<INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_Suffix_File').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete suffix \''+usr+'\' ?'; var r = confirm(cf); if (!r) return false; }" Name="SuffixSubmit" value="Delete" style="width:70px;height:22px;font-weight:regular">
		</td><td align="center" width="40%">
		&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="SuffixSubmit" value="Submit" style="width:100px;height:25px;font-weight:bold">&nbsp; &nbsp;
		</td><td align="right" width="30%">
		<INPUT TYPE="SUBMIT" Name="SuffixSubmit" value="Retrieve" style="width:80px;height:22px;font-weight:regular">
	</td></tr>
</table>
</form>
<form method=POST>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J User Information">
        <tr><td class="THL" colspan="3" height="25">&nbsp;Simple Home Directory</td></tr>
        <tr height=36 valign=middle><td class="TDL" width="18%">Directory:</td>
                <td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="SHD_Path" id="x_SHD_Path" value="<?PHP aj_echo('SHD_Path'); ?>"></td><td class="TDL"> 
	 <select class="RCEL" NAME="SHD_Options" onChange="document.getElementById('x_SHD_Path').value=this.value;">
                <option value="">Choose from</option>
<?PHP
        $fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$aj_user.'/.source_simple_home_directories';
	if (file_exists($fn))
	{
		$inf = fopen($fn, "r");
		while($sn=fgets($inf))
		{
			$en = trim($sn);
			echo '<option value="'.$en.'">'.$en.'</option>'.PHP_EOL;
		}
		fclose($inf);
	}
?>
</select>
	</td></tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
        <tr><td width="30%" align="left">
		<INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_SHD_Path').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete Simple path \''+usr+'\' ?'; var r = confirm(cf); if (!r) return false; }" Name="SHDSubmit" value="Delete" style="width:70px;height:22px;font-weight:regular">
                </td><td align="center" width="40%">
                &nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="SHDSubmit" value="Add Path" style="width:100px;height:25px;font-weight:bold">&nbsp; &nbsp;
                </td><td align="right" width="30%">
        </td></tr>
</table>
</form>

<form method=POST>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J User Information">
        <tr><td class="THL" colspan="3" height="25">&nbsp;Add Visitors</td></tr>
        <tr height=36 valign=middle><td class="TDL" width="18%">Visitor ID:</td>
                <td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Visitor" id="x_Visitor" value="<?PHP aj_echo('Visitor'); ?>"></td><td class="TDL">
	<select class="RCEL" NAME="SHD_Options" onChange="document.getElementById('x_Visitor').value=this.value;">
                <option value="">Choose from</option>
<?PHP
        $fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.visitors';
        if (file_exists($fn))
        {
                $inf = fopen($fn, "r");
                while($sn=fgets($inf))
                {
                        $en = trim($sn);
                        echo '<option value="'.$en.'">'.$en.'</option>'.PHP_EOL;
                }
                fclose($inf);
        }
?>
</select>

</td></tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
        <tr><td width="30%" align="left">
                <INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_Visitor').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete visitor \''+usr+'\' ?'; var r = confirm(cf); if (!r) return false; }" Name="VSubmit" value="Delete" style="width:70px;height:22px;font-weight:regular">
                </td><td align="center" width="40%">
                &nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="VSubmit" value="Add Visitor" style="width:100px;height:25px;font-weight:bold">&nbsp; &nbsp;
                </td><td align="right" width="30%">
        </td></tr>
</table>
</form>

<form method=POST>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J User Sharing">
        <tr><td class="THL" colspan="3" height="25">&nbsp;Grant Sharing Priority</td></tr>
        <tr height=36 valign=middle><td class="TDL" width="18%">User/Group ID:</td>
                <td class="TDL" width="60%" align=left>
		<nobr><INPUT class='CEL' TYPE="TEXT" NAME="SID" id="x_SID" value="<?PHP aj_echo('SID'); ?>" style="width:300px;">&nbsp; 
		<select class="RCEL" NAME="SFD" id="x_SFD" value="<?PHP aj_echo('SFD'); ?>" style="width:85px;">
		<option value="PagesTracks">Pages & Tracks</option>
		<option value="PagesOnly">Pages Only</option>
		</select>
		</nobr>
	</td><td class="TDL">
        <select class="RCEL" NAME="SID_Options" onChange="document.getElementById('x_SID').value=this.value;">
                <option value="">Choose from</option>
<?PHP
        $fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.allow_user_shares';
        if (file_exists($fn))
        {
                $inf = fopen($fn, "r");
                while($sn=fgets($inf))
                {
                        $en = explode("\t", trim($sn));
                        echo '<option value="'.$en[0].'">'.$en[0].' - '.$en[1].' </option>'.PHP_EOL;
                }
                fclose($inf);
        }
?>
</select>

</td></tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
        <tr><td width="30%" align="left">
                <INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_SID').value; if (usr=='') return false; else {  var cf = 'Are you sure to remove share priority of \''+usr+'\' ?'; var r = confirm(cf); if (!r) return false; }" Name="SSubmit" value="Delete" style="width:70px;height:22px;font-weight:regular">
                </td><td align="center" width="40%">
                &nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="SSubmit" value="Grant Share" style="width:100px;height:25px;font-weight:bold">&nbsp; &nbsp;
                </td><td align="right" width="30%">
        </td></tr>
</table>
</form>

<form method=POST>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Google Analytics">
        <tr><td class="THL" colspan="3" height="25">&nbsp;Google Analytics</td></tr>
        <tr height=36 valign=middle><td class="TDL" width="18%">JavaScript:</td>
                <td class="TDL" width="60%" align=left>
		<textarea class="CEL" rows=10 NAME="GAS" id="x_GAS"><?PHP
		$gaf = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.google_analytics';
		if (file_exists($gaf))
		{
			echo file_get_contents($gaf);
		}
		?></textarea>
        </td><td class="TDL">
</td></tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
        <tr><td width="30%" align="left">
	</td><td align="center" width="40%">
                &nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" onClick="javascript: if (document.getElementById('x_GAS').value=='') return false;" Name="GASSubmit" value="Upload Script" style="width:100px;height:25px;font-weight:bold">&nbsp; &nbsp;
                </td><td align="right" width="30%">
        </td></tr>
</table>
</form>

<P>
<br>
<br>
