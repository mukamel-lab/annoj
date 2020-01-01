<?PHP
ini_set('display_errors', 'On');

//$db = "/Library/WebServer/Documents/aj2/pages/hchen/.MX-Proxy.db";
//echo "<pre>".http_build_query($_REQUEST).PHP_EOL;

$m = 0; $n = 0;
if (isset($_REQUEST['Assembly'])) $RA = $_REQUEST['Assembly'];
if (isset($_REQUEST['Chromosome'])) $RA = $_REQUEST['Chromosome'];
if (isset($_REQUEST['Position'])) $RN = $_REQUEST['Position'];
if (isset($_REQUEST['Location']))
{
	$an = explode(":", $_REQUEST['Location']);
	$RA = $an[0]; $RN = $an[1];
}
if (isset($_REQUEST['Paths'])) 
{
	$RP = explode(";", $_REQUEST['Paths']);
	$m = count($RP);
}
if (isset($_REQUEST['Tracks'])) 
{
	$RT = explode(";", $_REQUEST['Tracks']);
	$n = count($RT);
}

if (file_exists($db))
{
	$HT = array(); $HP = array(); $k = 0; 
	$inf = fopen($db, "r");
	while (($sn = fgets($inf)) != NULL)
	{
		$da = explode(" =:= ", rtrim($sn,"\n"));
		if ($da[0] == "Keys")
		{
			$HK = explode(" =,= ", $da[1]);
		}
		else if ($da[0] == "Track")
		{
			$db = explode(" =,= ", $da[1]);
			$dz = array_combine($HK, $db);
			if (isset($RP) && in_array($dz['Track_Path'], $RP))
			{
				if (isset($RT) && in_array($dz['Track_Name'], $RT))
				{
					$dz['active'] = 2 + $n*array_search ($dz['Track_Path'], $RP) + array_search ($dz['Track_Name'], $RT);
				}
				else if (isset($RT) && in_array($dz['Track_Path'], $RT))
				{
					$dz['active'] = 5120 + $k;
					$k++;
				}
				else if ($dz['active'] > 1) $dz['active'] += $m*$n;
				$HT[] = $dz;
			}
			else if (!isset($RP))
			{
				if (isset($RT) && in_array($dz['Track_Name'], $RT))
					$dz['active'] = 2 + array_search ($dz['Track_Name'], $RT);
				else if ($dz['active'] > 1) $dz['active'] += $n;
				$HT[] = $dz;
			}
		}
		else
		{
			$HP[$da[0]] = trim($da[1]);
		}
		if (isset($RA)) $HP['Assembly'] = $RA;
		if (isset($RN)) $HP['Position'] = $RN;
	}
	fclose($inf);

	streamConfigScript ($HT, $HP);
}

function streamConfigScript ($HT, $HP)
{
	printf ("\tAnnoJ.config = {%s\t\ttracks : [%s", PHP_EOL.PHP_EOL, PHP_EOL.PHP_EOL);
	$grp='Nothing2GuessOnWay'; $g=0; $k=1; $HA = array(); 
	foreach ($HT as $ht)
	{
		if ($ht['Track_Path']!=$grp)
		{
			$g++; $k=1;
		}
		printf ("\t\t\t{%s", PHP_EOL);
		printf ("\t\t\t\tid : '%s',%s", $g."_".$k, PHP_EOL);
		printf ("\t\t\t\tname : '%s',%s", addslashes($ht['Track_Name']), PHP_EOL);
		printf ("\t\t\t\ttype : '%s',%s", $ht['Track_Type'], PHP_EOL);
		printf ("\t\t\t\tpath : '%s',%s", addslashes($ht['Track_Path']), PHP_EOL);
		printf ("\t\t\t\tdata : '%s',%s", $ht['Track_URL'], PHP_EOL);
		if (isset($ht['Icon']) && $ht['Icon']!='')
			printf ("\t\t\t\ticonCls : '%s',%s", $ht['Icon'], PHP_EOL);
		if (!isset($ht['Height']) || $ht['Height']=='') $ht['Height'] = 40;
		printf ("\t\t\t\theight : %s,%s", $ht['Height'], PHP_EOL);
		if (!isset($ht['Scale']) || $ht['Scale']=='') $ht['Scale'] = 1;
		printf ("\t\t\t\tscale : %s,%s", $ht['Scale'], PHP_EOL);
		if (isset($ht['boxHeight']) && $ht['boxHeight']!='' && $ht['boxHeight']>0)
			printf ("\t\t\t\tboxHeight : %s,%s", $ht['boxHeight'], PHP_EOL);

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
				printf ("\t\t\t\tcolor : { %s },%s", $colorStr, PHP_EOL);
		}
		if (isset($ht['Single']) && $ht['Single']!='')
			printf ("\t\t\t\tsingle : 1,%s", PHP_EOL);
		if (isset($ht['Control']) && $ht['Control']!='')
			printf ("\t\t\t\tshowControls : 1,%s", PHP_EOL);
		if (isset($ht['NoArrows']) && $ht['NoArrows']!='')
			printf ("\t\t\t\tshowArrows : 0,%s", PHP_EOL);
		if (isset($ht['NoLabels']) && $ht['NoLabels']!='')
			printf ("\t\t\t\tshowLabels : 0,%s", PHP_EOL);
		if (isset($ht['active']) && $ht['active'] > 0)
		{
				$HA[$ht['active']] = $g."_".$k;
		}

		printf ("\t\t\t},%s", PHP_EOL);

		$grp=$ht['Track_Path'];
		$k++;
	}
	printf ("\t\t],%s", PHP_EOL.PHP_EOL);

	ksort($HA); $k=0;
	printf ("\t\tactive : [");
	foreach ($HA as $ha)
	{
		if ($k==1) printf (", ");
		printf ("'%s'", $ha);
		$k=1;
	}
	printf ("],%s", PHP_EOL.PHP_EOL);

	printf ("\t\ttitle : '%s',%s", addslashes($HP['Page_Title']), PHP_EOL);
	printf ("\t\tgenome : '%s',%s", $HP['Genome_URL'], PHP_EOL);
	if (isset($HP['Bookmarks_URL']))
		printf ("\t\tbookmarks : '%s',%s", $HP['Bookmarks_URL'], PHP_EOL);
	printf ("\t\tlocation : {%s", PHP_EOL);
	printf ("\t\t\tassembly : '%s',%s", $HP['Assembly'], PHP_EOL);
	printf ("\t\t\tposition : '%s',%s", $HP['Position'], PHP_EOL);
	printf ("\t\t\tbases : %s,%s", $HP['Bases'], PHP_EOL);
	printf ("\t\t\tpixels : %s,%s", $HP['Pixels'], PHP_EOL);
	printf ("\t\t},%s\t\tadmin : {%s", PHP_EOL, PHP_EOL);
	printf ("\t\t\tname : '%s',%s", addslashes($HP['Author']), PHP_EOL);
	printf ("\t\t\temail : '%s',%s", $HP['Email'], PHP_EOL);
	printf ("\t\t\tnotes : '%s',%s", addslashes($HP['Notes']), PHP_EOL);
	printf ("\t\t},%s\t\tsettings : {%s", PHP_EOL, PHP_EOL);
	if (isset($HP['Baseline']) && $HP['Baseline']!='') printf ("\t\t\tbaseline : %s,%s", $HP['Baseline'], PHP_EOL);
	if (isset($HP['Display']) && $HP['Display']!='' && $HP['Display']!='0' && $HP['Display']!=0) printf ("\t\t\tdisplay : %s,%s", $HP['Display'], PHP_EOL);
	if (isset($HP['Activate']) && $HP['Activate']!='' && $HP['Activate']!='0' && $HP['Activate']!=0) printf ("\t\t\tactivate : %s,%s", $HP['Activate'], PHP_EOL);
	if (isset($HP['Activate']) && $HP['Activate']!='' && $HP['Activate']!='0' && $HP['Activate']!=0) printf ("\t\t\tactivate : %s,%s", $HP['Activate'], PHP_EOL);
	if (isset($HP['GlobalScale']) && $HP['GlobalScale']!='') printf ("\t\t\tscale : %s,%s", $HP['GlobalScale'], PHP_EOL);
	if (isset($HP['Multi']) && $HP['Multi']!='') printf ("\t\t\tmulti : %s,%s", $HP['Multi'], PHP_EOL);
	printf ("\t\t\tyaxis : %s,%s", $HP['Yaxis'], PHP_EOL);
	if (isset($HP['HiC_d']) && $HP['HiC_d']!='' && $HP['HiC_d']!='0' && $HP['HiC_d']!=0) printf ("\t\t\thic_d : %s,%s", $HP['HiC_d'], PHP_EOL);
	printf ("\t\t},%s\t\tcitation : '%s',%s", PHP_EOL, addslashes($HP['Citation']), PHP_EOL);
	if (isset($HP['jBuilder']))
		printf ("\t\tjbuilder : %s,%s", addslashes($HP['jBuilder']), PHP_EOL);
	printf ("\t};%s", PHP_EOL);
}

?>
