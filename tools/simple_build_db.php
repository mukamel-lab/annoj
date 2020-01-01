<?PHP
ini_set('display_errors', 'On');
require_once 'util_module.php';
require_once 'login_session.php';

if (isset($_REQUEST['page']) && !isset($_REQUEST['node']))
{
	$pg = trim($_REQUEST['page']);
	open_pages_one($_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/', $aj_user, $pg, '', false);
}

if (isset($_REQUEST['node']))
{
	echo "[";
	$nd = trim($_REQUEST['node']);
	$rn = explode("::", $nd);
	$self = $_SESSION['aj_user'];

	if ($rn[0]=='Source')
	{
		echo '{"text":"Tables","id":"Tables","cls":"folder",iconCls:"salk_data"}';
		echo ',{"text":"Pages","id":"Pages","cls":"folder",iconCls:"salk_page"}';
		echo ',{"text":"Tracks","id":"Tracks","cls":"folder",iconCls:"salk_track"}';
	}

	if ($rn[0]=='Tables')
	{
		echo '{"text":"Simple","id":"Simple","cls":"folder",iconCls:"salk_simple"}';
		echo ',{"text":"MySQL","id":"MySQL","cls":"folder",iconCls:"salk_mysql"}';
		echo ',{"text":"Proxy","id":"Proxy","cls":"folder",iconCls:"salk_proxy"}';
		echo ',{"text":"Gene Models","id":"Models","cls":"folder",iconCls:"silk_bricks"}';
		echo ',{"text":"Suffixes","id":"Suffixes","cls":"folder",iconCls:"silk_group"}';	
	}
	else if ($rn[0]=='Pages')
	{
		if (!isset($rn[1]) || $rn[1]=='')
		{
			echo '{"text":"'.$self.'","id":"Pages::'.$self.'","cls":"folder",iconCls:"silk_user_red","Data_Type":"PageUser"}';
			echo ',{"text":"Final","id":"Pages::Final","cls":"folder",iconCls:"silk_tick","Data_Type":"PageUser"}';
			echo ',{"text":"Group","id":"Pages::Group","cls":"folder",iconCls:"silk_group","Data_Type":"PageUser"}';
			echo ',{"text":"CORS","id":"Pages::CORS","cls":"folder",iconCls:"silk_asterisk_orange","Data_Type":"PageUser"}'; 
		}
		else if ($rn[1]==$self || $rn[1]=="Final")
		{
			$dir = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/';
			if (!isset($rn[2]))
			{
				//open_pages_dir($dir, $rn[1], false);
				open_pages_dir($dir, $self, false, $rn[1]=='Final');
			}
			else
			{
				if (!isset($rn[3])) $fd='';
				else $fd=$rn[3];
				//open_pages_one($dir, $rn[1], $rn[2], $fd, false);
				open_pages_one($dir, $self, $rn[2], $fd, false, $rn[1]=='Final');
			}
		}
		else if ($rn[1]=='CORS')
		{
			$dir = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/';
			if (!isset($rn[2]))
				open_pages_dir($dir, $rn[1], false);
			else
			{
				if (!isset($rn[3])) $fd='';
				else $fd=$rn[3];
				open_pages_one($dir, $rn[1], $rn[2], $fd, false);
			}
		}
		else if ($rn[1]=='Group')
		{
			if (!isset($rn[2]))
			{
				open_global_dir($_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/', $self);
			}
			else 
			{
				if (user_allow_share_pages($rn[2], $aj_user, $_SESSION['aj_group']))
				{
					if (!isset($rn[3])) open_pages_dir($_SERVER['DOCUMENT_ROOT'].$aj2."/pages/", $rn[2], true); 
					else
					{
						if (!isset($rn[4])) $fd='';
						else $fd=$rn[4];
						open_pages_one($_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/', $rn[2], $rn[3], $fd, true);
					}
				}
			}
		}
	}
	else if ($rn[0]=='Tracks')
	{
		if (!isset($rn[1]) || $rn[1]=='')
		{
			echo '{"text":"'.$self.'","id":"Tracks::'.$self.'","cls":"folder"}';
			//open_tracks_record ($_SERVER['DOCUMENT_ROOT'].$aj2."/tracks/", $self);
		}
		else if ($rn[1]==$self)
		{
			if (!isset($rn[2])) $fd='';
			else $fd=$rn[2];

			$home = $_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$self.'/';

			open_tracks_dir ($fd, $home);
		}
		else
		{
			if (!isset($rn[2])) $fd='';
			else $fd=$rn[2];
			open_tracks_one($_SERVER['DOCUMENT_ROOT'].$aj2."/tracks/", $self, $rn[1], $fd);
		}
	}
	else if ($rn[0]=="Proxy")
	{
		include_once '../includes/global_settings.php';
		if (!isset($rn[1]) || $rn[1]=='')
		{
			$pd = $proxy_cache_dir;
			$dh = opendir($pd); $k=0;
			while (($fl = readdir($dh)) != NULL)
			{
				if ($fl[0] != '.')
				{
					if ($k==1) echo ",";
					echo '{"text":"'.$fl.'","id":"Proxy::'.$fl.'","cls":"folder","Data_Type":"PageFolder"}';
					$k=1;
				}
			}
			closedir($dh);
		}
		else if (!isset($rn[2]) || $rn[2]=='')
		{
			$silk = array("silk_page_green", "silk_page", "silk_page_red");
			$pd = $proxy_cache_dir.'/'.$rn[1];
			$ids = "Proxy::".$rn[1];
			$dh = opendir($pd); $k=0;
			while (($fl = readdir($dh)) != NULL)
			{
				if ($fl[0]=='.' && substr($fl, strlen($fl)-3)=='.db')
				{
					if ($k>0) echo ",";
					echo '{"text":"'.urldecode(substr($fl,1,strpos($fl, ".db")-1)).'","id":"'.$ids.'::'.substr($fl,1,strpos($fl, ".db")-1).'","cls":"folder",iconCls:"'.$silk[$k%3].'","Data_Type":"PageBase"}';
					$k++;
				}
			}
			closedir($dh);
		}
		else
		{
			if (!isset($rn[3])) $fd='';
			else $fd=$rn[3];
			$ids = "Proxy::".$rn[1]."::".$rn[2];
			$fn = $proxy_cache_dir.'/'.$rn[1].'/.'.$rn[2].'.db';
			open_proxy_pages_one ($fn, $ids, $rn[2], $fd);
		}
	}
	else if ($rn[0]=="Simple")
	{
		if (!isset($rn[1]) || $rn[1]=='')
		{
			$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$self.'/.source_simple_home_directories';
			if (file_exists($fn))  $da = open_simple_home ($fn);
			if (!isset($da) || count($da)==0)  $da = array('/' => '/');

			$k=0;
			foreach ($da as $dk => $du)
			{ 
				if ($k==1) echo ",";
				echo '{"text":"'.$dk.'","id":"Simple::'.$du.'","cls":"folder","Data_Type":"SimpleFolder"}';
				$k=1;
			}
		}
		else
		{
			$hm = $rn[1];
			unset($rn[0]);
			unset($rn[1]);
			if (!isset($rn[2])) $dir = '';
			else $dir = implode('/', $rn);
			open_simple_dir ($dir, $hm);
		}
	}
	else if ($rn[0]=='MySQL')
	{
		if (!isset($rn[1]) || $rn[1]=='')
		{
			$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.servers';
			if (file_exists($fn))  $da = open_simple_home ($fn);
			if (!isset($da) || count($da)==0)  error("No MySQL server listed.");

			$k=0;
			foreach ($da as $dk => $du)
			{
				if ($k==1) echo ",";
				echo '{"text":"'.$dk.'","id":"MySQL::'.$du.'","cls":"folder","iconCls":"salk_server","Data_Type":"MySQLServer"}';
				$k=1;
			}
		}
		else
		{
			if (!isset($rn[2])) $db = '';
			else $db = $rn[2];
			open_mysql_service ($rn[1], $db);
		}
	}
	else if ($rn[0]=='Suffixes')
	{
		open_suffix_dir();
	}
	else if ($rn[0]=='Models')
	{
		if (!isset($rn[1])) $mdl = '';
		else $mdl = trim($rn[1]);
		open_models_dir($mdl);
	}
	echo "]";		
}

function open_models_dir ($mdl)
{
	global $aj2, $aj_user;
	$k=0;
	$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/genomes/.models';
	$inf = fopen($fn, "r");
	while (($sn = fgets($inf)) != NULL)
	{
		if ($sn[0]=='#' || $sn[0]=='\n' || $sn[0]=='\r') continue;
		$sa = explode("\t", $sn);
		if ($mdl=='')
		{
			if ($k!=0) echo ',';
			echo '{"text":"Annotation Models '.trim($sa[2]).'","id":"Models::'.trim($sa[0]).'","cls":"folder","Data_Type":"PageFolder"}';
			$k++;
		}
		else if ($mdl == trim($sa[0]))
		{
			if ($k!=0) echo ',';
			echo '{"text":"Gene Models ('.trim($sa[2]).')","id":"Gene Models '.trim($sa[2]).'","leaf":true,"cls":"file",iconCls:"silk_bricks","Data_Type":"PageTrack","Track_Type":"ModelsTrack","Height":"100","Scale":1,"Control":true,"Icon":"silk_bricks","Track_URL":"'.$aj2.'/models/'.$sa[1],'"}';
			$k++;
		}
	}
	fclose ($inf);
}

function open_suffix_dir()
{
	global $aj2, $aj_user;
	$dir = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user;
	$dh = opendir($dir); $k=0;
	while (($fl = readdir($dh)) != NULL)
	{
		if ($fl != '.' && $fl != '..' && strstr($fl, ".suffix_")!=null)
		{
			$fv = substr(trim($fl), strlen(".suffix_"), -5);
			if ($k!=0) echo ",";
			echo '{"text":"'.trim($fv).'","id":"Suffix::'.trim($fv).'","leaf":true,"cls":"file",iconCls:"silk_page_red","Data_Type":"SuffixBoth"';
			$fn = $dir.'/'.$fl;
			$inf = fopen($fn, "r");
			while (($sn = fgets($inf)) != NULL)
			{
				$dn = explode(" =:= ", $sn);
				if (strstr($dn[0], "_Options")==NULL)
					echo ',"'.$dn[0].'":"'.trim($dn[1]).'"';
			}
			fclose ($inf);
			echo '}';
			$k++;
		}
	}
	closedir($dh);
}

function open_mysql_service ($server, $dbname)
{
	include_once '../includes/global_settings.php';
	$dsn = 'mysql:host='.$server; $k=0;
	if ($dbname!='') $dsn .= ';dbname='.$dbname;
	try
	{
		$pdo = new PDO ($dsn, $user, $pass);
	} catch (PDOException $e)
	{
		die ($e->getMessage());
	}
	
	if ($dbname=='')  $qstmt = "show databases";
	else  $qstmt = "select TABLE_NAME from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA = '$dbname'";

	$stmt = $pdo->prepare($qstmt);
	$stmt->execute();

	if ($dbname=='')
	{
		while ($r = $stmt->fetch(PDO::FETCH_NUM))
		{
			if ($k==1) echo ",";
			echo '{"text":"'.trim($r[0]).'","id":"MySQL::'.$server.'::'.trim($r[0]).'","cls":"folder","Path":"'.$r[0].'","Data_Type":"MySQLDatabase"}';
			$k=1;
		}
	}
	else
	{
		while ($r = $stmt->fetch(PDO::FETCH_NUM))
		{
			if ($k==1) echo ",";
			echo '{"text":"'.trim($r[0]).'","id":"MySQL::'.$server.'::'.$dbname.'::'.trim($r[0]).'",leaf:true,cls:"file",iconCls:"silk_page","Data_Type":"MySQLData","Table_List":"'.$dbname.'.'.trim($r[0]).'","Title_List":"'.$r[0].'","Info_List":"'.$r[0].'","Track_URL":"'.$r[0].'.php","Server":"'.$server.'"}'; 
			$k=1;		
		}
	}
	$pdo = null;
}

function open_simple_home($fn)
{
	$da = array ();
	if (($inf=fopen($fn, "r"))!=NULL)
	{
		while ($sn=fgets($inf))
		{
			if ($sn[0]=='#') continue;
			$sa = explode("\t", $sn);
			if (isset($sa[1]) && trim($sa[1])!='')
				$da[trim($sa[0])]=trim($sa[1]);
			else if (trim($sa[0])!='') 
				$da[trim($sa[0])] = trim($sa[0]);
		}
		fclose($inf);
	}
	return $da;
}

function open_simple_dir($dir, $home)
{
	global $aj2, $aj_user;
	$inc = array("SimpleModels" => "simple_models", "SimpleMask" => "simple_mask", "SimpleAligns" => "simple_aligns", "SimpleReads" => "simple_reads", "SimplePairedEnd" => "simple_pe", "SimpleSmallReads" => "simple_smrna", "SimpleMeth" => "simple_wgta", "SimpleIntensity" => "simple_intensity", "SimpleMicroarray" => "simple_microarray", "SimpleSNPs" => "simple_snps", "SimpleIndel" => "simple_indel");
	$icons = array("SimpleModels" => "silk_bricks", "SimpleMask" => "salk_bed", "SimpleAligns" => "silk_bricks", "SimpleReads" => "salk_dna", "SimplePairedEnd" => "salk_mrna", "SimpleSmallReads" => "salk_smrna", "SimpleMeth" => "salk_meth", "SimpleIntensity" => "silk_bricks", "SimpleMicroarray" => "silk_bricks", "SimpleSNPs" => "salk_snp", "SimpleIndel" => "salk_dna");
	$trk = array("SimpleModels" => "ModelsTrack", "SimpleMask" => "MaskTrack", "SimpleAligns" => "AlignsTrack", "SimpleReads" => "ReadsTrack", "SimplePairedEnd" => "PairedEndTrack", "SimpleSmallReads" => "SmallReadsTrack", "SimpleMeth" => "MethTrack", "SimpleIntensity" => "IntensityTrack", "SimpleMicroarray" => "MicroarrayTrack", "SimpleSNPs" => "SNPsTrack", "SimpleIndel" => "IntensityTrack");

	$dh = opendir($home.'/'.$dir); $k=0; $readno=0; 
	while (($fl = readdir($dh)) != NULL)
	{
		$dr = $home.'/'.$dir."/".$fl;
		if ($fl[0] != '.' && is_dir($dr))
		{
			$leaf=0; $share=1;
			$fv = $dr."/.type";
			if (file_exists($fv))
			{
				$sn = trim(file_get_contents($fv));
				if (strncmp($sn, "Simple", 6)==0) $leaf=1;
				$fv = $dr."/.readno";
				if (file_exists($fv)) 
					$readno = trim(file_get_contents($fv));
				$share = simple_db_share($dr);
			}
			if ($share) 
			{
			if ($k==1) echo ",";
			if ($leaf==1)
			{
				$ttl = $fl; $tfl = $fl; $tnl = $fl;
				$fv = $dr."/.title";
				if (file_exists($fv)) $ttl = addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.info";
				if (file_exists($fv)) $tfl = addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.name";
				if (file_exists($fv)) $tnl = addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.path";
				if (file_exists($fv)) $tpl = addslashes(trim(file_get_contents($fv)));
				else $tpl = "";

				echo '{"text":"'.trim($tnl).'","id":"Simple::'.$home.'::'.$dir.'/'.$fl.'","leaf":true,"cls":"file","iconCls":"'.$icons[$sn].'","Data_Type":"SimpleData","Table_List":"'.$home.'/'.$dir.'/'.$fl.'","Title_List":"'.$ttl.'","Info_List":"'.$tfl.'","Track_URL":"'.$fl.'.php","Include":"'.$inc[$sn].'.php","Track_Type":"'.$trk[$sn].'","Icon":"'.$icons[$sn];
				if ($tpl != "") echo '","Track_Path":"'.$tpl;
				$fv = $dr."/.scale";
				if (file_exists($fv)) echo '","Scale":"'.addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.single";
				if (file_exists($fv)) echo '","Single":"'.addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.class";
				if (file_exists($fv)) echo '","Class":"'.addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.color";
				if (file_exists($fv)) echo '","Color":"'.addslashes(trim(file_get_contents($fv))); 
				$fv = $dr."/.height";
				if (file_exists($fv)) echo '","Height":"'.addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.boxHeight";
				if (file_exists($fv)) echo '","boxHeight":"'.addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.boxWidthMin";
				if (file_exists($fv)) echo '","boxWidthMin":"'.addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.showControls";
				if (file_exists($fv)) echo '","Control":"'.addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.alignControl";
				if (file_exists($fv)) echo '","alignControl":"'.addslashes(trim(file_get_contents($fv))); 
				$fv = $dr."/.showWalks";
				if (file_exists($fv)) echo '","showWalks":"'.addslashes(trim(file_get_contents($fv)));
				$fv = $dr."/.policy";
				if (file_exists($fv)) echo '","Policy":"'.addslashes(trim(file_get_contents($fv)));
				if ($readno>0) echo '","Reads":"'.$readno;
				echo '"}';	
			}
			else
			{
				echo '{"text":"'.trim($fl).'","id":"Simple::'.$home.'::'.$dir.'/'.$fl.'","cls":"folder","Path":"'.$dir.'","Data_Type":"SimpleFolder"}';
			}
			$k=1; $readno=0; 
			}
		}
	}
	closedir($dh);
}


function open_global_dir($dir, $self)
{
	$silk = array("silk_user_green", "silk_user", "silk_user_orange");
	$dh = opendir($dir); $k=0;
	while (($fl = readdir($dh)) != NULL)
	{
		if ($fl != '.' && $fl != '..' && $fl!=$self)
		{
			if ($k!=0) echo ",";
			echo '{"text":"'.trim($fl).'","id":"Pages::Group::'.trim($fl).'","cls":"folder",iconCls:"'.$silk[$k%3].'","Data_Type":"PageUser"}';
			$k++;
		}
	}
	closedir($dh);
}

function open_pages_dir($dir, $user, $group, $final=false)
{
	global $aj2;
	if ($final) $ids='Pages::Final';
	else
	{
		if ($group==true) $ids='Pages::Group::'.$user.'/';
		else $ids='Pages::'.$user;
	}
	$silk = array("silk_page_green", "silk_page", "silk_page_red");

	$fls = array(); $k = 0; 
	$dh = opendir($dir.$user); 
	while (($fl = readdir($dh)) != NULL)
	{
		if ($fl != '.' && $fl != '..' && $fl[0]=='.' && strpos($fl, ".db")==(strlen($fl)-3))
		{
			$fls[filemtime($dir.$user.'/'.$fl)] = $fl;
		}
	}
	closedir($dh);
	krsort ($fls);
	foreach ($fls as $fl)
	{
		$pl = substr($fl,1,(strlen($fl)-4)).'.php';
		if ($user=="CORS")
		{
			if ($k!=0) echo ",";
			echo '{"text":"'.substr($fl,1,strpos($fl, ".db")-1).'","id":"'.$ids.'::'.substr($fl,1,strpos($fl, ".db")-1).'","cls":"folder",iconCls:"'.$silk[$k%3].'","Data_Type":"PageBase"}';
			$k++;
		}
		else
		{
			if ($final)
			{
				$tl = $dir.$user."/.final_".substr($fl,1,(strlen($fl)-4));
				if (file_exists($tl))
				{
					if ($k!=0) echo ",";
					echo '{"text":"'.substr($fl,1,strpos($fl, ".db")-1).'","id":"'.$ids.'::'.substr($fl,1,strpos($fl, ".db")-1).'","cls":"folder",iconCls:"'.$silk[$k%3].'","Data_Type":"PageBase","href":"'.$aj2.'/pages/'.$user.'/'.$pl.'","hrefTarget":"_blank"}';
					$k++;
				} 
			}
			else
			{
				$tl = $dir.$user."/.final_".substr($fl,1,(strlen($fl)-4));
				if ($group || !file_exists($tl))
				{
					if ($k!=0) echo ",";
					echo '{"text":"'.substr($fl,1,strpos($fl, ".db")-1).'","id":"'.$ids.'::'.substr($fl,1,strpos($fl, ".db")-1).'","cls":"folder",iconCls:"'.$silk[$k%3].'","Data_Type":"PageBase","href":"'.$aj2.'/pages/'.$user.'/'.$pl.'","hrefTarget":"_blank"}';
					$k++;
				} 
			}
		}
	}
}

function open_proxy_pages_one ($fn, $ids, $page, $path)
{
	open_db_pages_one ($fn, $ids, $page, $path, false);
}
	
function open_pages_one ($dir, $user, $page, $path, $group, $final=false)
{
	if ($final) $ids='Pages::Final::'.$page;
	else if ($group==true) $ids='Pages::Group::'.$user.'::'.$page;
	else $ids='Pages::'.$user.'::'.$page;
	$fn = $dir.$user.'/.'.$page.'.db';
	open_db_pages_one ($fn, $ids, $page, $path, $final);
}

function open_db_pages_one ($fn, $ids, $page, $path, $final=false)
{
	$inf = fopen($fn, "r");
	$HK=array(); $HF=array();
	while (($sn=fgets($inf))!=NULL)
	{
		$sa = explode(" =:= ", rtrim($sn, PHP_EOL));
		if ($sa[0]=='Keys' || $sa[0]=='keys')
		{
			$sk = explode(' =,= ', trim($sa[1]));
			$ky = array();
			foreach ($sk as $i => $k) $ky[trim($k)]=$i;
		}
		else if ($sa[0]=='Track')
		{
			$HK[] = explode(' =,= ', $sa[1]);
		}
		else
		{
			if (!isset($sa[1])) continue;
			$HF[$sa[0]]=trim($sa[1]);
		}
	}
	fclose ($inf);

	$k=0; $dirs=array(); $paths=array();

	foreach ($HK as $hk) $paths[$hk[$ky['Track_Path']]]=1;

	if ($path=='')
	{
		echo '{"text":"Active","id":"'.$ids.'::Active","cls":"folder","Data_Type":"ActiveFolder"}'; $k=1;
		foreach($paths as $ps => $ys)
		{
			if (strpos($ps, '/')) $ps=substr($ps, 0, strpos($ps, '/'));
			if (isset($dirs[$ps])) continue;
			if ($k==1) echo ",";
			echo '{"text":"'.$ps.'","id":"'.$ids.'::'.$ps.'","cls":"folder","Data_Type":"PageFolder"}';
			$k=1; $dirs[$ps]=true;
		}
		$hf = ''; 
		foreach($HF as $hp => $hv)
		{
			if ($k==1) $hf .= ",";
			if ($hp=='Title') $hf .= '"Page_Title":"'.$hv.'"';
			else $hf .= '"'.$hp.'":"'.$hv.'"';
		}
		if ($final) $hf .= ',"FinalVersion":true';
		echo ',{"text":".suffixBoth","id":"Suffix::'.$page.'","leaf":true,"cls":"file","iconCls":"silk_page_red","Data_Type":"SuffixBoth"'.$hf.'}';
		
	}
	else if ($path=='Active')
	{
		$AK=array(); $k==0;
		foreach ($HK as $hk)
		{
			if ($hk[$ky['active']]!=false && $hk[$ky['active']]!='')
			{
				if (isset($AK[$hk[$ky['active']]])) $AK[] = $hk;
				else $AK[$hk[$ky['active']]] = $hk;
			}
		}
		if (count($AK)>0)
		{
			ksort($AK);
			foreach ($AK as $hk)
			{
				if ($k==1) echo ",";
				echo '{"text":"'.$hk[$ky['Track_Name']].'","id":"active_'.$hk[$ky['Track_URL']].'","leaf":true,"cls":"file",iconCls:"'.$hk[$ky['Icon']].'","Track_URL":"'.$hk[$ky['Track_URL']].'","Data_Type":"ActiveTrack"}';
				$k=1;
			}
		}
	}
	else
	{
		$n=strlen($path); 
		foreach($HK as $hk)
		{
			if ($path==$hk[$ky['Track_Path']])
			{
				if ($k==1) echo ",";
				$no = count($hk); $pa = '';
				for ($i=1;$i<$no;$i++)
				{
					if ($i>1) $pa .= ',';
					$pa .= '"'.trim($sk[$i]).'":"'.$hk[$i].'"';
				}
				if (trim($sk[$no-1])=='Data_Type' && $hk[$no-1]!='')
					echo '{"text":"'.$hk[$ky['Track_Name']].'","id":"'.$hk[$ky['Track_URL']].'","leaf":true,"cls":"file",iconCls:"'.$hk[$ky['Icon']].'",'.$pa.'}';
				else
					echo '{"text":"'.$hk[$ky['Track_Name']].'","id":"'.$hk[$ky['Track_URL']].'","leaf":true,"cls":"file",iconCls:"'.$hk[$ky['Icon']].'",'.$pa.',"Data_Type":"PageTrack"}';
				$k=1;
			}
			else if (strncmp($path, $hk[$ky['Track_Path']], $n)==0 && $hk[$ky['Track_Path']][$n]=='/')
			{
				$ps = substr($hk[$ky['Track_Path']], $n+1);
				if (strpos($ps, '/')>0) $ps=substr($ps,0,strpos($ps,'/'));
				$id=$ids.'::'.$path.'/'.$ps;
				if (isset($dirs[$id])) continue;
				if ($k==1) echo ",";
				echo '{"text":"'.$ps.'","id":"'.$id.'","cls":"folder","Data_Type":"PageFolder"}';
				$k=1; $dirs[$id]=true;
			} 
		}
	}       
}

function open_tracks_dir($dir, $home)
{
	global $aj2, $aj_user;

	$dh = opendir($home.'/'.$dir); $k=0;
	while (($fl = readdir($dh)) != NULL)
	{
		$dr = $home.'/'.$dir."/".$fl;
		if ($fl[0] != '.')
		{
			if (is_dir($dr))
			{
				if ($k==1) echo ",";
				echo '{"text":"'.trim($fl).'","id":"Tracks::'.$aj_user.'::'.$dir.'/'.$fl.'","cls":"folder","Path":"'.$dir.'","Data_Type":"PageFolder"}';
				$k=1;
			}
			else if ('.php' == strtolower(substr($fl, -4)))
			{
				if ($k==1) echo ",";
				echo '{"text":"'.trim($fl).'","id":"Tracks::'.$aj_user.'::'.$dir.'/'.trim($fl).'","leaf":"true","cls":"file","iconCls":"silk_page","Data_Type":"PageTrack","Track_URL":"'.$aj2.'/tracks/'.$aj_user.'/'.$dir.'/'.trim($fl).'","Track_Path":"'.$dir.'"}';
				$k=1;
			}
		}
	}
	closedir($dh);
}

function open_tracks_record($dir, $user)
{
	$dh = opendir($dir.$user); 
	while (($fl = readdir($dh)) != NULL)
	{
		if ((strncmp($fl, ".Record_", 8)==0 || strncmp($fl, ".Simple_", 8)==0) && strpos($fl, ".db")==(strlen($fl)-3))
		{
			echo ",";
			$fd = substr($fl,8,strpos($fl, ".db")-8);
			if (strncmp($fl, ".Record_", 8)==0) $icn = "salk_mysql";
			else $icn = "salk_simple";
			echo '{"text":"'.$fd.'","id":"Tracks::'.$fd.'","cls":"folder","iconCls":"'.$icn.'","Data_Type":"TrackBase"}';
		}
	}
	closedir($dh);
}

function open_tracks_one ($dir, $user, $record, $fd)
{
	$k=0;
	$fn = $dir.$user.'/.Record_'.$record.'.db'; 
	if (file_exists($fn))
	{
		$k = open_tracks_once ($dir, $user, $record, '.Record_', $fd);
	}
	$fn = $dir.$user.'/.Simple_'.$record.'.db';
	if (file_exists($fn))
	{
		if ($k!=0) echo ",";
		open_tracks_once ($dir, $user, $record, '.Simple_', $fd);
	}
}

function open_tracks_once ($dir, $user, $record, $type, $fd)
{
	global $aj2;
	$path=''; $k=0; $mdl=false; $silk = array("silk_page_green", "silk_page");
	if ($type=='.Simple_') { $dt = 'SimpleTrack';  $dd = 'SimpleFolder'; }
	else { $dt = 'MySQLTrack'; $dd = 'MySQLFolder'; }

	$fn = $dir.$user.'/'.$type.$record.'.db';
	$inf = fopen($fn, "r");
	$sv = ''; 
	$HF = array();
	while (($sn=fgets($inf))!=NULL)
	{
		$sa = explode(' =:= ', $sn);
		if ($sa[0]=='Path') { $path=trim($sa[1]); $sv .= ',"Path":"'.$path.'"'; }
		if ($sa[0]=='Server') { $server=trim($sa[1]); $sv .= ',"Server":"'.$server.'"'; }
		if ($sa[0]=='Include') { $include=trim($sa[1]); $sv .= ',"Include":"'.$include.'"'; }
		if ($sa[0]=='File_List') $fa = explode("\t", trim($sa[1]));
		if ($sa[0]=='Table_List') $ba = explode("\t", trim($sa[1]));
		if ($sa[0]=='Title_List') $ta = explode("\t", trim($sa[1]));
		if ($sa[0]=='Info_List') $ia = explode("\t", trim($sa[1]));
		if ($sa[0]=='Name_List') $na = explode("\t", trim($sa[1]));
		if ($sa[0]=='Options_List') $oa = explode("\t", trim($sa[1]));
		if ($sa[0]=='Model') $mdl = true;
		if (strstr($sa[0], "_List")==NULL && strstr($sa[0], "_OPTION")==NULL && $sa[0]!='Force')
			$HF[$sa[0]] = trim($sa[1]);

	}
	fclose($inf);
	
	$n = strlen($fd);
	$path = trim($path, '/');
	if ($n<strlen($path))
	{
		$nf = substr($path, $n);
		$nf = trim($nf, '/');
		if (strstr($nf, '/')!=NULL)
			$nf = substr($nf, 0, strpos($nf, '/'));
		if ($fd=='') $fdx = $nf;
		else $fdx = $fd.'/'.$nf;
		echo '{"text":"'.$nf.'","id":"Tracks::'.$record.'::'.$fdx.'","cls":"folder","iconCls":"'.$icn.'","Data_Type":"'.$dd.'"'.$sv.'}';
		return;
	}
	$n=count($ba);
	for ($i=0; $i<$n; $i++)
	{
		if ($k!=0) echo ",";
		if (isset($fa) && isset($fa[$i]) && trim($fa[$i])!='')
		{
			$ul = trim($fa[$i]);	
		}
		else
		{
			if ($dt=='SimpleTrack') $ul = substr(trim($ba[$i]). strrpos(trim($ba[$i]), '/')+1).'.php';
			else $ul = rtrim(substr(trim($ba[$i]), strpos(trim($ba[$i]), '.')+1),'_-').'.php';
		}

		if (isset($na) && isset($na[$i]) && trim($na[$i])!='') $name = trim($na[$i]);
		else $name = trim($ba[$i]); //$ul;

		if ($mdl) $url = $aj2.'/models/'.$ul;
		else $url = $aj2.'/tracks/'.$user.'/'.trim($path).'/'.$ul;

		$hp = ',"Table_List":"'.trim($ba[$i]).'","Title_List":"'.trim($ta[$i]).'","Info_List":"'.trim($ia[$i]).'","File_List":"'.$ul.'","Name_List":"'.$name.'","Track_Name":"'.$name.'","Track_URL":"'.$url.'"'.$sv;
		if (isset($oa) && isset($oa[$i]) && trim($oa[$i])!='') $hp .= ',"Options_List":"'.trim($oa[$i]).'"'; 
		echo '{"text":"'.$name.'","id":"'.$url.'","leaf":true,"cls":"file",iconCls:"'.$silk[$k%2].'","Data_Type":"'.$dt.'"'.$hp.'}';
		$k++;

	}
	$hp='';
	foreach ($HF as $hk => $hv)
		$hp .= ',"'.$hk.'":"'.trim($hv).'"';
	if ($k!=0) echo ",";
	echo '{"text":".suffixBoth","id":"SuffixOf'.$record.'","leaf":true,"cls":"file",iconCls:"silk_page_red","Data_Type":"SuffixBoth"'.$hp.'}';

	return $k;
}

function simple_db_share ($dir)
{
	global $aj_user;
	$agroup= $_SESSION['aj_group'];

	$fu = $dir."/.user"; $fg = $dir."/.group";

	if (!file_exists($fu) && !file_exists($fg)) return true;

	if (file_exists($fu))
	{
		$sa = explode(",", trim(file_get_contents($fu)));
		if (in_array($aj_user, $sa)) return true;
	}
	if (file_exists($fg))
	{
		$sa = explode(",", trim(file_get_contents($fg)));
		if (in_array($agroup, $sa)) return true;
	}

	return false;
}

function user_allow_share ($owner, $user, $group, $page)
{
	global $aj2;

	if ($group=='admin' || $group=='super') return true;
	
	$fn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$owner.'/.allow_user_shares';
	if (file_exists($fn))
	{
		$sa = explode(PHP_EOL, trim(file_get_contents($fn)));
		$no = count($sa);
		for ($i=0; $i<$no; $i++)
		{
			$en = explode("\t", $sa[$i]);
			if ($en[0]=='All' || $en[0]==$user || $en[0]==$group)
			{
				if ($page=='pages') return true;
				if ($page=='tracks' && $en[1]=='PagesTracks') return true;
			}
		}
	}
	return false;
}

function user_allow_share_pages ($owner, $user, $group)
{
	return user_allow_share ($owner, $user, $group, 'pages');
}

function user_allow_share_tracks ($owner, $user, $group)
{
	return user_allow_share ($owner, $user, $group, 'tracks');
}
	
?>
