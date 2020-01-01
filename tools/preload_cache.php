<?PHP
ini_set('display_errors', 'On');
ini_set('max_execution_time', 28800);

require_once '../includes/global_settings.php';
require_once 'util_module.php';
require_once '../includes/common_query.php';
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
			$HK['Genome_URL'] = '';
			$HK['Track_Type'] = array();
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
					$HK['Track_Type'][] = $tk[$ky['Track_Type']];
				}
				else if ($sa[0]=='Genome_URL')
					$HK['Genome_URL'] = trim($sa[1]);
			}
			fclose ($inf);
			$_REQUEST['Track_URL'] = implode(PHP_EOL, $HK['Track_URL']);
			$_REQUEST['Track_Type'] = implode(PHP_EOL, $HK['Track_Type']);
			if (isset($HK['Genome_URL'])) $_REQUEST['Genome_URL'] =  $HK['Genome_URL'];
		}
	}
}

$tables = array();

if (isset($_REQUEST['Submit']) && $_REQUEST['Submit']=='Submit')
{
	if ($_REQUEST['Track_URL'] == '') { $err_no = 'Track_URL'; $err_msg = ' Must be filled.'; }
	else if ($_REQUEST['Track_Type'] == '') { $err_no = 'Track_Type'; $err_msg = ' Must be filled.'; }
	else if ($_REQUEST['Genome_URL'] == '') { $err_no = 'Genome_URL'; $err_msg = ' Must be filled.'; }
	else if (!file_exists($_SERVER['DOCUMENT_ROOT'].trim($_REQUEST['Genome_URL'])))
        {       $err_no = 'Genome_URL'; $err_msg = ' Not exist.'; }
	else
	{
		$track_url = explode(PHP_EOL, trim($_REQUEST['Track_URL']));
		$track_type = explode(PHP_EOL, trim($_REQUEST['Track_Type']));
		$no = count($track_url);
		if ($no != count($track_type))
		{	$err_no = 'Track_Type'; $err_msg = ' Row number must be the same as Track URL.'; }
		else if (($assemblies=getGenomeInfo($_SERVER['DOCUMENT_ROOT'].trim($_REQUEST['Genome_URL'])))==false)
		{	$err_no = 'Genome_URL'; $err_msg = ' Can not open.'; }
	}

	if ($err_no=='')
	{
		for ($i=0; $i<$no; $i++)
		{
			if (($ta=getTableInfo($_SERVER['DOCUMENT_ROOT'].trim($track_url[$i])))==false)
			{
				$fail_msg .= '<nobr>'.$track_url[$i].' not exist or can not open file.</nobr><br>'.PHP_EOL;
				unset($track_url[$i]); unset($track_type[$i]);
			}
			else
			{
				$ta['Track_URL'] = $track_url[$i];
				$ta['Track_Type'] = $track_type[$i];
				$ta['Action']=$ta['Track_Type'];
				if ($ta['Include']=="custom_models.php") $ta['Action'] = "CustomModelsTrack";
				if ($ta['Include']=="custom_reads.php") $ta['Action'] = "CustomReadsTrack";
				$ta['Policy'] = getPolicy (trim($ta['Track_Type']));
				$tables[]=$ta;
			}						

		}
		usort($tables, 'sortByServer');
		if (!isset($_REQUEST['Resolution']) || trim($_REQUEST['Resolution'])=='')
			$_REQUEST['Resolution'] = '1:1, 10:1';                                        //1:1, 10:1, 100:1, 1000:1, 10000:1, 1:10, 1:100';
		$rsn = explode(',', $_REQUEST['Resolution']);
		$resolution = array();
		foreach ($rsn as $ren)
		{
			$rzn = explode(':', $ren);
			$resolution[] = array ('bases' => trim($rzn[0]), 'pixels' =>  trim($rzn[1]));
		}
		$zn = count($resolution);
		for ($i=0; $i<$no; $i++)
		{
			$xn = count($tables[$i]['Policy']);
			for ($k=0; $k<$xn; $k++)
			{
				for ($j=0; $j<$zn; $j++)
				{
					if ($tables[$i]['Policy'][$k]['pixels'] == $resolution[$j]['pixels'] && $tables[$i]['Policy'][$k]['bases'] == $resolution[$j]['bases'])
					{
						$tables[$i]['Policy'][$k]['run'] = 1; break;
					}
				}
			}
		}
	}
	if ($err_no=='')
	{
		echo '<pre>';

		$old_server = '';  $pdo=false;
		//foreach ($tables as $tb)
		for ($i=0, $zn=1; $i<$no; $i++, $zn++)	
		{
			echo $zn.'. '.trim($tables[$i]['Track_URL']).' : '.PHP_EOL;
			if (!$pdo || $tables[$i]['Server']!=$old_server)
			{
				$dsn = 'mysql:host='.trim($tables[$i]['Server']);
				try 
				{
					$pdo = new PDO($dsn, 'mysql', 'rekce');
				} catch (PDOException $e)
				{
        				//die ($e->getMessage());
					$fail_msg .= trim($tables[$i]['Track_URL']).' '.$err_star.' '.$e->getMessage().PHP_EOL;
					unset($tables[$i]);
					continue;
				}
			}
			$getQuery = 'get'.trim($tables[$i]['Action']).'Query';

			foreach($assemblies as $assembly)
			{
				if ($tables[$i]['Append']==true)
					$table = $tables[$i]['Table'].$assembly['id'];
				else  $table = $tables[$i]['Table'];
				$length = $assembly['size'];

				echo '['.$assembly['id'].']';
				
				foreach ( $tables[$i]['Policy'] as $py)
				{
					$left=0;
					$bases= $py['bases'];
					$pixels = $py['pixels'];
					$cache =  $py['cache'];
					if ($py['run']==0) continue;
					echo '['.$bases.':'.$pixels.']';
					if (isset($tables[$i]['Options'])) $optz = $tables[$i]['Options'];
					else $optz = "";
					if (strncasecmp($tables[$i]['Action'], "Custom", 6)==0) $ktrl=1;
                        		else $ktrl=0;

					while ($left < $length)
					{
						$right = $left+$cache-1;
						echo '.';
						if ($ktrl==1)
							$getQuery($pdo, $table, $assembly['id'], $left, $right, $bases, $pixels, false, $optz);
						else 
							$getQuery($pdo, $table, $assembly['id'], $left, $right, $bases, $pixels, false);
						$left += $cache;
					}
					echo PHP_EOL;
					flush();
				}
				echo PHP_EOL;
			}
			$old_server=$tables[$i]['Server'];
			echo PHP_EOL;
		}
		echo "</pre>";
	}
}
//print_r($track_url);
//print_r($assemblies);
//print_r ($tables);
?>
<head>
<link type='text/css' rel='stylesheet' href='<?PHP echo $aj2 ?>/css/util.css' />
</head>
<body>
<P>
<table cellspacing="5" border="0" height="40" width="580" align=center>
<tr><td width=24%></td><td style="font-family: Verdana, sans-serif, Arial; font-size: 1.1em; font-weight: bold; color: #408040;">jBuilder : Preload Caching </td></tr>
</table>
<?PHP if (isset($_REQUEST['Submit']) && $err_no!='') { ?>
<P>
<table class="TBL" cellspacing="5" border="0" align=center>
<tr><td class="THL" colspan="3">&nbsp;Cachinging Information</td></tr>
<tr><td class="TDL" width="20%"><?PHP if ($err_no=='Success') echo 'Success:'; else echo 'Error:'; ?></td><td class="TDL" colspan="2">
<?PHP if ($err_no=='Success') echo $err_msg; else echo $err_no.': '.$err_msg.'. '.$err_star.' marked below.'; ?>
</td></tr></table>
<?PHP } ?>

<form method="POST">
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
<tr><td class="THL" colspan="3">&nbsp;Caching Tracks</td></tr>
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
<TEXTAREA class="CEL" ROWS="9" id="x_Track_URL" NAME="Track_URL"><?PHP if (isset($_REQUEST['Track_URL'])) echo $_REQUEST['Track_URL']; ?></TEXTAREA></td><td class="TDL"><?PHP if ($err_no=='Track_URL') echo $err_star.$err_msg; ?></td></tr>
<tr><td class="TDL">Track Type: <?PHP if ($err_no=='Track_Type') echo $err_star; ?></td><td class="TDL" width="60%">
<TEXTAREA class="CEL" ROWS="9" id="x_Track_Type" NAME="Track_Type"><?PHP if (isset($_REQUEST['Track_Type'])) echo $_REQUEST['Track_Type']; ?></TEXTAREA></td><td class="TDL"><?PHP if ($err_no=='Track_Type') echo $err_star.$err_msg; ?></td></tr>
<tr><td class="TDL" width="20%" height="30" valign="top">Genome: <?PHP if ($err_no=='Genome_URL') echo $err_star; ?></td><td class="TDL" width="60%"><INPUT class="CEL" TYPE="text" id="x_Genome_URL" NAME="Genome_URL" VALUE="<?PHP if (isset($_REQUEST['Genome_URL'])) echo $_REQUEST['Genome_URL']; ?>"></td><td class="TDL">
<?PHP read_genomes_x_info('genomes'); ?>
</td></tr>

<tr><td class="TDL" width="20%" height="33" valign="top">Resolution:</td><td class="TDL" width="60%"><INPUT class="CEL" TYPE="text" id="x_Resolution" NAME="Resolution" 
VALUE="<?PHP if (isset($_REQUEST['Resolution'])) echo $_REQUEST['Resolution']; else echo '1:1, 10:1'; ?>"></td>
<td class="TDL">
<select class="RCEL" NAME="Resolution_Options" onChange="if (this.value!='') document.getElementById('x_Resolution').value += ', '+this.value;">
<option value="">Add from</option>
<option value="1:1">1 : 1  bases:pixels
<option value="10:1">10 : 1 
<option value="100:1">100 : 1 
<option value="1000:1">1,000 : 1 
<option value="10000:1">10,000 : 1 
<option value="1:10">1 : 10 
<option value="1:100">1 : 100 
</select>
</td></tr>
</table>

<P>
<table class="noborder" width="580" cellspacing="5" height="40" align=center>
<tr><td>
<INPUT TYPE="SUBMIT" Name="Logout" value="Logout" style="width:60px;height:22px"> &nbsp;
<INPUT TYPE="BUTTON" Name="Reset" onClick="document.getElementById('x_Genome_URL').value=''; document.getElementById('x_Track_Type').value=''; document.getElementById('x_Track_URL').value=''; document.getElementById('x_Save_File').value=''; document.getElementById('x_Resolution').value='1:1, 10:1'; " value="Clear" style="width:60px;height:22px">
</td><td align="center" width="40%">
<INPUT TYPE="SUBMIT" Name="Submit" value="Submit" style="width:120px;height:25px;font-weight:bold">
</td><td align="right" width="30%">
<INPUT TYPE="SUBMIT" Name="Submit" value="Retrieve" style="width:75px;height:22px">
</td></tr></table>
</form>


