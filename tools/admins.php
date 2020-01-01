<?PHP
ini_set('display_errors', 'On');
include_once '../includes/global_settings.php';
require_once 'util_module.php';
require_once 'login_session3.php';
$err_no = '';
$err_msg = '';
$succ_msg = '';
$fail_msg = '';

if (isset($_REQUEST['UserSubmit']) && $_REQUEST['UserSubmit']=='Reset Password')
{
	$user = '';
	if (isset($_REQUEST['User_ID'])) $user=$_REQUEST['User_ID'];
	if ($user!='')
	{
		$rn = user_del2 ($user, $aj_user, $_SESSION['aj_group']);
	}
	$_REQUEST['SuffixSubmit']='Reset password';
	if ($rn=='')
	{
		$err_msg = 'No Error'; $succ_msg = "User ".$user."'s password is reset.";
	}
	else $err_msg = $rn;
}
else if (isset($_REQUEST['UserSubmit']) && $_REQUEST['UserSubmit']=='Delete')
{
	$user = '';
	if (isset($_REQUEST['User_ID'])) $user=$_REQUEST['User_ID'];
	if ($user!='')
	{
		$rn = user_del2 ($user, $aj_user, $_SESSION['aj_group']);
		$rn .= user_del ($user, $aj_user, $_SESSION['aj_group']);
		if ($rn=='')
		{
			$td = $_SERVER['DOCUMENT_ROOT'].$aj2.'/trashes/'.$user;
			if (!file_exists($td)) mkdir($td, 0700, true);
			if (file_exists($_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$user))
				rename($_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$user, $td.'/suffix');
			if (file_exists($_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$user))
				rename($_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$user, $td.'/pages');
			if (file_exists($_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$user))
				rename($_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$user, $td.'/tracks');
			else $rn = "User ".$user." might not exist.";
		}
	}
	else $rn = "User ID is empty.";
	$_REQUEST['SuffixSubmit']='Delete User';
	if ($rn=='')
	{
		$err_msg = 'No Error'; $succ_msg = "User ".$user." is deleted.";
	}
	else $err_msg = $rn;
}
else if (isset($_REQUEST['UserSubmit']) && $_REQUEST['UserSubmit']=='Add User')
{
	$user = $name = $email = $group = $priority = '';
	if (isset($_REQUEST['User_ID'])) $user=$_REQUEST['User_ID'];
	if (isset($_REQUEST['User_Name'])) $name=$_REQUEST['User_Name'];
	if (isset($_REQUEST['User_Mail'])) $email=$_REQUEST['User_Mail'];
	if (isset($_REQUEST['Group'])) $group=$_REQUEST['Group'];
	if (isset($_REQUEST['Priority'])) $priority = $_REQUEST['Priority'];
	if ($user!='' && $name!='' && $email!='' && $group!='' && $priority!='')
		$rn = user_add ($user, $name, $email, $group, $priority);
	else $rn = "At least one of fields is empty.";

	$_REQUEST['SuffixSubmit']='Add User';
	if ($rn=='') 
	{
		add_group_share ($user, $group, $priority);
		$err_msg = 'No Error'; $succ_msg = "User ".$user." is created.";
	}
	else $err_msg = $rn;
}
else if (isset($_REQUEST['Submit']))
{
	if (isset($_REQUEST['Organism_Name']) && isset($_REQUEST['Organism_Latin']))
	{
		$an = 'Organism';
		if ($_REQUEST['Organism_Name']!='' && $_REQUEST['Organism_Latin']!='')
		{
			$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.organisms';
			$ns = trim($_REQUEST['Organism_Name']); $ul = $_REQUEST['Organism_Latin'];
		}
	}
	if (isset($_REQUEST['Model_Name']) && isset($_REQUEST['Model_URL']))
	{
		$an = 'Gene Model';
		if ($_REQUEST['Model_Name']!='' && $_REQUEST['Model_URL']!='')
		{
			$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.models';
			$ns = trim($_REQUEST['Model_Name']); $ul = $_REQUEST['Model_URL'];
			$as = $_REQUEST['Model_Alias'];
		}
	}
	if (isset($_REQUEST['Genome_Name']) && isset($_REQUEST['Genome_URL']))
	{
		$an = 'Genome URL';
		if ($_REQUEST['Genome_Name']!='' && $_REQUEST['Genome_URL']!='')
		{
			$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.genomes';
			$ns = trim($_REQUEST['Genome_Name']); $ul = $_REQUEST['Genome_URL'];
		}
	}
	if (isset($_REQUEST['Server_Name']) && isset($_REQUEST['Server_URL']))
	{
		$an = 'MySQL Server';
		if ($_REQUEST['Server_Name']!='' && $_REQUEST['Server_URL']!='')
		{
			$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.servers';
			$ns = trim($_REQUEST['Server_Name']); $ul = $_REQUEST['Server_URL'];
		}
	}
	if (isset($_REQUEST['Group_Name']))
	{
		$an = 'Group';
		if ($_REQUEST['Group_Name']!='')
		{
			$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.groups';
			$ns = $ul = trim($_REQUEST['Group_Name']);
		}
	}
	if (isset($fn) && isset($ns) && isset($ul))
	{
		$ar = array();
		if (file_exists($fn))
		{
			$inf = fopen($fn, "r");
			while ($sn=fgets($inf))
			{
				$ar[] = explode("\t", trim($sn));
			}
			fclose($inf);
		}

		$no = count($ar);
		for ($i=0; $i<$no; $i++)
		{
			if ($ns==$ar[$i][0] || $ul==$ar[$i][1]) break;
		}
		if ($i<$no)
		{
			if ($_REQUEST['Submit']=='Delete') unset($ar[$i]);
			else { $ar[$i][0]=$ns; $ar[$i][1]= $ul; if (isset($as)) $ar[$i][2]=$as; }
		}
		else 
		{
			if ($_REQUEST['Submit']=='Add/Update')
			{
				if (isset($as)) $ar[]=array($ns, $ul, $as); 
				else $ar[]=array($ns, $ul);
			}
		}

		$outf = fopen($fn, "w");
		foreach ($ar as $ad)
		{
			fprintf($outf, "%s%s", implode("\t", $ad), PHP_EOL);
		}
		fclose ($outf);	
	}
	else 
	{
		$err_msg = 'an input field is empty.';
	}
	$_REQUEST['SuffixSubmit'] = $an." - ".$_REQUEST['Submit'];
}
else if (isset($_REQUEST['SuffixSubmit']) && $_REQUEST['SuffixSubmit']=='Delete' && $_REQUEST['Suffix_File']!='')
{
	$sc=true;
	if (isset($_REQUEST['Suffix_File']) && $_REQUEST['Suffix_File']!='')
		$sc = delete_suffix($_REQUEST['Suffix_File']);
	if ($sc==false) $fail_msg = 'Failed to delete '.$_REQUEST['Suffix_File'].PHP_EOL;
	else { $err_msg = 'No Error'; $succ_msg = $_REQUEST['Suffix_File'].' is deleted.'.PHP_EOL; $_REQUEST['SuffixSubmit']='Suffix Removal'; }
}
else if (isset($_REQUEST['ProxyPageSubmit']))
{
	$url = trim($_REQUEST['Page_URL']);
	$ua = parse_url($url);
	if (strncmp($url, "http://", 7)!=0)
		$err_msg = $_REQUEST['Target_Type'].' source url must begin with http://';
	if ($err_msg == '')
	{
		$se = efile_get_contents($url); 
		$str = '';
		if (isset($se['err'])) $err_msg = $se['err'];
		else $str = $se['str'];
	}
	if ($err_msg == '' && $str == '') $err_msg = 'Source content is empty'; 
	if ($err_msg == '' && ($str=js_2_json($str)) == '')
	{ 
		$str = js_2_json(pg_2_js ($se['str'], $ua, 'Proxy'));
		if ($str=='') $err_msg = 'Source is not a valid AnnoJ.config source';
	}
	if ($err_msg == '' && ($ad=json_decode(utf8_encode($str), true))==null)
		$err_msg = 'Syntax error in source AnnoJ.config';
	if ($err_msg == '')
	{
		$HT = js_2_db_tracks ($ad);
		$HX = js_2_db_suffix ($ad);

		if (isset($ua['scheme'])) $host = $ua['scheme']."://".$ua['host'];
		$path = substr($ua['path'], 0, 1+strrpos($ua['path'],"/"));
		$n = count($HT);
		for ($i=0; $i<$n; $i++)
		{
			$ub = parse_url($HT[$i]['Track_URL']);
			if (isset($ub['scheme']) && isset($ub['host'])) continue;
			$ubp = trim($ub['path']);
			if ($ubp[0]!='/')
				$HT[$i]['Track_URL']=clean_url_parent($host.$path.$ub['path']);
			else $HT[$i]['Track_URL']=clean_url_parent($host.$ub['path']);
			$uc = parse_url($HT[$i]['Track_URL']);
			$ud = $proxy_cache_dir.'/'.$ua['host'].'/'.urlencode($uc['path']);
			if (!file_exists($ud)) mkdir($ud, 0755, true);
			if (isset($_REQUEST['Proxy_Cache']) && $_REQUEST['Proxy_Cache']==1)
			{
				$ue = "echo 1 > ".$ud."/.cache";
				exec ($ue);
			} 
			$HT[$i]['Track_URL']=$aj2.'/includes/proxy.php?'.$HT[$i]['Track_URL'];
		}
		$ub = parse_url($HX['Genome_URL']);
		if (!(isset($ub['scheme']) && isset($ub['host'])))
		{
			$ubp = trim($ub['path']);
			if ($ubp[0]!='/')
				$HX['Genome_URL']=clean_url_parent($host.$path.$ub['path']);
			else $HX['Genome_URL']=clean_url_parent($host.$ub['path']);
			$uc = parse_url($HX['Genome_URL']);
                        $ud = $proxy_cache_dir.'/'.$ua['host'].'/'.urlencode($uc['path']);
                        if (!file_exists($ud)) mkdir($ud, 0755, true);
			$HX['Genome_URL']=$aj2.'/includes/proxy.php?'.$HX['Genome_URL'];
		}
		$HX['Notes']="Proxy source: ".$_REQUEST['Page_URL'];
		$db = $proxy_cache_dir.'/'.$ua['host'].'/.'.urlencode($ua['path']).'.db'; 
		saveConfigDB ($HT, $HX, $db);
		$err_msg = "No Error";
		$succ_msg = "Proxy source ".$_REQUEST['Page_URL']." is created.";
	}
	$_REQUEST['SuffixSubmit'] = 'Proxy Page';
}

function add_group_share ($user, $group, $priority)
{
	global $aj2;

	if ($user=='' || $group=='') return;
	if ($priority[1]=='0' && $priority[2]=='0') return;

	$gcl = ''; $tcl = '';
	if ($priority[1]=='1' || $priority[1]=='5') $gcl = 'PagesOnly';
	else if ($priority[1]=='3' || $priority[1]=='7') $gcl = 'PagesTracks';
	if ($priority[2]=='1' || $priority[2]=='5') $tcl = 'PagesOnly';
	else if ($priority[2]=='3' || $priority[2]=='7') $tcl = 'PagesTracks';

	$ud = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$user;
	if (!file_exists($ud)) mkdir($ud, 0755, true);
	
	$fn = $ud.'/.allow_user_shares';
	$ga = explode(',', $group);
	$go = count($ga);
 
	if (file_exists($fn))
	{
		$sa = explode(PHP_EOL, trim(file_get_contents($fn)));
		$no = count($sa);
		for ($i=0; $i<$no; $i++)
		{
			$en = explode("\t", $sa[$i]);
			if ($en[0]=='All') unset($sa[$i]);
			else
			{
				foreach($ga as $sid)
				{
					if ($en[0]==$sid) unset($sa[$i]);
				}
			}
		}
	}
	if ($tcl!='') $sa[] = "All\t".$tcl;
	if ($gcl!='') 
	{
		foreach ($ga as $sid) $sa[] = $sid."\t".$gcl;
	}
	
	$sv = implode(PHP_EOL, $sa);
	file_put_contents($fn, $sv);
}

function proxy_track_retrieve ($ua, $trks)
{
	$trk_urls = ''; 
	$scheme = $ua['scheme']; $host = $ua['host']; 
	$path = '';
	$n = strrpos($ua['path'], '/')+1;
	if ($n>0) $path = substr($ua['path'],0,$n);
	$no = count($trks);
	for ($i=0;$i<$no;$i++)
	{
		$url = $trks[$i]['data'];
		if (strncmp($url,'http://',7)==0)
		{
			$ub = parse_url($url);
			if ($ub['host']!=$host) continue;
			$url = $ub['path'];
		}
		else if ($url[0]=='.')
		{
			$url = $path.$url;
		}
		$url = clean_url_parent($url);
		$trk_urls .= $scheme.'://'.$host.$url.PHP_EOL;
	}
	return $trk_urls;
}

function proxy_track_delete ($tl)
{
	global $proxy_cache_dir, $cache_dir;
	$msg = array ('err_msg' => '', 'fail_msg' => '', 'succ_msg' => '');
	if (!isset($proxy_cache_dir)) $proxy_cache_dir = $cache_dir;
	$tls = explode(PHP_EOL, trim($tl));
	$rmv_cmd = '';
	$no = count($tls);
	for ($i=0;$i<$no;$i++)
	{
		$ul = trim($tls[$i]);
		$ua = parse_url($ul);
		$dm = $proxy_cache_dir.'/'.$ua['host'].'/'.urlencode($ua['path']);
		if (file_exists($dm) && is_dir($dm))
		{
			$rmv_cmd = 'rm -rf '.$dm.' & '.PHP_EOL;
			exec ($rmv_cmd);
			$msg['succ_msg'] .= '<nobr>'.$ul.' in proxy is deleted.<br>'.PHP_EOL;
		}
		else $msg['fail_msg'] .= '<nobr>'.$ul.' is not in proxy folder.<br>'.PHP_EOL;
	}
	return $msg;
}

function proxy_track_create ($ua, $trks, $tl, $ch)
{
	global $aj2, $proxy_cache_dir, $cache_dir;
	$msg = array ('err_msg' => '', 'fail_msg' => '', 'succ_msg' => '');
	if (!isset($aj2)) $aj2 = $_SESSION['aj_root'];
	if (!isset($proxy_cache_dir)) $proxy_cache_dir = $cache_dir;
	$host = $ua['host'];
	$path = '';
	$n = strrpos($ua['path'], '/')+1;
	if ($n>0) $path = substr($ua['path'],0,$n);
	$dm = $proxy_cache_dir.'/'.$host;
	if (!file_exists($dm))
	{
		if (!mkdir($dm, 0755, true)) 
		{
			$msg['err_msg'] .= 'can not mkdir '.$dm.'.'.PHP_EOL;
			return $msg;
		}
	}
	file_put_contents($dm.'/.pages.'.urlencode($ua['path']), '1');

	$act = $aj2.'/includes/proxy.php';
	$n=strlen($act);
	$tls = explode(PHP_EOL, trim($tl));
	$no = count($tls);
	for ($i=0;$i<$no;$i++) $tls[$i] = trim($tls[$i]); 

	$no = count($trks);
	for ($i=0;$i<$no;$i++)
	{
		$url = $trks[$i]['data'];
		if (strncmp($url,$act,$n)==0) continue;
		if (strncmp($url,'http://',7)==0)
		{
			$ub = parse_url($url);
			if ($ub['host']!=$host) continue;
			$url = $ub['path'];
		}
		else if ($url[0]=='.')
		{
			$url = $path.$url;
		}
		$url = clean_url_parent($url);
		$nl = trim($ua['scheme'].'://'.$host.$url);
		if (!in_array($nl, $tls)) continue;
		$px = $dm.'/'.urlencode($url);
		if (!file_exists($px))
		{
			if (!mkdir($px, 0755, true))
			{
				$msg['err_msg'] .= 'can not mkdir '.$px.'.'.PHP_EOL;
				continue;
			}
		}
		$fn = $px.'/.type';
		file_put_contents($fn, 'ProxyTrack');
		$fn = $px.'/.cache';
		file_put_contents($fn, $ch);
		$fn = $px.'/.node_json';
		$txt = 'text : "'.$trks[$i]['name'].'", "Track_Name" : "'.$trks[$i]['name'].'"';
		$txt .= ', "Track_Type" : "'.$trks[$i]['type'].'"';
		$txt .= ', "Track_URL" : "'.$act.'?'.$ua['scheme'].'://'.$host.$url.'"';
		if (isset($trks[$i]['iconCls']) && $trks[$i]['iconCls']!='')
		{
			$txt .= ', "Icon" : "'.$trks[$i]['iconCls'].'"';
			$txt .= ', iconCls : "'.$trks[$i]['iconCls'].'"';
		}
		if (isset($trks[$i]['height']) && $trks[$i]['height']!='') $txt .= ', "Height" : "'.$trks[$i]['height'].'"';
		if (isset($trks[$i]['scale']) && $trks[$i]['scale']!='') $txt .= ', "Scale" : "'.$trks[$i]['scale'].'"';
		if (isset($trks[$i]['class']) && $trks[$i]['class']!='') $txt .= ', "Class" : "'.$trks[$i]['class'].'"';
		if (isset($trks[$i]['color']) && $trks[$i]['color']!='') $txt .= ', "Color" : "'.$trks[$i]['color'].'"';
		if (isset($trks[$i]['single']) && $trks[$i]['single']!='') $txt .= ', "Single" : "'.$trks[$i]['single'].'"';
		if (isset($trks[$i]['showControls']) && $trks[$i]['showControls']!='') $txt .= ', "Control" : "'.$trks[$i]['showControls'].'"';
		file_put_contents($fn, $txt);
		file_put_contents($px.'/.page.'.urlencode($ua['path']), '1');

		$msg['succ_msg'] .= '<nobr>proxy '.$ua['scheme'].'://'.$host.$url.' is created.<br>'.PHP_EOL;
	}
	return $msg;
}

?>
<head>
	<link type='text/css' rel='stylesheet' href='<?PHP echo $aj2 ?>/css/util.css' />
</head>
<body>
<table border="0" width="580" height="40" align=center>
	<tr align=center><td style="font-family: Verdana, sans-serif, Arial; font-size: 1.15em; font-weight: bold;  color: #408040;">jBuilder Admin Tools</td></tr>
</table>
<?PHP if ($err_msg!='') { ?>
<P>
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J Administration Information">
<tr><td class="THL" colspan="3" height="25">&nbsp; <?PHP if (isset($_REQUEST['SuffixSubmit'])) echo $_REQUEST['SuffixSubmit']; ?>  </td></tr>
<?PHP if ($succ_msg!='') { ?>
<tr><td class="TDL" width="16%">Success:</td><td class="TDL" width="60%"><?PHP echo $succ_msg ?></td><td class="TDL"></td></tr>
<?PHP } ?>
<?PHP if ($fail_msg!='') { ?>
<tr><td class="TDL" width="16%">Failed:</td><td class="TDL" width="60%"><?PHP echo $fail_msg ?></td><td class="TDL"></td></tr>
<?PHP } ?>
<?PHP if ($err_msg!='No Error') { ?>
<tr><td class="TDL" width="16%">Error:</td><td class="TDL" width="60%"><?PHP echo $err_msg.' '.err_echo().' '; ?></td><td class="TDL"></td></tr>
<?PHP } ?>
</table>
<?PHP } ?>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center summary="Anno-J User Information">
	<tr><td class="THL" colspan="3" height="25">&nbsp;Add User</td></tr>
	<tr height=30 valign=bottom><td class="TDL" width="16%">User ID:</td> 
		<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" id="x_User_ID" NAME="User_ID" value="<?PHP aj_echo('User_ID'); ?>"></td><td class="TDL"><?PHP if ($err_msg=='User_ID') err_echo('Must be filled'); else echo '<i>hchen</i>'; ?></td></tr>
	<tr height=30 valign=bottom><td class="TDL" width="16%">Name:</td>
		<td class="TDL" width="60%" ><INPUT class='CEL' TYPE="TEXT" NAME="User_Name" value="<?PHP aj_echo('User_Name'); ?>"></td><td class="TDL"><?PHP if ($err_msg=='User_Name') err_echo('Must be filled'); else echo '<i>Huaming Chen</i>';  ?></td></tr>
	<tr height=30 valign=bottom><td class="TDL">E-mail:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="User_Mail" value="<?PHP aj_echo('User_Mail'); ?>"></td><td class="TDL"><?PHP if ($err_msg=='User_Mail') err_echo('Must be filled'); else echo '<i>hchen@salk.edu</i>';  ?></td></tr>
	<tr height=30 valign=bottom><td class="TDL">Group:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Group" id="x_Group" value="<?PHP aj_echo('Group'); ?>"><td class="TDL">
		<?PHP $fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.groups'; read_x_info($fn, 'xGroup', 'x_Group', 10); ?>
	</td></tr>
	<tr height=36 valign=middle><td class="TDL">Priority:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Priority" value="<?PHP aj_echo('Priority'); ?>"><td class="TDL"></td></tr>
	</tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
	<tr><td width="30%" align="left"> 
		<INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_User_ID').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete user \''+usr+'\' ?\n'+usr+'\'s pages, tracks and suffixes will be deleted too.'; var r = confirm(cf); if (!r) return false; }" Name="UserSubmit" value="Delete" style="width:99px;height:22px;font-weight:regular">
		</td><td align="center" width="40%">
		&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="UserSubmit" value="Add User" style="width:120px;height:25px;font-weight:bold">&nbsp; &nbsp;
		</td><td align="right" width="30%">
		<INPUT TYPE="SUBMIT" onClick="javascript: if (document.getElementById('x_User_ID').value=='') return false;" Name="UserSubmit" value="Reset Password" style="width:110px;height:22px;font-weight:regular">
	</td></tr>
</table>
</form>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
	<tr><td class="THL" colspan="3" height="25">&nbsp;Add Group</td></tr>
	<tr height=32 valign=middle><td class="TDL" width="16%">Group:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Group_Name" id="x_Group_Name" value="<?PHP aj_echo('Group_Name'); ?>"></td><td class="TDL"><?PHP $fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.groups'; read_x_info($fn, 'xGroup', 'x_Group_Name', 0); ?>
	</td></tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
	<tr><td width="30%" align="left">
		<INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_Group_Name').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete group \''+usr+'\' ?'; var r = confirm(cf); if (!r) return false; }" Name="Submit" value="Delete" style="width:99px;height:22px;font-weight:regular">
		</td><td align="center" width="40%">
		&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="Submit" value="Add/Update" style="width:120px;height:25px;font-weight:bold">&nbsp; &nbsp;
		</td><td align="right" width="30%">
	</td></tr>
</table>
</form>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
	<tr><td class="THL" colspan="3" height="25">&nbsp;Organisms</td></tr>
	<tr height=25 valign=middle><td class="TDL" width="16%">Name:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Organism_Name" id="x_OrganismText" value="<?PHP aj_echo('Organism_Name'); ?>"></td><td class="TDL"><?PHP $fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.organisms'; read_x_info($fn, 'xOrganism', 'x_Organism', 1); ?>
	</td></tr>
	<tr height=32 valign=middle><td class="TDL" width="16%">Full Name:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Organism_Latin" id="x_Organism" value="<?PHP aj_echo('Organism_Latin'); ?>"></td><td class="TDL">
	</td></tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
	<tr><td width="30%" align="left">
		<INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_OrganismText').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete organism \''+usr+'\' ?'; var r = confirm(cf); if (!r) return false; }" Name="Submit" value="Delete" style="width:99px;height:22px;font-weight:regular">
		</td><td align="center" width="40%">
		&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="Submit" value="Add/Update" style="width:120px;height:25px;font-weight:bold">&nbsp; &nbsp;
		</td><td align="right" width="30%">
	</td></tr>
</table>
</form>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
	<tr><td class="THL" colspan="3" height="25">&nbsp;Gene Models</td></tr>
	<tr height=25 valign=middle><td class="TDL" width="16%">Name:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Model_Name" id="x_ModelText" value="<?PHP aj_echo('Model_Name'); ?>"></td><td class="TDL"><?PHP $fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.models'; $fn2 = $_SERVER['DOCUMENT_ROOT'].$aj2.'/models'; read_x_info($fn, 'xModel', 'x_Model', 2, $fn2); ?>
	</td></tr>
	<tr height=32 valign=middle><td class="TDL" width="16%">URL:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Model_URL" id="x_Model" value="<?PHP aj_echo('Model_URL'); ?>"></td><td class="TDL">
	</td></tr>
	<tr height=32 valign=middle><td class="TDL" width="16%">Alias:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Model_Alias" id="x_ModelAlias" value="<?PHP aj_echo('Model_Alias'); ?>"></td><td class="TDL">
	</td></tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
	<tr><td width="30%" align="left">
		<INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_ModelText').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete model \''+usr+'\' ?'; var r = confirm(cf); if (!r) return false; }" Name="Submit" value="Delete" style="width:99px;height:22px;font-weight:regular">
		</td><td align="center" width="40%">
		&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="Submit" value="Add/Update" style="width:120px;height:25px;font-weight:bold">&nbsp; &nbsp;
		</td><td align="right" width="30%">
	</td></tr>
</table>
</form>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
	<tr><td class="THL" colspan="3" height="25">&nbsp;Genome URLs</td></tr>
	<tr height=25 valign=middle><td class="TDL" width="16%">Name:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Genome_Name" id="x_GenomeText" value="<?PHP aj_echo('Genome_Name'); ?>"></td><td class="TDL"><?PHP $fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.genomes'; $fn2 = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes'; read_x_info($fn, 'xGenome', 'x_Genome', 1, $fn2); ?>
	</td></tr>
	<tr height=32 valign=middle><td class="TDL" width="16%">URL:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Genome_URL" id="x_Genome" value="<?PHP aj_echo('Genome_URL'); ?>"></td><td class="TDL">
	</td></tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
	<tr><td width="30%" align="left">
		<INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_GenomeText').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete genome \''+usr+'\' ?'; var r = confirm(cf); if (!r) return false; }" Name="Submit" value="Delete" style="width:99px;height:22px;font-weight:regular">
		</td><td align="center" width="40%">
		&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="Submit" value="Add/Update" style="width:120px;height:25px;font-weight:bold">&nbsp; &nbsp;
		</td><td align="right" width="30%">
	</td></tr>
</table>
</form>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
	<tr><td class="THL" colspan="3" height="25">&nbsp;MySQL Servers</td></tr>
	<tr height=25 valign=middle><td class="TDL" width="16%">Name:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Server_Name" id="x_ServerText" value="<?PHP aj_echo('Server_Name'); ?>"></td><td class="TDL"><?PHP $fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.servers'; read_x_info($fn, 'xServer', 'x_Server', 1); ?>
	</td></tr>
	<tr height=32 valign=middle><td class="TDL" width="16%">Addr:</td>
		<td class="TDL" width="60%" align=left><INPUT class='CEL' TYPE="TEXT" NAME="Server_URL" id="x_Server" value="<?PHP aj_echo('Server_URL'); ?>"></td><td class="TDL">	 </td></tr>
</table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
	<tr><td width="30%" align="left">
		<INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_ServerText').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete server \''+usr+'\' ?'; var r = confirm(cf); if (!r) return false; }" Name="Submit" value="Delete" style="width:99px;height:22px;font-weight:regular">
		</td><td align="center" width="40%">
		&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="Submit" value="Add/Update" style="width:120px;height:25px;font-weight:bold">&nbsp; &nbsp;
		</td><td align="right" width="30%">
	</td></tr>
</table>
</form>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
<tr><td class="THL" colspan="3" height="25">&nbsp;Proxy Page</td></tr>
<tr height=40 valign=middle><td class="TDL" width="16%">Page URL:</td>
<td class="TDL" width="60%" align=left>
<INPUT class='CEL' TYPE="TEXT" NAME="Page_URL" id="x_Page_URL" value="<?PHP aj_echo('Page_URL'); ?>"></td><td class="TDL">
<INPUT TYPE="checkbox" NAME="Proxy_Cache" id="x_Proxy_Cache" value="1" <?PHP if (isset($_REQUEST['Proxy_Cache']) && $_REQUEST['Proxy_Cache']=='1') echo "checked"; ?> > Save Caches
</td></tr></table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
<tr><td width="30%" align="left">
</td><td align="center" width="40%">
&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="ProxyPageSubmit" value="Add Proxy Page" style="width:120px;height:25px;font-weight:bold">&nbsp; &nbsp;
</td><td align="right" width="30%">
</td></tr>
</table>
</form>

<!--
<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
<tr><td class="THL" colspan="3" height="25">&nbsp;Proxy Tracks</td></tr>
<tr height=32 valign=middle><td class="TDL" width="16%">Config URL:</td>
<td class="TDL" width="60%" align=left>
<INPUT class='CEL' TYPE="TEXT" NAME="Proxy_URL" id="x_Proxy_URL" value="<?PHP aj_echo('Proxy_URL'); ?>"></td><td class="TDL">
<INPUT TYPE="checkbox" NAME="Proxy_Cache" id="x_Proxy_Cache" value="1" <?PHP if (isset($_REQUEST['Proxy_Cache']) && $_REQUEST['Proxy_Cache']=='1') echo "checked"; ?> > Save Caches
</td></tr>
<tr height=32 valign=middle><td class="TDL" width="16%">Track URLs:</td>
<td class="TDL" width="60%" align=left>
<TEXTAREA class='CEL' TYPE="TEXT" rows=8 NAME="Track_URL" id="x_Track_URL"><?PHP aj_echo('Track_URL'); ?></TEXTAREA></td><td class="TDL">&nbsp;
</td></tr></table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
<tr><td width="30%" align="left">
<INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_Track_URL').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete these proxy tracks? \n '; var r = confirm(cf); if (!r) return false; }" Name="ProxySubmit" value="Delete" style="width:99px;height:22px;font-weight:regular">
</td><td align="center" width="40%">
&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="ProxySubmit" value="Add Proxy Tracks" style="width:120px;height:25px;font-weight:bold">&nbsp; &nbsp;
</td><td align="right" width="30%">
<INPUT TYPE="SUBMIT" Name="ProxySubmit" value="Retrieve" style="width:110px;height:20px;">
</td></tr>
</table>
</form>

<form method=POST>
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
<tr><td class="THL" colspan="3" height="25">&nbsp;Proxy Genome</td></tr>
<tr height=32 valign=middle><td class="TDL" width="16%">Genome URL:</td>
<td class="TDL" width="60%" align=left>
<INPUT class='CEL' TYPE="TEXT" NAME="Proxy_Genome_URL" id="x_Proxy_Genome_URL" value="<?PHP aj_echo('Proxy_Genome_URL'); ?>"></td><td class="TDL">
</td></tr></table>
<table class="noborder" width="580" cellspacing="5" height="45" align=center>
<tr><td width="30%" align="left">
<INPUT TYPE="SUBMIT" onClick="javascript: var usr=document.getElementById('x_Proxy_Genome_URL').value; if (usr=='') return false; else {  var cf = 'Are you sure to delete these proxy tracks? \n '; var r = confirm(cf); if (!r) return false; }" Name="ProxyGenomeSubmit" value="Delete" style="width:99px;height:22px;font-weight:regular">
</td><td align="center" width="40%">
&nbsp; &nbsp; &nbsp; <INPUT TYPE="SUBMIT" Name="ProxyGenomeSubmit" value="Add Proxy Genome" style="width:120px;height:25px;font-weight:bold">&nbsp; &nbsp;
</td><td align="right" width="30%">
</td></tr>
</table>
</form>
//-->

<P>
<br>

