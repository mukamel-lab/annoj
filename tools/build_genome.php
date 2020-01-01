<?PHP
ini_set('display_errors', 'On');
require_once 'util_module.php';
require_once 'login_session3.php';

$err_no = '';
$err_msg = '';
$succ_msg = '';
$fail_msg = '';

if (isset($_REQUEST['Action']) && $_REQUEST['Action']=='Build Genome')
{
	$institution = trim($_REQUEST['Institution']);
	$institution_link = trim($_REQUEST['Institution_Link']);
	$institution_logo = trim($_REQUEST['Institution_Logo']);
	$name = trim($_REQUEST['Provider_Name']);
	$email = trim($_REQUEST['Provider_Mail']);
	$title = trim($_REQUEST['Title']);
	$version =  trim($_REQUEST['Version']);
	$description = trim($_REQUEST['Description']);
	$genome_url = trim($_REQUEST['Genome_URL']);
	$genome_dir = trim($_REQUEST['Genome_Dir']);
	$force = (isset($_REQUEST['Force']))?$_REQUEST['Force']:0;
	$cors = (isset($_REQUEST['CORS']))?$_REQUEST['CORS']:0;

	if ($err_msg=='' & $institution=='') { $err_no = 'Institution'; $err_msg = 'Institution is empty '; }
	if ($err_msg=='' & $title=='') { $err_no = 'Title'; $err_msg = 'Genome title is empty '; }
	if ($err_msg=='' & $version=='') { $err_no = 'Version'; $err_msg = 'Genome version is empty '; }
	if ($err_msg=='' & $description=='') { $err_no = 'Description'; $err_msg = 'Genome description is empty '; }
	if ($err_msg=='' & $genome_dir=='') { $err_no = 'Genome_Dir'; $err_msg = 'Genome_Dir is empty '; }
	if ($err_msg=='' & $genome_url=='') { $err_no = 'Genome_URL'; $err_msg = 'Save as is empty '; }

	if ($err_msg=='')
	{
		$assm = ''; 
		$fn = 'ls '.$genome_dir.'*';
		$inf = popen($fn, 'r');
		while ($sn=trim(fgets($inf)))
		{
			if ($sn[0]=='.') continue;
			if (file_exists($sn) && !is_dir($sn))
			{
				$fs = stat($sn);
				$assm .= "\t\t\t\tarray( 'id' => '".str_replace($genome_dir, "", $sn)."', 'size' => ".$fs['size']." ),".PHP_EOL;
			}
		}
		pclose($inf);

		if ($assm!='')
		{
			$gn = "<?PHP".PHP_EOL."require_once '../includes/common.php';".PHP_EOL.PHP_EOL;
			if ($cors) $gn .= http_cors().PHP_EOL;
			$gn .= "if (".'$'."action=='syndicate')".PHP_EOL."{".PHP_EOL."\t".'$'."genome = array(".PHP_EOL;
			$gn .= "\t\t'institution' => array(".PHP_EOL;
			$gn .= "\t\t\t'name' => '".$institution."',".PHP_EOL;
			$gn .= "\t\t\t'url' => '".$institution_link."',".PHP_EOL;
			$gn .= "\t\t\t'logo' => '".$institution_logo."'".PHP_EOL;
			$gn .= "\t\t),".PHP_EOL."\t\t'engineer' => array(".PHP_EOL;
			$gn .= "\t\t\t'name' => '".$name."',".PHP_EOL;
			$gn .= "\t\t\t'email' => '".$email."',".PHP_EOL;
			$gn .= "\t\t),".PHP_EOL."\t\t'service' => array(".PHP_EOL;
			$gn .= "\t\t\t'title' => '".$title."',".PHP_EOL;
			$gn .= "\t\t\t'version' => '".$version."',".PHP_EOL;
			$gn .= "\t\t\t'description' => '".$description."',".PHP_EOL;
			$gn .= "\t\t),".PHP_EOL."\t\t'genome' => array(".PHP_EOL;
			$gn .= "\t\t\t'assemblies' => array(".PHP_EOL;
			$gn .= $assm;
			$gn .= "\t\t\t)".PHP_EOL."\t\t)".PHP_EOL."\t);".PHP_EOL."\trespond(".'$'."genome);".PHP_EOL."}".PHP_EOL;
			$gn .= PHP_EOL.'$'."genome_dir = '".$genome_dir."';".PHP_EOL;
			$gn .= '$'."ibase = 1;".PHP_EOL.PHP_EOL."include_once '../includes/common_genome.php';".PHP_EOL;
			$gn .= PHP_EOL."error('Invalid action requested: '.".'$'."action);".PHP_EOL."?>".PHP_EOL;

			$err_msg = 'No Error';
			if (!file_exists('../genomes/'.$genome_url) || $force)
			{
				if (file_put_contents('../genomes/'.$genome_url, $gn))
					$succ_msg = 'genome '.$genome_url.' is saved '; 
				else $fail_msg = 'genome '.$genome_url.' is failed ';
			}
			else $fail_msg = 'genome '.$genome_url.' exists. Please check force to overwrite it ';
		}
		else
		{
			$err_msg = 'No assemblies info retrieved ';
		}
	}
}

?>
<head>
<link type='text/css' rel='stylesheet' href='<?PHP echo $aj2 ?>/css/util.css' />
</head>
<body>

<P>
<table border="0" width="580" height="40" align=center>
<tr height=8><td colspan=5></td></tr>
<tr><td colspan=5 align=center style="font-family: Verdana, sans-serif, Arial; font-size: 1.2em; font-weight: bold; align: center; color: #408040;">jBuilder : Build Genome &nbsp; &nbsp; </td></tr>
</table>
<?PHP if ($err_msg!='') { ?>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Genome Building Information">
<tr><td class="THL" colspan="3" height="25">&nbsp;Build Genome </td></tr>
<?PHP if ($succ_msg!='') { ?>
<tr><td class="TDL" width="20%">Success:</td><td class="TDL" width="60%"><?PHP err_echo();echo $succ_msg ?></td><td class="TDL"></td></tr>
<?PHP } ?>
<?PHP if ($fail_msg!='') { ?>
<tr><td class="TDL" width="20%">Failed:</td><td class="TDL" width="60%"><?PHP err_echo(); echo $fail_msg ?></td><td class="TDL"> </td></tr>
<?PHP } ?>
<?PHP if ($err_msg!='No Error') { ?>
<tr><td class="TDL" width="20%">Error:</td><td class="TDL" width="60%"><?PHP echo $err_msg.': '.err_echo().' marked below'; ?></td><td class="TDL"></td></tr>
<?PHP } ?>
</table>
<?PHP } ?>
<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Genome Provider Information">
<tr><td class="THL" colspan="3" height="25">&nbsp;Genome Source</td></tr>
<tr><td class="TDL" width="20%">Name:</td>
<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" NAME="Provider_Name" value="<?PHP aj_echo('Provider_Name'); ?>"></td><td class="TDL"><?PHP if ($err_msg=='Provider_Name') err_echo('Must be filled'); ?></td></tr>
<tr style="height:35px;valign:top;"><td class="TDL" style="align:top;">e-mail:</td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Provider_Mail" value="<?PHP aj_echo('Provider_Mail'); ?>"></td><td class="TDL"><?PHP if ($err_msg=='Provider_Mail') err_echo('Must be filled'); ?></td></tr>
<tr><td class="TDL">Institution: <?PHP if ($err_no=='Institution') err_echo(); ?></td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Institution" value="<?PHP aj_echo('Institution'); ?>"><td class="TDL"></td></tr>
<tr><td class="TDL">Institution Link:</td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Institution_Link" value="<?PHP aj_echo('Institution_Link'); ?>"><td class="TDL"></td></tr>
<tr><td class="TDL">Institution Logo:</td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Institution_Logo" value="<?PHP aj_echo('Institution_Logo'); ?>"><td class="TDL"></td></tr>
</table>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Genome Information">
<tr><td class="THL" colspan="3" height="25">&nbsp;Genome Service </td></tr>
<tr><td class="TDL" width="20%">Title:<?PHP if ($err_no=='Title') err_echo(); ?></td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Title" id="x_Organism" value="<?PHP aj_echo('Title'); ?>">
<td class="TDL"><?PHP read_genomes_x_info('organisms'); ?> </td></tr>
<tr><td class="TDL" width="20%">Version: <?PHP if ($err_no=='Version') err_echo(); ?></td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Version" id="x_Version" value="<?PHP aj_echo('Version'); ?>">
<td class="TDL"> </td></tr>
<tr><td class="TDL" width="20%">Description: <?PHP if ($err_no=='Description') err_echo(); ?></td>
<td class="TDL" width="60%" align=left><TEXTAREA class='CEL' NAME="Description" rows=4><?PHP aj_echo('Description'); ?></TEXTAREA>
<td class="TDL"> </td></tr>
<tr><td class="TDL" height="30px" width="20%">Genome Path: <?PHP if ($err_no=='Genome_Dir') err_echo(); ?></td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Genome_Dir" id="x_Genome_Dir" value="<?PHP aj_echo('Genome_Dir'); ?>">
</td><td class="TDL"> </td></tr> </table>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Genome Save Information">
<tr><td class="THL" colspan="3" height="25">&nbsp;Destination</td></tr>
<tr><td class="TDL" width="20%">Save as:  <?PHP if ($err_no=='Genome_URL') err_echo(); ?></td>
<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Genome_URL" id="x_Genome_URL" VALUE="<?PHP aj_echo('Genome_URL'); ?>"></td><td class="TDL">
<?PHP
	$fn = 'ls ../genomes'; $inf = popen($fn, 'r');
	echo '<select class="CEL" name="Genome_URL_Options" style="width:90px;" onChange="document.getElementById(\'x_Genome_URL\').value=this.value; ">'.PHP_EOL.'<option value="">Choose from'.PHP_EOL;
	while($sn=fgets($inf)) echo '<option value="'.trim($sn).'">'.trim($sn).PHP_EOL;
	echo '</select>'.PHP_EOL;
	pclose($inf);
?>
</td></tr>
<tr><td class="TDL" width="20%"> </td><td class="TDL" width="60%" align=left style="height: 30px; font-family: Verdana, sans-serif, Arial; font-size: .7em; color: #404040;">
<INPUT TYPE="CHECKBOX" NAME="Force" value="1"> Force to overwrite &nbsp; &nbsp; &nbsp;
<INPUT TYPE="CHECKBOX" NAME="CORS" value="1"> Allow CORS
</td><td class="TDL"> </td></tr>
</table>
<table border="0" width="580" cellpadding="5" align=center>
<tr><td height=55 valign="CENTER">
<INPUT TYPE="SUBMIT" NAME="Logout" VALUE="Logout" style="width:70px"> </td><td align="center">
<INPUT TYPE="SUBMIT" NAME="Action" VALUE="Build Genome" style="width:130px;font-weight:bold;"> 
</td><td align="right" width="25%"> &nbsp;  </td></tr>
</table>
</form>
<P><br><br>
