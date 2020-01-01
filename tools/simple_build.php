<?PHP
ini_set('display_errors', 'On');
require_once 'util_module.php';
require_once 'login_session2.php';

$err_star = "<font color=red>*</font>";
$err_no = '';
$err_msg = '';

if (isset($_REQUEST["RollbackPage"]) && $_REQUEST["RollbackPage"]!='')
{
	$ownPage=trim($_REQUEST["RollbackPage"]);
	$fg = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/.'.$ownPage.'.db';
	if (file_exists($fg.'bak0')) copy($fg.'bak0', $fg);
	for ($i=1;$i<5;$i++)
	{
		if (file_exists($fg.'bak'.$i)) 
		{
			copy($fg.'bak'.$i, $fg.'bak'.($i-1));
			unlink($fg.'bak'.$i);
		}
	}
}

if (isset($_REQUEST["TrashBin"]) && $_REQUEST["TrashBin"]!='')
{
	$DA = explode (" ,,, ", $_REQUEST["TrashBin"]);
	$dv = array();
	foreach ($DA as $da)
	{
		$dh = explode("::", $da);
		if (!isset($dh[1]) || trim($dh[1])=='') continue;
		if (!isset($dh[2]) || trim(trim($dh[2]),'/')=='' ) continue;
		if ($dh[1]==$aj_user && ($dh[0]=="Tracks" || $dh[0]=="Pages"))
		{
			if ($dh[0]=="Tracks")
				$dv[$dh[2]] = $_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$aj_user.$dh[2];
			else
			{
				$dv['.'.$dh[2].'.db'] = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/.'.$dh[2].'.db';
				$dv[$dh[2].'.js'] = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/'.$dh[2].'.js';
				$dv[$dh[2].'.php'] = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/'.$dh[2].'.php';
			}
		}
		else if ($dh[1]=="CORS")
		{
			$dv['.'.$dh[2].'.db'] = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/CORS/.'.$dh[2].'.db';
		}
	}
	$td = $_SERVER['DOCUMENT_ROOT'].$aj2.'/trashes/'.$aj_user;
	if (!file_exists($td))
	{
		mkdir($td, 0700, true);
	}
	foreach ($dv as $df => $dd)
	{
		if (file_exists($dd))
		{
			if (file_exists($td.'/'.$df)) 
			{
				if (is_dir($td.'/'.$df)) exec('rm -rf '.$td.'/'.$df);
				else unlink($td.'/'.$df);
			}
			else
			{
				$tdv = substr($td.'/'.$df, 0, strrpos($td.'/'.$df, "/"));
				if (!file_exists($tdv)) mkdir ($tdv, 0700, true);
			}
			rename($dd, $td.'/'.$df);
		}
	}
}
if (isset($_REQUEST["AllTrack"]) && $_REQUEST["AllTrack"]!='')
{
	$alltracks = $_REQUEST["AllTrack"]; 
	if (isset($_REQUEST["AllTrack1"])) $alltracks .= $_REQUEST["AllTrack1"];
	if (isset($_REQUEST["AllTrack2"])) $alltracks .= $_REQUEST["AllTrack2"];
	if (isset($_REQUEST["AllTrack3"])) $alltracks .= $_REQUEST["AllTrack3"];
	if (isset($_REQUEST["AllTrack4"])) $alltracks .= $_REQUEST["AllTrack4"];
	if (isset($_REQUEST["AllTrack5"])) $alltracks .= $_REQUEST["AllTrack5"];
	if (isset($_REQUEST["AllTrack6"])) $alltracks .= $_REQUEST["AllTrack6"];
	if (isset($_REQUEST["AllTrack7"])) $alltracks .= $_REQUEST["AllTrack7"];
	if (isset($_REQUEST["AllTrack8"])) $alltracks .= $_REQUEST["AllTrack8"];
	if (isset($_REQUEST["AllTrack9"])) $alltracks .= $_REQUEST["AllTrack9"];

	if (isset($_REQUEST["AllTrack10"])) $alltracks .= $_REQUEST["AllTrack10"];
	if (isset($_REQUEST["AllTrack11"])) $alltracks .= $_REQUEST["AllTrack11"];
	if (isset($_REQUEST["AllTrack12"])) $alltracks .= $_REQUEST["AllTrack12"];
	if (isset($_REQUEST["AllTrack13"])) $alltracks .= $_REQUEST["AllTrack13"];
	if (isset($_REQUEST["AllTrack14"])) $alltracks .= $_REQUEST["AllTrack14"];
	if (isset($_REQUEST["AllTrack15"])) $alltracks .= $_REQUEST["AllTrack15"];
	if (isset($_REQUEST["AllTrack16"])) $alltracks .= $_REQUEST["AllTrack16"];
	if (isset($_REQUEST["AllTrack17"])) $alltracks .= $_REQUEST["AllTrack17"];
	if (isset($_REQUEST["AllTrack18"])) $alltracks .= $_REQUEST["AllTrack18"];
	if (isset($_REQUEST["AllTrack19"])) $alltracks .= $_REQUEST["AllTrack19"];

	if (isset($_REQUEST["AllTrack20"])) $alltracks .= $_REQUEST["AllTrack20"];
	if (isset($_REQUEST["AllTrack21"])) $alltracks .= $_REQUEST["AllTrack21"];
	if (isset($_REQUEST["AllTrack22"])) $alltracks .= $_REQUEST["AllTrack22"];
	if (isset($_REQUEST["AllTrack23"])) $alltracks .= $_REQUEST["AllTrack23"];
	if (isset($_REQUEST["AllTrack24"])) $alltracks .= $_REQUEST["AllTrack24"];
	if (isset($_REQUEST["AllTrack25"])) $alltracks .= $_REQUEST["AllTrack25"];
	if (isset($_REQUEST["AllTrack26"])) $alltracks .= $_REQUEST["AllTrack26"];
	if (isset($_REQUEST["AllTrack27"])) $alltracks .= $_REQUEST["AllTrack27"];
	if (isset($_REQUEST["AllTrack28"])) $alltracks .= $_REQUEST["AllTrack28"];
	if (isset($_REQUEST["AllTrack29"])) $alltracks .= $_REQUEST["AllTrack29"];

	if (isset($_REQUEST["AllTrack30"])) $alltracks .= $_REQUEST["AllTrack30"];
	if (isset($_REQUEST["AllTrack31"])) $alltracks .= $_REQUEST["AllTrack31"];
	if (isset($_REQUEST["AllTrack32"])) $alltracks .= $_REQUEST["AllTrack32"];
	if (isset($_REQUEST["AllTrack33"])) $alltracks .= $_REQUEST["AllTrack33"];
	if (isset($_REQUEST["AllTrack34"])) $alltracks .= $_REQUEST["AllTrack34"];
	if (isset($_REQUEST["AllTrack35"])) $alltracks .= $_REQUEST["AllTrack35"];
	if (isset($_REQUEST["AllTrack36"])) $alltracks .= $_REQUEST["AllTrack36"];
	if (isset($_REQUEST["AllTrack37"])) $alltracks .= $_REQUEST["AllTrack37"];
	if (isset($_REQUEST["AllTrack38"])) $alltracks .= $_REQUEST["AllTrack38"];
	if (isset($_REQUEST["AllTrack39"])) $alltracks .= $_REQUEST["AllTrack39"];
	
	$DA = explode (" ;;; ", $alltracks);
	$DH = array();
	foreach ($DA as $da)
	{
		$DB = explode(" ,,, ", $da);
		$dh = array();
		foreach ($DB as $db)
		{
			$dbj = explode(" =:= ", $db);
			if (strtolower(trim($dbj[1]))=="undefined") $dbj[1]="";
			$dh[trim($dbj[0])]=trim($dbj[1]);
		}
		$dh["Node_Path"]=substr($dh["Node_Path"], 0, strpos($dh["Node_Path"], $dh["Node_Name"])-1);
		if (isset($dh["Track_Name"])) $dh["Track_Name"] = $dh["Node_Name"];
		if (isset($dh["Path"]) && $dh["Path"]=="") $dh["Path"]=$dh["Node_Path"];
		if (isset($dh["Track_Path"])) 
		{
			if ($dh["Data_Type"]!="SimpleData" || trim($dh["Track_Path"])=="")
				$dh["Track_Path"] = substr($dh["Node_Path"], strpos($dh["Node_Path"],"/")+1);
		}
		$DH[] = $dh; 
	}
	$HA = array();    //active tracks
	$HX = array();    //suffixes
	$HD = array();	  //data to build tracks
	$HT = array();	  //tracks for config;
	foreach ($DH as $dh)
	{
		if ($dh["Data_Type"]=="PageTrack" || $dh["Data_Type"]=="SimpleData" || $dh["Data_Type"]=="SimpleTrack" || $dh["Data_Type"]=="MySQLData" || $dh["Data_Type"]=="MySQLTrack")
		{
			if ($dh["Data_Type"]!="PageTrack")
			{
				$HD[]=$dh;
				$r=strrpos($dh["Track_URL"],'/');
				if (!$r) $r=-1;
				if ($r<1)
				{
					$dh["Track_URL"]=strtr($aj2."/tracks/".$aj_user."/".$dh["Node_Path"].'/'.substr($dh["Track_URL"], $r+1), ' ', '_');
				}
			}
			$HT[]=$dh; continue;
		}
		if ($dh["Data_Type"]=="ActiveTrack") { $HA[]=$dh; continue; }
		if ($dh["Data_Type"]=="SuffixBoth" || $dh["Data_Type"]=="SuffixPage" || $dh["Data_Type"]=="SuffixTrack")
			$HX[]=$dh;
	}
	$no = count($HA);
	$nz = count($HT);
	for ($i=0;$i<$no;$i++)
	{
		for ($j=0;$j<$nz;$j++)
		{
			if ($HT[$j]["Track_URL"]==$HA[$i]["Track_URL"] || substr($HT[$j]["Track_URL"], strrpos($HT[$j]["Track_URL"],'/')+1)==$HA[$i]["Track_URL"]) 
			{
				$HT[$j]["active"]= $i+1;
				break;
			}
		}
	}
	unset($HA);
	if (count($HX)>1) 
	{ 
		$hx = array_merge($HX[0], $HX[1]); 
		if ($HX[0]["Data_Type"] != $HX[1]["Data_Type"]) $hx["Data_Type"]="SuffixBoth"; 
	}
	else $hx = $HX[0];

	$ownPage = "NewPage";

	if (count($HT)>0 && ($hx["Data_Type"]=="SuffixBoth" || $hx["Data_Type"]=="SuffixPage"))
	{
		$dba = saveConfigDB($HT, $hx);
		$ownPage = $dba[0];
		$HT = $dba[1];
		$hx = $dba[2];
		saveConfigDBFinal ((isset($hx['FinalVersion']) && $hx['FinalVersion']==true), $ownPage, $HT);

		if (isset($hx['DynamicJS']) && $hx['DynamicJS'] == true)
		{
			//saveConfigScriptPHP ($ownPage, $hx);
			saveConfigScript ($ownPage, $HT, $hx);
			saveConfigPage ($ownPage, $hx, true);
		}
		else 
		{
			saveConfigScript ($ownPage, $HT, $hx);
			saveConfigPage ($ownPage, $hx, false);
		}
	}
}

function saveConfigDBFinal ($final, $ownPage, $HT)
{
	global $aj2, $aj_user;
	$fs = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/.final_'.$ownPage;
	if ($final) file_put_contents($fs, '');
	else if (!$final && file_exists($fs)) unlink ($fs);
}

function saveConfigScriptPHP($fg, $hx)
{
	global $aj2, $aj_user;
	$fs = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/'.$fg.'_js.php';
	$outf = fopen($fs, "w");
	fprintf ($outf, "<?PHP\n");
	if (isset($hx['AllowCORS']) && $hx['AllowCORS'] == true)
		fprintf ($outf, "%s", http_cors());
	$df = '$_SERVER[\'DOCUMENT_ROOT\'].\''.$aj2.'/pages/'.$aj_user.'/.'.$fg.'.db\'';
	fprintf ($outf, "\t\$db=%s;\n\t", $df);
	$df = 'require_once ($_SERVER[\'DOCUMENT_ROOT\'].\''.$aj2.'/includes/simple_mine.php\');';
	fprintf ($outf, "%s\n?>\n", $df);
	fclose ($outf);
}

function saveConfigScript($fg, $HT, $hx)
{
	global $aj2, $aj_user;
	$fs = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/'.$fg.'.js';
	$outf = fopen($fs, "w");
	fprintf ($outf, "\tAnnoJ.config = {%s\t\ttracks : [%s", PHP_EOL.PHP_EOL, PHP_EOL.PHP_EOL);
	
	$grp='Nothing2GuessOnWay'; $g=0; $k=1; $HA = array();
	foreach ($HT as $ht)
	{
		if ($ht['Track_Path']!=$grp)
		{
			$g++; $k=1;
		}
		fprintf ($outf, "\t\t\t{%s", PHP_EOL);
		fprintf ($outf, "\t\t\t\tid : '%s',%s", $g."_".$k, PHP_EOL);
		fprintf ($outf, "\t\t\t\tname : '%s',%s", addslashes($ht['Track_Name']), PHP_EOL);
		fprintf ($outf, "\t\t\t\ttype : '%s',%s", $ht['Track_Type'], PHP_EOL);
		fprintf ($outf, "\t\t\t\tpath : '%s',%s", addslashes($ht['Track_Path']), PHP_EOL);
		fprintf ($outf, "\t\t\t\tdata : '%s',%s", $ht['Track_URL'], PHP_EOL);
		if (isset($ht['Icon']) && $ht['Icon']!='')		
			fprintf ($outf, "\t\t\t\ticonCls : '%s',%s", $ht['Icon'], PHP_EOL);
		if (!isset($ht['Height']) || $ht['Height']=='') $ht['Height'] = 40;
		fprintf ($outf, "\t\t\t\theight : %s,%s", $ht['Height'], PHP_EOL);
		if (!isset($ht['Scale']) || $ht['Scale']=='') $ht['Scale'] = 1;
		fprintf ($outf, "\t\t\t\tscale : %s,%s", $ht['Scale'], PHP_EOL);
		if (!isset($ht['boxHeight']) || $ht['boxHeight']=='') $ht['boxHeight'] = 0;
		if (isset($ht['boxHeight']) && $ht['boxHeight']!= 10 && $ht['boxHeight'] > 0)
			fprintf ($outf, "\t\t\t\tboxHeight : %s,%s", $ht['boxHeight'], PHP_EOL);
		if (!isset($ht['boxWidthMin']) || $ht['boxWidthMin']=='') $ht['boxWidthMin'] = 0;
		if (isset($ht['boxWidthMin']) && $ht['boxWidthMin'] > 0)
			fprintf ($outf, "\t\t\t\tboxWidthMin : %d,%s", $ht['boxWidthMin'], PHP_EOL);
		if (isset($ht['Class']) && $ht['Class'] != '')
			fprintf ($outf, "\t\t\t\tclass: '%s',%s", $ht['Class'], PHP_EOL);
		if (isset($ht['Color']) && $ht['Color']!='')
		{
			$colors = explode(",", $ht['Color']);
			$colorStr = ''; $ncolorStr = 0;
			foreach ($colors as $colorv)
			{
				$icolors = explode(":", $colorv);
				$iclass = $icolors[0]; $icolor = '';
				if (isset($icolors[1])) $icolor = $icolors[1];
				if (trim($iclass)!='' && trim($icolor)!='')
				{
					if ($ncolorStr != 0) $colorStr .= ", ";
					$colorStr .= trim($iclass)." : '".trim($icolor)."'";
					$ncolorStr++;
				}
			}
			if ($colorStr != '') 
				fprintf ($outf, "\t\t\t\tcolor : { %s },%s", $colorStr, PHP_EOL);
		}

		if (isset($ht['Single']) && $ht['Single']!='')
			fprintf ($outf, "\t\t\t\tsingle : 1,%s", PHP_EOL);
		if (isset($ht['Control']) && $ht['Control']!='')
			fprintf ($outf, "\t\t\t\tshowControls : 1,%s", PHP_EOL);
		if (isset($ht['showWalks']) && $ht['showWalks']!='')
			fprintf ($outf, "\t\t\t\tshowWalks : 1,%s", PHP_EOL);
		if (isset($ht['Policy']) && $ht['Policy']!='')
			fprintf ($outf, "\t\t\t\tpolicy : \"%s\",%s", $ht['Policy'], PHP_EOL);
		if (isset($ht['alignControl']) && $ht['alignControl']!='')
			fprintf ($outf, "\t\t\t\talignControl : %s,%s", $ht['alignControl'], PHP_EOL);
		if (isset($ht['NoArrows']) && $ht['NoArrows']!='')
			fprintf ($outf, "\t\t\t\tshowArrows : 0,%s", PHP_EOL);
		if (isset($ht['NoLabels']) && $ht['NoLabels']!='')
			fprintf ($outf, "\t\t\t\tshowLabels : 0,%s", PHP_EOL);
		if (isset($ht['active']) && $ht['active'] >0)
			$HA[$ht['active']] = $g."_".$k;
		
		fprintf ($outf, "\t\t\t},%s", PHP_EOL);	

		$grp=$ht['Track_Path'];	
		$k++;
	}
	fprintf ($outf, "\t\t],%s", PHP_EOL.PHP_EOL);

	ksort($HA); $k=0;
	fprintf ($outf, "\t\tactive : [");
	foreach ($HA as $ha)
	{
		if ($k==1) fprintf ($outf, ", ");
		fprintf ($outf, "'%s'", $ha);
		$k=1;
	}
	fprintf ($outf, "],%s", PHP_EOL.PHP_EOL);

	if (!isset($hx['Bookmarks_URL']) || $hx['Bookmarks_URL']=='')
		$hx['Bookmarks_URL'] = $aj2.'/includes/common_bookmarks.php';
	if (!isset($hx['Analysis_URL']) || $hx['Analysis_URL']=='')
		$hx['Analysis_URL'] = $aj2.'/includes/analysis.php';

	fprintf ($outf, "\t\ttitle : '%s',%s", addslashes($hx['Page_Title']), PHP_EOL);
	fprintf ($outf, "\t\tgenome : '%s',%s", $hx['Genome_URL'], PHP_EOL);
	fprintf ($outf, "\t\tbookmarks : '%s',%s", $hx['Bookmarks_URL'], PHP_EOL);
	fprintf ($outf, "\t\tanalysis : '%s',%s", $hx['Analysis_URL'], PHP_EOL);
	fprintf ($outf, "\t\tlocation : {%s", PHP_EOL);
	fprintf ($outf, "\t\t\tassembly : '%s',%s", $hx['Assembly'], PHP_EOL);
	fprintf ($outf, "\t\t\tposition : '%s',%s", $hx['Position'], PHP_EOL);
	fprintf ($outf, "\t\t\tbases : %s,%s", $hx['Bases'], PHP_EOL);
	fprintf ($outf, "\t\t\tpixels : %s,%s", $hx['Pixels'], PHP_EOL);
	fprintf ($outf, "\t\t},%s\t\tadmin : {%s", PHP_EOL, PHP_EOL);
	fprintf ($outf, "\t\t\tname : '%s',%s", addslashes($hx['Author']), PHP_EOL);
	fprintf ($outf, "\t\t\temail : '%s',%s", $hx['Email'], PHP_EOL);
	fprintf ($outf, "\t\t\tnotes : '%s',%s", addslashes($hx['Notes']), PHP_EOL);
	fprintf ($outf, "\t\t},%s\t\tsettings : {%s", PHP_EOL, PHP_EOL);
	if (isset($hx['Baseline']) && $hx['Baseline']!='') fprintf ($outf, "\t\t\tbaseline : %s,%s", $hx['Baseline'], PHP_EOL);
	if (isset($hx['Display']) && $hx['Display']!='' && $hx['Display']!='0' && $hx['Display']!=0) fprintf ($outf, "\t\t\tdisplay : %s,%s", $hx['Display'], PHP_EOL);
	if (isset($hx['Activate']) && $hx['Activate']!='' && $hx['Activate']!='0' && $hx['Activate']!=0) fprintf ($outf, "\t\t\tactivate : %s,%s", $hx['Activate'], PHP_EOL);
	if (isset($hx['GlobalScale']) && $hx['GlobalScale']!='') fprintf ($outf, "\t\t\tscale : %s,%s", $hx['GlobalScale'], PHP_EOL);
	if (isset($hx['Multi']) && $hx['Multi']!='') fprintf ($outf, "\t\t\tmulti : %s,%s", $hx['Multi'], PHP_EOL);
	fprintf ($outf, "\t\t\tyaxis : %s,%s", $hx['Yaxis'], PHP_EOL);
	if (isset($hx['Max']) && $hx['Max']!='') fprintf ($outf, "\t\t\tmax : %s,%s", $hx['Max'], PHP_EOL);
	if (isset($hx['Min']) && $hx['Min']!='') fprintf ($outf, "\t\t\tmin : %s,%s", $hx['Min'], PHP_EOL);
	if (isset($hx['HiC_d']) && $hx['HiC_d']!='' && $hx['HiC_d']!='0' && $hx['HiC_d']!=0) fprintf ($outf, "\t\t\thic_d : %s,%s", $hx['HiC_d'], PHP_EOL);
	fprintf ($outf, "\t\t},%s\t\tcitation : '%s',%s", PHP_EOL, addslashes($hx['Citation']), PHP_EOL);
	fprintf ($outf, "\t\tjbuilder : %s,%s", trim(file_get_contents(".version")), PHP_EOL); 
	fprintf ($outf, "\t};%s", PHP_EOL);

	fclose($outf);
}

function saveConfigPage($fg, $hx, $hp=false)
{
	global $aj2, $aj_user;
	$page_path = $aj2.'/pages/'.$aj_user.'/';
	$fm = $_SERVER['DOCUMENT_ROOT'].$aj2.'/tools/anno-j.templat.locale';
	$fn = $_SERVER['DOCUMENT_ROOT'].$page_path.$fg.'.php';
	$inf = fopen($fm, "r");
	$outf = fopen($fn, "w");
	while (($sn=fgets($inf))!=NULL)			
	{
		if (trim($sn)=='<!-- anno-j variables -->')
		{
			fprintf ($outf, '%s$aj2 = \'%s\';%s', PHP_EOL, $aj2, PHP_EOL);
			fprintf ($outf, 'require_once ($_SERVER[\'DOCUMENT_ROOT\'].$aj2.\'/tools/util_module.php\');%s', PHP_EOL.PHP_EOL);
			fprintf ($outf, '$priority = \'%s\';%s', (isset($hx['Priority']))?$hx['Priority']:'Public', PHP_EOL);
			if (isset($_SESSION['aj_group']))
			{
				if (strpos($_SESSION['aj_group'], ",") !== false) $tgroup = substr($_SESSION['aj_group'],0,strpos($_SESSION['aj_group'], ','));
				else $tgroup = $_SESSION['aj_group'];
			}
			else $tgroup = '';
			fprintf ($outf, '$visitor = \'%s\';%s', (isset($_SESSION['aj_group']) && $hx['Priority']!='Private')?$tgroup:$_SESSION['aj_user'], PHP_EOL);
			if (isset($_SESSION['aj_root'])) $aj2=$_SESSION['aj_root'];
		} 
		else if (trim($sn)=='<!-- anno-j redirect -->')
		{
			fprintf ($outf, '$_SESSION[\'aj_root\'] = $aj2;%s', PHP_EOL); 
			fprintf ($outf, 'if ($priority!=\'Public\' && !isset($_SESSION[\'aj_user\'])) http_redirect(\'login_module.php\');%s', PHP_EOL);
		}
		else if (trim($sn)=='<!-- anno-j page title -->')
		{
			if (isset($hx['Page_Title']) && trim($hx['Page_Title'])!='')
				fprintf ($outf, '	<title>%s</title>%s', trim($hx['Page_Title']), PHP_EOL);
			else fprintf ($outf, '	<title>Genome Browser</title>%s', PHP_EOL); 
		}
		else if (trim($sn)=='<!-- anno-j ext-all.css -->')
			fprintf ($outf, "\t<link type='text/css' rel='stylesheet' href='%s/css/ext-all.css' />%s", $aj2, PHP_EOL);
		else if (trim($sn)=='<!-- anno-j viewport.css -->')
			fprintf ($outf, "\t<link type='text/css' rel='stylesheet' href='%s/css/viewport.css' />%s", $aj2, PHP_EOL);
		else if (trim($sn)=='<!-- anno-j plugins.css -->')
			fprintf ($outf, "\t<link type='text/css' rel='stylesheet' href='%s/css/plugins.css' />%s", $aj2, PHP_EOL);
		else if (trim($sn)=='<!-- anno-j salk.css -->')
			fprintf ($outf, "\t<link type='text/css' rel='stylesheet' href='%s/css/salk.css' />%s", $aj2, PHP_EOL);
		else if (trim($sn)=='<!-- anno-j ext-base-3.2.js -->')
			fprintf ($outf, '	<script type=\'text/javascript\' src=\'%s\'></script>%s', $aj2.'/js/ext-base-3.2.js', PHP_EOL);
		else if (trim($sn)=='<!-- anno-j ext-all-3.2.js -->')
			fprintf ($outf, '	<script type=\'text/javascript\' src=\'%s\'></script>%s', $aj2.'/js/ext-all-3.2.js', PHP_EOL);
		else if (trim($sn)=='<!-- anno-j excanvas.js -->')
			fprintf ($outf, '	<script type=\'text/javascript\' src=\'%s\'></script>%s', $aj2.'/js/excanvas.js', PHP_EOL);
		else if (trim($sn)=='<!-- anno-j aj-min8-src.js -->')
			fprintf ($outf, '	<script type=\'text/javascript\' src=\'%s\'></script>%s', $aj2.'/js/aj-min8-src.js', PHP_EOL);
		else if (trim($sn)=='<!-- anno-j aj-min9-src.js -->')
			fprintf ($outf, '	<script type=\'text/javascript\' src=\'%s\'></script>%s', $aj2.'/js/aj-min9-src.js', PHP_EOL);
		else if (trim($sn)=='<!-- anno-j aj-min10-src.js -->')
			fprintf ($outf, '	<script type=\'text/javascript\' src=\'%s\'></script>%s', $aj2.'/js/aj-min10-src.js', PHP_EOL);
		else if (trim($sn)=='<!-- anno-j aj-min5-src.js -->')
			fprintf ($outf, '	<script type=\'text/javascript\' src=\'%s\'></script>%s', $aj2.'/js/aj-min5-src.js', PHP_EOL);
		else if (trim($sn)=='<!-- anno-j favicon -->')
			fprintf ($outf, '	<link rel=\'icon\' href=\'%s\' type=\'image/x-icon\'>%s	<link rel=\'shortcut icon\' href=\'%s\' type=\'image/x-icon\'>%s', $aj2.'/img/aj.ico', PHP_EOL, $aj2.'/img/aj.ico', PHP_EOL);
		else if (trim($sn)=='<!-- anno-j script -->')
		{
			fprintf ($outf, "	<script type='text/javascript' src='%s'></script>%s", $page_path.$fg.'.js', PHP_EOL);
			if ($hp==true)
			{
				fprintf ($outf, "%s	<!-- dynamics init -->%s", PHP_EOL, PHP_EOL);
				fprintf ($outf, "<?PHP".PHP_EOL."\t\$queryPost = urldecode(http_build_query (\$_REQUEST));\n");
				fprintf ($outf, "\techo \"\t<script type='text/javascript'>\".PHP_EOL.\"\t\tvar queryPost = '\".\$queryPost.\"';\".PHP_EOL.\"\t</script>\".PHP_EOL;%s?>%s", PHP_EOL, PHP_EOL);
				fprintf ($outf, "	<script type='text/javascript' src='%s'></script>%s", $aj2.'/js/urlinit.js', PHP_EOL);    
			}
		}
		else if (trim($sn)=='<!-- google analytics script -->')
		{
			$gaf = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.google_analytics';
			if (file_exists($gaf))
			{
				$gas = file_get_contents($gaf);
				$ga = explode(PHP_EOL, $gas);
				foreach ($ga as $gz)
				{
					if (strstr($gz, 'script>')!=null || strstr($gz, '<script')!=null)
						fprintf ($outf, "\t%s", trim($gz).PHP_EOL);
					else fprintf ($outf, "\t\t%s", trim($gz).PHP_EOL);	
				}
			}
		} 
		else fprintf ($outf, "%s", rtrim($sn).PHP_EOL);
	}
	fclose($inf);
	fclose($outf);
}


?> 
<head>
	<link type='text/css' rel='stylesheet' href='/aj2/css/util.css' />

	<!-- ExtJS Dependencies -->
	<link type='text/css' rel='stylesheet' href='/aj2/css/ext-all.css' />
	<script type='text/javascript' src='/aj2/js/ext-base-3.2.js'></script>
	<script type='text/javascript' src='/aj2/js/ext-all-3.2.js'></script>

	<!-- Anno-J & jBuilder -->
	<link type='text/css' rel='stylesheet' href='/aj2/css/viewport.css' />
	<link type='text/css' rel='stylesheet' href='/aj2/css/plugins.css' />
	<link type='text/css' rel='stylesheet' href='/aj2/css/salk.css' />
	<script type='text/javascript' src='simple_build_js.php'></script>
</head>
<body>
<P><br>
<table id="x_main" cellspacing="5" border="0" height="40" width="785" align=center>
<tr><td align=center style="font-family: Verdana, sans-serif, Arial; font-size: 1.1em; font-weight: bold; color: #408040;">jBuilder : Anno-J Config Tool &nbsp; &nbsp; &nbsp; </td></tr>
</table>

<P>
<form method=POST>
<br>
<table align=center>
<tr><td>
	<div id="source-div" style="overflow:auto; height:481px;width:360px;border:1px solid #c3daf9;"></div>
</td><td width=36 align=center>
<input type="image" src="move.png" name="Move" title="Transfer node" style="width:20px;height:20px;" onClick="javascript:moveNode(); return false;" onMouseOver="javascript: dockIn(this, 36);" onMouseOut="javacript: dockOut(this,20,2);"><br>
<br><br><br><br><br>
</td><td>
	<div id="tree-div" style="overflow:auto; height:481px;width:360px;border:1px solid #c3daf9;"></div>
</td><td width=5 valign=top height=481>&nbsp;</td>
<td width=60 valign=top height=481>
<table width=80 valign=top height=350><tr width=60><td align=left valign=top width=60 height=350>
<table width=80 hight=350 border=0><tr width=80><td align=left width=80>
</td></tr><tr height=2><td></td></tr><tr><td align=left width=80>

<input type="image" src="edit.png" name="Edit" title="Edit node info" style="width:24px;height:24px;" onClick="javascript:editNode(); return false;" onMouseOver="javascript: dockIn(this, 48);" onMouseOut="javacript: dockOut(this, 24);"><br>
</td></tr><tr height=4><td></td></tr><tr><td align=left width=80>
<input type="image" src="sort.png" name="Util" title="Sort subfolder" style="width:24px;height:24px;" onClick="javascript:sortNode(); return false;" onMouseOver="javascript: dockIn(this, 48);" onMouseOut="javacript: dockOut(this, 24);"><br>
</td></tr><tr height=4><td></td></tr><tr><td align=left width=80>
<input type="image" src="active.png" name="Active" title="Pick active track" style="width:24px;height:24px;" onClick="javascript:activeNode(); return false;" onMouseOver="javascript: dockIn(this, 48);" onMouseOut="javacript: dockOut(this, 24);"><br>
</td></tr><tr height=12><td></td></tr><tr><td align=left width=80>
<input type="image" src="add.png" name="Add" title="Add new folder" style="width:24px;height:24px;" onClick="javascript:addNode(); return false;" onMouseOver="javascript: dockIn(this, 48);" onMouseOut="javacript: dockOut(this, 24);"><br>
</td></tr><tr height=4><td></td></tr><tr><td align=left width=80>
<input type="image" src="del.png" name="Del" title="Remove a node" style="width:24px;height:24px;" onClick="javascript:removeNode(); return false;" onMouseOver="javascript: dockIn(this, 48);" onMouseOut="javacript: dockOut(this, 24);"><br>
</td></tr><tr height=7><td></td></tr><tr><td align=left width=80>
<input type="image" src="collapse.png" name="Expand" title="Expand/Collapse tree" style="width:24px;height:24px;" onClick="javascript:expands(); return false;" onMouseOver="javascript: dockIn(this, 48);" onMouseOut="javacript: dockOut(this, 24);"><br>
</td></tr><tr height=18><td></td></tr><tr><td align=left width=80>
<input type="image" src="save.png" name="Save" title="Test & Save" style="width:36px;height:36px;" onClick="javascript: if (runv()==false) return false;" onMouseOver="javascript: dockIn(this, 64);" onMouseOut="javacript: dockOut(this, 36);"><br>
</td></tr></table>
</td></tr></table>
<table border=0><tr><td width=80 height=120 valign=bottom>
<table valign=bottom height=118 valign=bottom>
<tr with=80><td align=left height=38>
</td></tr><tr with=60><td align=left height=54>
<input type="image" src="rollback.png" name="Rollback" title="Rollback Page" style="width:26px;height:26px;" onClick="javascript: if (rollbackPage()==false) return false;" onMouseOver="javascript: dockIn(this, 54);" onMouseOut="javacript: dockOut(this, 26);"><br>
</td></tr></tr><tr><td align=left valign=top height=55>
<input type="image" src="trash.png" name="Trash" title="Empty Trash" style="width:30px;height:30px;" onClick="javascript: if (trashNode()==false) return false;" onMouseOver="javascript: dockIn(this, 56);" onMouseOut="javacript: dockOut(this, 30);">
</td></tr><tr><td id="x_zoom" align=left valign=bottom style="width:40px;height:5px;">
<span id="x_zoom_h"><font color="#0000AA">+</font></span>
</td></tr></table>
</td></tr></table>
</td></tr>
</table>
<INPUT type=hidden id="x_All_Track" name="AllTrack" value="">
<INPUT type=hidden id="x_Trash_Bin" name="TrashBin" value="">
<INPUT type=hidden id="x_Rollback_Page" name="RollbackPage" value="">
<INPUT type=hidden id="x_All_Track1" name="AllTrack1" value="">
<INPUT type=hidden id="x_All_Track2" name="AllTrack2" value="">
<INPUT type=hidden id="x_All_Track3" name="AllTrack3" value="">
<INPUT type=hidden id="x_All_Track4" name="AllTrack4" value="">
<INPUT type=hidden id="x_All_Track5" name="AllTrack5" value="">
<INPUT type=hidden id="x_All_Track6" name="AllTrack6" value="">
<INPUT type=hidden id="x_All_Track7" name="AllTrack7" value="">
<INPUT type=hidden id="x_All_Track8" name="AllTrack8" value="">
<INPUT type=hidden id="x_All_Track9" name="AllTrack9" value="">

<INPUT type=hidden id="x_All_Track10" name="AllTrack10" value="">
<INPUT type=hidden id="x_All_Track11" name="AllTrack11" value="">
<INPUT type=hidden id="x_All_Track12" name="AllTrack12" value="">
<INPUT type=hidden id="x_All_Track13" name="AllTrack13" value="">
<INPUT type=hidden id="x_All_Track14" name="AllTrack14" value="">
<INPUT type=hidden id="x_All_Track15" name="AllTrack15" value="">
<INPUT type=hidden id="x_All_Track16" name="AllTrack16" value="">
<INPUT type=hidden id="x_All_Track17" name="AllTrack17" value="">
<INPUT type=hidden id="x_All_Track18" name="AllTrack18" value="">
<INPUT type=hidden id="x_All_Track19" name="AllTrack19" value="">

<INPUT type=hidden id="x_All_Track20" name="AllTrack20" value="">
<INPUT type=hidden id="x_All_Track21" name="AllTrack21" value="">
<INPUT type=hidden id="x_All_Track22" name="AllTrack22" value="">
<INPUT type=hidden id="x_All_Track23" name="AllTrack23" value="">
<INPUT type=hidden id="x_All_Track24" name="AllTrack24" value="">
<INPUT type=hidden id="x_All_Track25" name="AllTrack25" value="">
<INPUT type=hidden id="x_All_Track26" name="AllTrack26" value="">
<INPUT type=hidden id="x_All_Track27" name="AllTrack27" value="">
<INPUT type=hidden id="x_All_Track28" name="AllTrack28" value="">
<INPUT type=hidden id="x_All_Track29" name="AllTrack29" value="">

<INPUT type=hidden id="x_All_Track30" name="AllTrack30" value="">
<INPUT type=hidden id="x_All_Track31" name="AllTrack31" value="">
<INPUT type=hidden id="x_All_Track32" name="AllTrack32" value="">
<INPUT type=hidden id="x_All_Track33" name="AllTrack33" value="">
<INPUT type=hidden id="x_All_Track34" name="AllTrack34" value="">
<INPUT type=hidden id="x_All_Track35" name="AllTrack35" value="">
<INPUT type=hidden id="x_All_Track36" name="AllTrack36" value="">
<INPUT type=hidden id="x_All_Track37" name="AllTrack37" value="">
<INPUT type=hidden id="x_All_Track38" name="AllTrack38" value="">
<INPUT type=hidden id="x_All_Track39" name="AllTrack39" value="">

<P><br>
</form>
<div id="x_div" style="display:none;"><div id="x_div2" style="position:relative; top:3px; left:3px; width:490px;"></div></span></div>
<div id="x_div_ec" style="display:none;"><div id="x_div_ec2" style="position:relative; top:1px; left:3px; width:374px;"></div></span></div>
<style type="text/css">
#x_div { position:absolute; top:50; left:400; width:496px; z-index: 111; border: 1px solid #c3daf9; background: #efffff;}
#x_div_ec { position:absolute; top:100; left:480; width:380px; z-index: 112; border: 2px solid #c3daf9; background: #efffff;}
.label { display:inline-block; width: 82px; height: 22px; color: #333399; font-size: 0.75em; text-align: right; }
.label2 { display:inline-block; width: 54px; height: 22px; color: #333399; font-size: 0.75em; text-align: right; }
.field { display:inline-block; width: 154px; height: 22px; color: #333399; font-size: 0.75em; }
.field1 { display:inline-block; width: 260px; height: 22px; color: #333399; font-size: 0.75em; }
.field2 { display:inline-block; width: 360px; height: 22px; color: #333399; font-size: 0.75em; }
</style>

<script>
var div = document.getElementById('x_zoom');
var divm = document.getElementById('x_main');
var divs = document.getElementById('source-div');
var divw = document.getElementById('tree-div');

div.onmousedown = function(evt){
	var dx = evt.x;
	var dy = evt.y;
	var dm = parseInt(divm.style.width);
	var dw = parseInt(divs.style.width);
	var dh = parseInt(divs.style.height);
	var zh = parseInt(div.style.height);
	document.onmousemove = function(e){
		var w = e.pageX - dx;
		var h = e.pageY - dy;
		divs.style.height = (dh+h)+'px';
		divw.style.height = (dh+h)+'px';
		div.style.height = (zh+h)+'px';
		divs.style.width = (dw+w/2)+'px';
		divw.style.width = (dw+w/2)+'px';
	}
	document.onmouseup = function(e){
		document.onmousemove = function(){};
	}
}
</script>
</body>
