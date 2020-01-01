<?PHP

ini_set('display_errors', 'On');
include_once '../includes/global_settings.php';
require_once 'util_module.php';
require_once 'login_session2.php';
$err_no = '';
$err_msg = '';
$succ_msg = '';
$fail_msg = '';

if (isset($_REQUEST['CORSSubmit']) && $_REQUEST['CORSSubmit']=='Submit')
{
	$url = trim($_REQUEST['Page_URL']);
	$ua = parse_url($url);
	if ($_REQUEST['Target_Type']=='CORS')
	{
		if (strncmp($url, "http://", 7)!=0)
			$err_msg = $_REQUEST['Target_Type']." source url must begin with http://";
	}
	else if ($_REQUEST['Target_Type']=='Locale')
	{
		if (strncmp($url, "/", 1)!=0 && strncmp($url, "http://", 7)!=0)
			$err_msg = 'Locale source url must begin with / or http://';
		if ($err_msg == '')
		{
			$ua = parse_url($url);
			if (isset($ua['host']) && $ua['host']!=$_SERVER['HTTP_HOST'])
				$err_msg = 'URL is not a locale source';
			if (isset($ua['path']))
				$url = $_SERVER['DOCUMENT_ROOT'].$ua['path'];
			if (!isset($_REQUEST['Page_Name']) || $_REQUEST['Page_Name']=='')
				$err_msg = 'Page name is empty';
		}
	}
	$_REQUEST['CORSSubmit'] .= " ".$_REQUEST['Target_Type']." Source";
	if ($err_msg == '')
	{
		$se = efile_get_contents($url); 
		$str = $se['str'];
		if (isset($se['err'])) $err_msg = $se['err'];
	}
	if ($err_msg == '' && $str == '') $err_msg = 'Source content is empty';	
	if ($err_msg == '' && ($str=js_2_json($str)) == '')
	{ 
		$str = js_2_json(pg_2_js ($se['str'], $ua, $_REQUEST['Target_Type']));
		if ($str=='') $err_msg = 'Source is not a valid AnnoJ.config source';
	}
	if ($err_msg == '' && ($ad=json_decode(utf8_encode($str), true))==null)
		$err_msg = 'Syntax error in source AnnoJ.config';
	echo $err_msg;
	if ($err_msg == '')
	{
		$HT = js_2_db_tracks ($ad);
		$HX = js_2_db_suffix ($ad);

		if (isset($ua['scheme'])) $host = $ua['scheme']."://".$ua['host'];
		$path = substr($ua['path'], 0, 1+strrpos($ua['path'],"/"));
		$n = count($HT);

		if ($_REQUEST['Target_Type']=="Locale")
		{
			$m=0; 
			if (isset($_REQUEST['Create_New']) && $_REQUEST['Create_New']) $m=1;
			$ownPath = $aj2."/tracks/".$aj_user;
			for ($i=0; $i<$n; $i++)
			{
				$ub = parse_url($HT[$i]['Track_URL']);
				$ubp = trim($ub['path']);
				if ($ubp[0]!='/')
					$HT[$i]['Track_URL']=clean_url_parent($path."/".$ub['path']);
				else $HT[$i]['Track_URL']=clean_url_parent($ub['path']);
				if (!isset($HT[$i]['Table_List']) || $HT[$i]['Table_List']=='')
				{
					$fn = $_SERVER['DOCUMENT_ROOT'].$HT[$i]['Track_URL'];
					$FT = getTableInfo ($fn);
					$HT[$i]['Append_Assembly']=$FT['Append'];
					$HT[$i]['iBase']=$FT['ibase'];
					if (isset($FT['Server'])) $HT[$i]['Server']=$FT['Server'];
					if (isset($FT['Table'])) $HT[$i]['Table_List']=$FT['Table'];
					if (isset($FT['Title'])) $HT[$i]['Title_List']=$FT['Title'];
					if (isset($FT['Info'])) $HT[$i]['Info_List']=$FT['Info'];
					if (isset($FT['Options'])) $HT[$i]['Option_List']=$FT['Options'];
					if (isset($FT['Include'])) $HT[$i]['Include']=$FT['Include'];
					if (isset($FT['Provider_Name']) && $FT['Provider_Name']!='') 
						$HX['Provider_Name'] = $FT['Provider_Name'];
					if (isset($FT['Provider_Mail']) && $FT['Provider_Mail']!='') 
                                                $HX['Provider_Mail'] = $FT['Provider_Mail'];
					if (isset($FT['Organism']) && $FT['Organism']!='') 
                                                $HX['Organism'] = $FT['Organism'];
					if ($m==1)
					{
						$HT[$i]['Track_URL'] = $ownPath."/".$_REQUEST['Page_Name']."/".str_replace(" ", "_", $HT[$i]['Track_Path'])."/".substr($HT[$i]['Track_URL'], strrpos($HT[$i]['Track_URL'], "/")+1);
						if (isset($HT[$i]['Server']) && $HT[$i]['Server']!='')
							$HT[$i]['Data_Type']='MySQLData';
						else $HT[$i]['Data_Type']='SimpleData';
					}
				}
			}
			$HT[0]['Node_Path']=$_REQUEST['Page_Name']."/";
			echo $HT[0]['Node_Path'];
			saveConfigDB ($HT, $HX);
			$err_msg = "No Error";
			$succ_msg = "Local source is converted to ".$_REQUEST['Page_Name'].".";
		}
		else if ($_REQUEST['Target_Type']=="CORS")
		{
			for ($i=0; $i<$n; $i++)
			{
				$ub = parse_url($HT[$i]['Track_URL']);
				if (isset($ub['scheme']) && isset($ub['host'])) continue;
				$ubp = trim($ub['path']);
				if ($ubp[0]!='/')
					$HT[$i]['Track_URL']=clean_url_parent($host.$path.$ub['path']);
				else $HT[$i]['Track_URL']=clean_url_parent($host.$ub['path']);
			}
			$ub = parse_url($HX['Genome_URL']);
			if (!(isset($ub['scheme']) && isset($ub['host'])))
			{
				$ubp = trim($ub['path']);
				if ($ubp[0]!='/')
					$HX['Genome_URL']=clean_url_parent($host.$path.$ub['path']);
				else $HX['Genome_URL']=clean_url_parent($host.$ub['path']); 
			}
			$HT[0]['Node_Path']=$_REQUEST['Page_Name']."/";
			$HX['Notes']="CORS source: ".$_REQUEST['Page_URL']; 
			saveConfigDB ($HT, $HX, "CORS");
			$err_msg = "No Error";
			$succ_msg = "CORS source ".$_REQUEST['Page_Name']." is created.";
		}
	}
}
?>
<html>
<head>
<link type='text/css' rel='stylesheet' href='<?PHP echo $aj2 ?>/css/util.css' />
</head>
<body>
<table border="0" width="600" height="40" align=center>
        <tr align=center><td style="font-family: Verdana, sans-serif, Arial; font-size: 1.15em; font-weight: bold;  color: #408040;">Build CORS Sources</td></tr>
</table>
<?PHP if ($err_msg!='') { ?>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Tools Information">
<tr><td class="THL" colspan="3" height="25">&nbsp; <?PHP if (isset($_REQUEST['CORSSubmit'])) echo $_REQUEST['CORSSubmit']; ?>  </td></tr>
<?PHP if ($succ_msg!='') { ?>
<tr><td class="TDL" width="16%">Success:</td><td class="TDL" width="60%"><?PHP echo $succ_msg ?></td><td class="TDL"></td></tr>
<?PHP } ?>
<?PHP if ($fail_msg!='') { ?>
<tr><td class="TDL" width="16%">Failed:</td><td class="TDL" width="60%"><?PHP echo $fail_msg ?></td><td class="TDL"></td></tr>
<?PHP } ?>
<?PHP if ($err_msg!='No Error') { ?>
<tr><td class="TDL" width="16%">Error:</td><td class="TDL" width="60%"><?PHP echo $err_msg." ".err_echo()." "; ?></td><td class="TDL"></td></tr>
<?PHP } ?>
</table>
<?PHP } ?>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" style="width:700px;" cellspacing="5" border="0" align=center>
<tr><td class="THL" colspan="3" height="25">&nbsp;Cross Origin / Locale Resource:</td></tr>
<tr height=40 valign=middle><td class="TDL" style="width:70px;">Page URL:</td>
<td class="TDL" align=left colspan="2">
<INPUT class='CEL' style="width:500px;align:left;" TYPE="TEXT" NAME="Page_URL" id="x_Page_URL" value="<?PHP if (isset($_REQUEST['Page_URL'])) echo $_REQUEST['Page_URL']; ?>">
</td></tr>
<tr height=40 valign=middle><td class="TDL" colspan="3" width="100%">
<nobr>
<INPUT class='CEL' style="width:12px;" TYPE="RADIO" id="x_TT2" NAME="Target_Type" value="CORS" <?PHP if (isset($_REQUEST['Target_Type']) && 'CORS' == $_REQUEST['Target_Type']) echo 'checked'; ?>> CORS &nbsp;&nbsp;
<INPUT class='CEL' style="width:12px;" TYPE="RADIO" id="x_TT3" NAME="Target_Type" value="Locale" <?PHP if (isset($_REQUEST['Target_Type']) && 'Locale' == $_REQUEST['Target_Type']) echo 'checked'; ?>> Locale &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
<span id='x_options' name='options'>Page Name: &nbsp; <INPUT class='CEL' style='width:297px;' TYPE='TEXT' NAME='Page_Name' id="x_Page_Name" value="<?PHP if (isset($_REQUEST['Page_Name'])) echo $_REQUEST['Page_Name']; ?>"> &nbsp;&nbsp; <INPUT class='CEL' style='width:12px;' TYPE='CHECKBOX' NAME='Create_New' value='1' <?PHP if (isset($_REQUEST['Create_New']) && '1' == $_REQUEST['Create_New']) echo 'checked'; ?>> New</span></nobr>
</td></tr></table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
<tr><td width="30%" align="left"> &nbsp; </td><td align="center" width="40%">
&nbsp; &nbsp; &nbsp; <INPUT onClick="javascript: var usr=document.getElementById('x_Page_URL').value; var ttype=''; if (document.getElementById('x_TT2').checked) ttype=document.getElementById('x_TT2').value; if (document.getElementById('x_TT3').checked) ttype=document.getElementById('x_TT3').value; var pn=document.getElementById('x_Page_Name').value; if (usr=='' || ttype=='' || pn=='') return false;" TYPE="SUBMIT" Name="CORSSubmit" value="Submit" style="width:120px;height:25px;font-weight:bold">&nbsp; &nbsp;
</td><td align="right" width="30%">
</td></tr>
</table>
</form>

<P>
<br>
</body>
</html>
