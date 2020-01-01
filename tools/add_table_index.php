<?PHP
ini_set('display_errors', 'On');
ini_set('max_execution_time', 300);

require_once '../includes/global_settings.php';
require_once 'util_module.php';
require_once '../includes/common_query.php';

require_once 'login_session2.php';

$err_star = "<font color=red>*</font>";
$err_no = '';
$err_msg = '';
$succ_msg = '';
$fail_msg = '';

$tables = array();

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
                                else if ($sa[0]=='Genome_URL')
                                        $HK['Genome_URL'] = trim($sa[1]);
                        }
                        fclose ($inf);
                        $_REQUEST['Track_URL'] = implode(PHP_EOL, $HK['Track_URL']);
                        if (isset($HK['Genome_URL'])) $_REQUEST['Genome_URL'] =  $HK['Genome_URL'];
			$err_no = 'Success'; $succ_msg = 'Tracks are retrieved from page '.$_REQUEST['Save_File'].'.';
                }
        }
}

if (isset($_REQUEST['Submit']) && $_REQUEST['Submit']=='Submit')
{
	if ($_REQUEST['Track_URL'] == '') { $err_no = 'Track_URL'; $err_msg = ' Must be filled.'; }
	else if ($_REQUEST['Genome_URL'] == '') { $err_no = 'Genome_URL'; $err_msg = ' Must be filled.'; }
	else if (!file_exists($_SERVER['DOCUMENT_ROOT'].trim($_REQUEST['Genome_URL'])))
        {       $err_no = 'Genome_URL'; $err_msg = ' Not exist.'; }
	else
	{
		$track_url = explode(PHP_EOL, trim($_REQUEST['Track_URL']));
		$no = count($track_url);
		if (($assemblies=getGenomeInfo($_SERVER['DOCUMENT_ROOT'].trim($_REQUEST['Genome_URL'])))==false)
		{	$err_no = 'Genome_URL'; $err_msg = ' Can not open.'; }

		for ($i=0; $i<$no; $i++)
		{
			if (!file_exists($_SERVER['DOCUMENT_ROOT'].trim($track_url[$i])))
			{
				$fail_msg .= '<nobr>'.$track_url[$i].'  '.$err_star.'not exist.</nobr><br>'.PHP_EOL;	
			}
			else if (($ta=getTableInfo($_SERVER['DOCUMENT_ROOT'].trim($track_url[$i])))==false)
			{
				$fail_msg .= '<nobr>'.$track_url[$i].'  '.$err_star.'can not open.</nobr><br>'.PHP_EOL;
				unset($track_url[$i]); 
			}
			else
			{
				$ta['Track_URL'] = $track_url[$i];
				$tables[]=$ta;
			}						

		}
		if (($no=count($tables))==0) { $err_no = 'Track_URL'; $err_msg = ' No trusted url.'; }
		else usort($tables, 'sortByServer');
	}
	if ($err_no=='')
	{
		$old_server = '';  $pdo=false;
		//foreach ($tables as $tb)
		for ($i=0, $zn=1; $i<$no; $i++, $zn++)	
		{
			if (!$pdo || $tables[$i]['Server']!=$old_server)
			{
				//echo "hi = ";
				$dsn = 'mysql:host='.trim($tables[$i]['Server']);
				try 
				{
					$pdo = new PDO($dsn, 'mysql', 'rekce');
				} catch (PDOException $e)
				{
					$fail_msg .= '<nobr>'.trim($tables[$i]['Track_URL']).' '.$err_star.' '.$e->getMessage().'</nobr><br>'.PHP_EOL;
					unset($tables[$i]);
					continue;
				}
			}
			//$getQuery = 'get'.trim($tables[$i]['Track_Type']).'Query';
			$action = trim($_REQUEST['Action']);
			$qstr = getIndexQuery($action, trim($tables[$i]['Include']), $tables[$i]['Append']);

			if ($action=='P2S')
			{
				if ($tables[$i]['Include']=='common_reads.php')
				{
					$rn='';
					if ($tables[$i]['Append']==true)
					{
						foreach($assemblies as $assembly)
						{
							$table = $tables[$i]['Table'].$assembly['id'];
							$rn .= Paired2Single($pdo, $table);
						}
					}
					else
					{
						$table = $tables[$i]['Table'];
						$rn = Paired2Single($pdo, $table);
					}
					if ($rn!='') $fail_msg .= '<nobr>'.trim($tables[$i]['Track_URL']).' '.$err_star.$rn.'</nobr><br>'.PHP_EOL;
					else $succ_msg .= '<nobr>'.trim($tables[$i]['Track_URL']).' '.$err_star.'Paired2Single done.</nobr><br>'.PHP_EOL;
				}
				else $fail_msg .= '<nobr>'.trim($tables[$i]['Track_URL']).' '.$err_star.'not common_reads.php module</nobr><br>'.PHP_EOL;
			}
			else
			{
				if ($tables[$i]['Append']==true)
				{
					$rn='';
					foreach($assemblies as $assembly)
					{
						$table = $tables[$i]['Table'].$assembly['id'];
						$query = 'alter table '.$table.' '.$qstr.' ;';
						try {
							$pdo->beginTransaction();
							$stmt = $pdo->exec($query);
							$pdo->commit();
						} catch (PDOException $e) {
							$rn .= '['.$assembly['id'].':] '.$e->getMessage().' ';
						}
					}
					if ($rn!='') $fail_msg .= '<nobr>'.trim($tables[$i]['Track_URL']).' '.$err_star.$rn.'</nobr><br>'.PHP_EOL;
					else $succ_msg .= '<nobr>'.trim($tables[$i]['Track_URL']).' '.$err_star.$qstr.'.</nobr><br>'.PHP_EOL;
				}
				else
				{
					$table = $tables[$i]['Table'];
					$query = 'alter table '.$table.' '.$qstr.' ;';
					try { 
						$pdo->beginTransaction();
						$stmt = $pdo->exec($query);
						$pdo->commit();
						$succ_msg .= '<nobr>'.trim($tables[$i]['Track_URL']).' '.$err_star.$qstr.'.</nobr><br>'.PHP_EOL; 
					} catch (PDOException $e) {
						$fail_msg .= '<nobr>'.trim($tables[$i]['Track_URL']).' '.$err_star.$e->getMessage().'</nobr><br>'.PHP_EOL;
					}
					//echo "SINGLE: ".$query.PHP_EOL;
				}
			}
			$old_server=$tables[$i]['Server'];
			//echo PHP_EOL;
		}
		$err_no='Success';
		//echo "</PRE>";
	}	
}

function getIndexQuery($action, $module, $append=true)
{
	if ($module=='common_wgta.php' || $module=='common_indel.php')
	{
		if($action=="Drop_Index") return 'drop index position';
		if ($append) return 'add index position (position)';
		else return 'add index position (assembly, position)';
	}
	else 
	{
		if($action=="Drop_Index") return 'drop index start';
		if ($append) return 'add index start (start)';
		else return 'add index start (assembly, start)';
	}
}

function Paired2Single($pdo, $table)
{
	$typeA=''; $typeB='';
	$query = 'describe '.$table;
	try
	{
		$stmt = $pdo->prepare($query);
		$stmt->execute();
	} catch (PDOException $e) {
		return $e->getMessage();
	}

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		if($row['Field']=='sequenceA') $typeA=$row['Type'];
		if($row['Field']=='sequenceB') $typeB=$row['Type'];
		//print_r($row);
	}
	if ($typeA!='' && $typeB!='')
	{
		$query = 'select count(*) from '.$table;
		//echo  $query.PHP_EOL;
		try
		{
			$stmt = $pdo->prepare($query);
			$stmt->execute();
			$tno = $stmt->fetchColumn();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
		$query .= " where sequenceB = '' or sequenceB is null ";
		//echo  $query.PHP_EOL;
		try
		{
			$stmt = $pdo->prepare($query);
			$stmt->execute();
			$bno = $stmt->fetchColumn();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
		//echo "Rows in table is ".$tno." while rows of B empty ".$bno.PHP_EOL;
		if ($bno < $tno) return "sequenceB is not empty"; 
		$query = 'alter table '.$table.' drop sequenceB';
		$query2 = 'alter table '.$table.' change sequenceA sequence '.$typeA.' ;';
		//echo $query.PHP_EOL.$query2.PHP_EOL;
		try
		{
			$pdo->beginTransaction();
			$stmt = $pdo->prepare($query);
			$stmt->execute();
			$stmt = $pdo->prepare($query2);
			$stmt->execute();
			$pdo->commit();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
		//echo 'Done'.PHP_EOL;
		return '';
	}
	else return "not a paired end table";	
}

//print_r ($tables);
//echo "</PRE>";
?>
<head>
<link type='text/css' rel='stylesheet' href='<?PHP echo $aj2 ?>/css/util.css' />
</head>
<body>
<P>
<table cellspacing="5" border="0" height="40" width="680" align=center>
<tr><td width=24%></td><td style="font-family: Verdana, sans-serif, Arial; font-size: 1.1em; font-weight: bold; color: #408040;">jBuilder : Add Table Index </td></tr>
</table>
<?PHP if (isset($_REQUEST['Submit'])) { ?>
<P>
<table class="TBL" cellspacing="5" border="0" align=center>
<tr height=25><td class="THL" colspan="3">&nbsp;Adding Index Information</td></tr>
<?PHP if ($succ_msg!='') { ?>
<tr><td class="TDL" width="20%">Success:</td><td class="TDL" colspan="2"><?PHP if ($err_no=='Success') echo $succ_msg; ?></td></tr>
<?PHP } if ($fail_msg!='') { ?>
<tr><td class="TDL" width="20%">Failed:</td><td class="TDL" colspan="2"><?PHP if ($err_no=='Success') echo $fail_msg; ?></td></tr>
<?PHP } ?>
<?PHP if ($err_no!='' && $err_no!='Success') { ?>
<tr><td class="TDL" width="20%">Errors:</td><td class="TDL" colspan="2"><?PHP echo $err_no.': '.$err_msg.' '.$err_star.'marked below.'; ?></td></tr>
<?PHP } ?>
</table>
<?PHP } ?>

<form method="POST">
<input type="SUBMIT" name="Submit" onClick="return false;" style="visibility:hidden;">
<table class="TBL" cellspacing="5" border="0" align=center>
<tr height=25><td class="THL" colspan="3">&nbsp;Adding Indexes</td></tr>

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
<tr><td class="TDL" width="20%" height="35" valign="middle">Genome: <?PHP if ($err_no=='Genome_URL') echo $err_star; ?></td><td class="TDL" width="60%"><INPUT class="CEL" TYPE="text" id="x_Genome_URL" NAME="Genome_URL" VALUE="<?PHP if (isset($_REQUEST['Genome_URL'])) echo $_REQUEST['Genome_URL']; ?>"></td><td class="TDL"> 
<?PHP read_genomes_x_info('genomes'); ?>
</td></tr>
<tr><td class="TDL" width="20%" height="35" valign="middle">Action: </td><td class="TDL" width="60%">&nbsp; 
<INPUT class="CEL" style="width:12px" TYPE="RADIO" NAME="Action" VALUE="Add_Index"<?PHP if (!isset($_REQUEST['Action']) || $_REQUEST['Action']=='Add_Index') echo ' checked'; ?>>Add Index &nbsp;&nbsp; 
<INPUT class="CEL" style="width:12px" TYPE="RADIO" NAME="Action" VALUE="Drop_Index"<?PHP if (isset($_REQUEST['Action']) && $_REQUEST['Action']=='Drop_Index') echo ' checked'; ?>>Drop Index &nbsp;&nbsp;
<?PHP if (isset($_SESSION['aj_group']) && $_SESSION['aj_group']=='admin') { ?>
<INPUT class="CEL" style="width:12px" TYPE="RADIO" NAME="Action" VALUE="P2S"<?PHP if (isset($_REQUEST['Action']) && $_REQUEST['Action']=='P2S') echo ' checked'; ?>>Paired-&gt;Single
<?PHP } ?>
</td><td class="TDL"> </td></tr>
</table>

<P>
<table class="noborder" width="580" cellspacing="5" height="40" align=center>
<tr><td width="25%">
<INPUT TYPE="SUBMIT" Name="Logout" value="Logout" style="width:60px;height:22px"> &nbsp;
<INPUT TYPE="BUTTON" Name="Reset" onClick="document.getElementById('x_Genome_URL').value=''; document.getElementById('x_Track_URL').value=''; " value="Clear" style="width:60px;height:22px">

</td><td align="center" width="50%">
<INPUT TYPE="SUBMIT" Name="Submit" value="Submit" style="width:120px;height:25px;font-weight:bold">
</td><td align="right" width="25%">
<INPUT TYPE="SUBMIT" Name="Submit" value="Retrieve" style="width:75px;height:22px">
</td></tr></table>
</form>


