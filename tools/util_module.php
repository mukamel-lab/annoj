<?PHP

//HTTP_Redirect
function http_redirect ($url)
{
	include ($url);
	exit ();
}

function http_cors ()
{
	return "header('Access-Control-Allow-Origin: *');".PHP_EOL."header('Access-Control-Allow-Headers: X-Requested-With');".PHP_EOL;
}

//Function aj_error_check
function aj_error_check($aj_var=NULL)
{
	if ($aj_var==NULL)
		$aj_var = array ("Provider_Name", "Provider_Mail", "Organism", "Table_List", "Title_List", "Include", "Server", "Genome_URL");
	foreach ($aj_var as $var)
	{
		if (!isset($_REQUEST[$var]) || $_REQUEST[$var]=='')
		{
			if ($var=="Server" && strstr($_REQUEST["Include"], "simple_")!=null) ;
			else return $var;
		}
	}
	$nt = count(explode(PHP_EOL, trim($_REQUEST['Table_List'])));
	$nf = count(explode(PHP_EOL, trim($_REQUEST['File_List'])));

	if ($nt!=$nf)
	{
		if ($nf==1)
		{
			if ($nt!=count(explode(PHP_EOL, trim($_REQUEST['Name_List'])))) return 'Name_List_No';
		}
		else if ($nf>1) return 'File_List_No';
	}

	if ($nf>0 && $nf!=count(explode(PHP_EOL, trim($_REQUEST['Title_List'])))) return 'Title_List_No';
	if ((isset($_REQUEST['Info_List']) && $_REQUEST['Info_List']!=NULL) && $nf>0 &&
		$nf!=count(explode(PHP_EOL, trim($_REQUEST['Info_List'])))) return 'Info_List_No';
	
	return 'No Error';
}

//Function aj_echo
function aj_echo ($aj_var)
{
	if (isset($_REQUEST[$aj_var]) && $_REQUEST[$aj_var]!='') echo $_REQUEST[$aj_var];
}

//function err_echo
function err_echo($err_msg=NULL)
{
	echo "<font color=red>*</font> ";
	if ($err_msg!=NULL) echo $err_msg;
}

function aj_self($self)
{
	$sa = explode('/', $self);
	$sc = count($sa);
	$aj2 = '';
	for ($i=1;$i<$sc-2;$i++) $aj2 .= '/'.$sa[$i];
	return $aj2;
}

//group match
function group_confirmation ($igroup, $dgroup)
{
	$ig = explode(',', $igroup);
	$dg = explode(',', $dgroup);
	if ($igroup=='') return trim($dg[0]);
	return implode(',', array_intersect($ig, $dg));
}

//user deletion
function user_del2 ($user, $owner, $group)
{
	global $cache_dir, $self;

	if ($group != 'admin') return $user." can not be deleted.";

	$access_path = $cache_dir.'/.htaccess/.htaccess_md5_db';

	if (!file_exists($access_path))
		return "Pass file is not existed."; 

	if (($inf = fopen($access_path, 'r'))!=NULL)
	{
		$ua = array ();
		while ($un=fgets($inf))
		{
			if (trim($un)!='') $ua[] = explode("\t", trim($un));
			$un='';
		}
		fclose ($inf);
		if (($outf = fopen($access_path, 'w'))!=NULL)
		{
			foreach ($ua as $ud)
			{
				if ($ud[0]!=$user)
				{
					fprintf($outf, "%s%s", implode("\t", $ud), PHP_EOL);
				}
			}
			fclose($outf);
			return '';
		}
		else return "Pass file is not writable.";
	}
	else return "Pass file is not accessible."; 
}

//user deletion
function user_del ($user, $owner, $group)
{
	global $cache_dir, $self;

	if ($user == $owner) return $user." can not be deleted.";

	$access_path_md5 = $cache_dir.'/.htaccess/.htaccess_md5_db.tmp.'.trim($user);
	
	if ($group == 'admin')
	{
		if (file_exists($access_path_md5)) unlink ($access_path_md5);
	}
	else
	{
		$ur = explode("\t", file_get_contents($access_path_md5));
		if ($ur[2]==$group && $ur[3]==$owner && $ur[5][0]=='0')
		{
			if (file_exists($access_path_md5)) unlink ($access_path_md5);
		}
		else return $user." can not be deleted.";
	}
	return '';
}

//user addition
function user_add ($user, $name, $email, $group, $priority)
{
	global $cache_dir, $self;

	$access_path_md5 = $cache_dir.'/.htaccess/.htaccess_md5_db';

	if (file_exists($access_path_md5.'.tmp.'.trim($user))) 
		return "User ID is already existed.";
	$nu = $user."\t\t".$group."\t".$name."\t".$email."\t".$priority;
	$outf = fopen($access_path_md5.'.tmp.'.trim($user), "w");
	fprintf($outf, "%s", $nu);
	fclose($outf);

	return '';
}

//user confirmation
function user_confirmation ($user, $pass, $group='')
{
	global $cache_dir, $self;

	$k=0; $access_path = $cache_dir.'/.htaccess/.htaccess_md5_db';
	$enpass = md5(trim($pass));
	if (file_exists($access_path) && ($inf = fopen($access_path, 'r'))!=null)
	{
		while ($un=fgets($inf))
		{
			$tn = explode('	', $un);
			if ($user==trim($tn[0]))
			{ 
				if ($enpass==trim($tn[1]))
				{
					$k=2;
					$group2 = group_confirmation($group, trim($tn[2]));
					$priority = trim($tn[5]);
				}
				else $k=1;
				break;
			}
		}
		fclose ($inf);
	}
	if ($k==0 && (trim($user)!='' && trim($pass!='')) && file_exists($access_path.'.tmp.'.trim($user)))
	{
		$inf = fopen($access_path.'.tmp.'.trim($user), 'r');
		$un=fgets($inf);

		$tn = explode('	', trim($un));
		if ($user==trim($tn[0]))
		{
			$priority = trim($tn[5]);
			if ($priority[0]!='0')
			{
				$tn[1]=$enpass;
				$outf = fopen($access_path, 'a');
				fprintf($outf, "%s", implode('	', $tn).PHP_EOL);
				fclose($outf);
				$k=2;
			}
			else
			{
				if (trim($tn[1])=='') 
				{
					$tn[1]=$enpass;
					$outf = fopen($access_path.'.tmp.'.trim($user), 'w');
					fprintf($outf, "%s", implode("\t", $tn).PHP_EOL);
					fclose($outf);
					$k=2;
				}
				else if (trim($tn[1]) == $enpass) $k=2;
			}
			$group2 = group_confirmation($group, trim($tn[2]));

			//Create user working directories: pages, tracks, suffix ...
			if ($priority[0]!='0')
			{
				$aj2 = aj_self($_SERVER['REQUEST_URI']);
				$ud = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$user;
				if (!file_exists($ud)) mkdir($ud, 0755, true);
				$ud = $_SERVER['DOCUMENT_ROOT'].$aj2.'/tracks/'.$user;
				if (!file_exists($ud)) mkdir($ud, 0755, true);
				$ud = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$user;
				if (!file_exists($ud)) mkdir($ud, 0755, true);
			}
		}
		fclose($inf);
	}

	if ($k==2 && !empty($group2))
	{
		$_SESSION['aj_user']=trim($user);
		$_SESSION['aj_group']=$group2;
		$_SESSION['aj_root']=aj_self($self);
		$_SESSION['aj_priority']=$priority;
		return true;
	}
	return false;
}

//verify_visitor
function verify_visitor($priority, $visitor)
{
	if (!isset($priority) || !isset($visitor) || $priority=='' || $visitor=='') return true;
	if ($priority=='Public') return true;
	if ($visitor==$_SESSION['aj_user']) return true;
	if ($priority=='Protected')
	{
		$agroup = explode(',', $_SESSION['aj_group']);
		foreach ($agroup as $group)
		{
			if (trim($visitor)==trim($group)) return true;
			if (trim($group)=='super' || trim($group)=='admin') return true;
		}
	}
	return false;
}

function enKey($value)
{
	$key = "SiCqDJtpSADFo9zVnSj39Uh57CtdyF6R";
	$text = $value;
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
	return $crypttext;
}

function deKey($value)
{
	$key = "SiCqDJtpSADFo9zVnSj39Uh57CtdyF6R";
	$crypttext = $value;
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
	return trim($decrypttext);
}

//usort 
function sortByServer($a, $b)
{
	//return strcmp($a['Server'], $b['Server']);
	return sortByField($a, $b, 'Server');
}

function sortByField($a, $b, $field)
{
	return strcmp($a[$field], $b[$field]);
}

//Get track policy 
function getPolicy ($module)
{
	$p_1_100 = array('bases' => 1, 'pixels' => 100, 'cache' => 1000, 'run' => 0);
	$p_1_10 = array('bases' => 1, 'pixels' => 10, 'cache' => 1000, 'run' => 0);
	$p_1_1 = array('bases' => 1, 'pixels' => 1, 'cache' => 10000, 'run' => 0);
	$p_10_1 = array('bases' => 10, 'pixels' => 1, 'cache' => 100000, 'run' => 0);
	$p_100_1 = array('bases' => 100, 'pixels' => 1, 'cache' => 1000000, 'run' => 0);
	$p_1000_1 = array('bases' => 1000, 'pixels' => 1, 'cache' => 10000000, 'run' => 0);
	$p_10000_1 = array('bases' => 10000, 'pixels' => 1, 'cache' => 100000000, 'run' => 0);

	if ($module==='ReadsTrack' || $module==='PairedEndTrack' || $module==='SmallReadsTrack' || $module==='HiCTrack')
		$policy = array($p_1_100, $p_1_1, $p_10_1, $p_100_1, $p_1000_1, $p_10000_1);
	else if ($module==='IntensityTrack')
		$policy = array($p_1_1, $p_10_1, $p_100_1, $p_1000_1, $p_10000_1);
	else if ($module==='ModelsTrack')
		$policy = array($p_1_1, $p_10_1, $p_100_1, $p_1000_1);
	else if ($module==='MaskTrack' || $module==='AlignsTrack' || $module==='SNPsTrack')
		$policy = array($p_1_1, $p_10_1, $p_100_1, $p_1000_1);
	else if ($module==='MicroarrayTrack' || $module==='MethTrack')
		$policy = array($p_1_1, $p_10_1, $p_100_1, $p_1000_1, $p_10000_1);
	else return false;

	return $policy;
}

//Get server, table, module info from track
function getTableInfo ($fn)
{
	$tableInfo = array(); $sp=chr(34).chr(39).chr(40).chr(41).chr(32).chr(44).chr(59);
	$tableInfo['Append'] = false;
	$tableInfo['ibase'] = 0;
	if (($inf=fopen($fn, 'r'))==NULL) return false;

	while ($sn = fgets($inf))
	{
		$st = trim($sn);
		if (strncasecmp($st, '$provider_name', 14)==0)
		{
			$sa = explode('=', $st);
			$tableInfo['Provider_Name'] = trim($sa[1], $sp);
		}
		else if (strncasecmp($st, '$provider_email', 15)==0)
		{
			$sa = explode('=', $st);
			$tableInfo['Provider_Mail'] = trim($sa[1], $sp);
		}
		else if (strncasecmp($st, '$species', 8)==0)
		{
			$sa = explode('=', $st);
			$tableInfo['Organism'] = trim($sa[1], $sp);
		}
		else if (strncasecmp($st, '$append_assembly', 16)==0)
		{
			$sa = explode('=', $st);
			if (strtolower(trim($sa[1], $sp))=='true')
				$tableInfo['Append'] = true;
		}
		else if (strncasecmp($st, '$server', 7)==0)
		{
			$sa = explode('=', $st);
			$tableInfo['Server'] = trim($sa[1], $sp);
		}
		else if (strncasecmp($st, '$table', 6)==0)
		{
			$sa = explode('=', $st);
			$tableInfo['Table'] = trim($sa[1], $sp);
		}
		else if (strncasecmp($st, '$title', 6)==0)
		{
			$sa = explode('=', $st);
			$tableInfo['Title'] = trim($sa[1], $sp);
		}
		else if (strncasecmp($st, '$info', 5)==0)
		{
			$sa = explode('=', $st);
			$tableInfo['Info'] = trim($sa[1], $sp);

		}
		else if (strncasecmp($st, '$link', 5)==0)
		{
			$vn=explode(';', $sn);
			$tn=explode('=', $vn[0]);

			$sr = explode(',', substr(trim($tn[1]), strpos(trim($tn[1]),'(')+1));
			$tableInfo['Server']=trim(trim($sr[0]), $sp);
		}
		else if (strncasecmp($st, '$options', 7)==0)
		{
			$vn=explode(';', $sn);
			$tn=explode('=', $vn[0]);
			$tableInfo['Options']=trim(trim($tn[1]), $sp);
		}
		else if (strncasecmp($st, '$ibase', 6)==0)
		{
			$vn=explode(';', $sn);
			$tn=explode('=', $vn[0]);
			$tableInfo['ibase']=trim(trim($tn[1]), $sp);
		}
		else if (strncasecmp($st, 'require', 7)==0 || strncasecmp($st, 'include', 7)==0)
		{
			if (strstr($st, 'common_')!=null)
				$tableInfo['Include'] = trim(substr($st, strpos($st, 'common_')), $sp);
			else if (strstr($st, 'custom_')!=null)
				$tableInfo['Include'] = trim(substr($st, strpos($st, 'custom_')), $sp);
			else if (strstr($st, 'simple_')!=null)
				$tableInfo['Include'] = trim(substr($st, strpos($st, 'simple_')), $sp);
		}
	}
	fclose($inf);

	return $tableInfo;
}

//Get assemblies infos from a genome file
function getGenomeInfo ($fn)
{
	$found=false; $assembly=array();
	if (($inf=fopen($fn, 'r'))==NULL) return false;

	while ($sn = fgets($inf))
	{
		if (($a=strpos($sn, 'assemblies'))!=false && ($b=strpos($sn, '=>'))!=false && ($c=strpos($sn,'array'))!=false)
		{
			$found=true; continue;
		}
		if (!$found) continue;

		if (strstr($sn, 'array')!=NULL && strstr($sn, 'id')!=NULL && strstr($sn, 'size')!=NULL)
		{
			$sb = explode(',', trim(trim(substr($sn, strpos($sn, 'array')+5)), ',()'));
			$sba = explode('=>', $sb[0]);
			$sbb = explode('=>', $sb[1]);
			$assembly[] = array('id' => trim($sba[1], "'() "), 'size' => trim($sbb[1], "'() "));
		}
		if (strstr($sn, ';')!=NULL) break;
	}
	return $assembly;
}

//read_genomes_x_info($fn); $fn = 'genomes', 'organisms', 'models', 'includes', 'simple_includes' or 'servers' in $aj2/genomes/ folder 2013-06-06 
function read_genomes_x_info($fn)
{
	global $aj2;
	if (!isset($aj2)) $aj2 == $_SESSION('aj_root');
	if ($fn=='genome' || $fn=='genomes') { $gn='Genome'; $gl='x_Genome_URL'; }
	else if ($fn=='organism' || $fn=='organisms') { $gn='Organism'; $gl='x_Organism'; }
	else if ($fn=='model' || $fn=='models') { $gn='Model'; $gl='x_Model'; }
	else if ($fn=='include' || $fn=='includes') { $gn='Include'; $gl='x_Include'; }
	else if ($fn=='mysql_include' || $fn=='mysql_includes') { $gn='Include'; $gl='x_Include'; }
	else if ($fn=='simple_include' || $fn=='simple_includes') { $gn='Include'; $gl='x_Include'; }
	else if ($fn=='server' || $fn=='servers') { $gn='Server'; $gl='x_Server'; }
	else if ($fn=='bookmarks') { $gn='Bookmarks_URL'; $gl='x_Bookmarks_URL'; }
        else if ($fn=='analyses') { $gn='Analysis_URL'; $gl='x_Analysis_URL'; }
	else return;
	$fv = $_SERVER['DOCUMENT_ROOT']."/".$aj2."/genomes/.".strtolower($fn);
	read_x_info ($fv, $gn, $gl, 0);
}

//read_x_info
function read_x_info ($fv, $gn, $gl, $idx, $fv2=null)
{
	global $aj2;
	if (!isset($aj2)) $aj2 == $_SESSION('aj_root');
	
	if (file_exists($fv))
	{
		if (is_dir($fv)) $ga = scandir($fv);
		else $ga = explode (PHP_EOL, trim(file_get_contents($fv)));
		if (isset($fv2) && file_exists($fv2))
		{
			$hr = array ("------------");
			if (is_dir($fv2)) $ga = array_merge($ga, $hr, scandir($fv2));
			else $ga = array_merge($ga, $hr, explode (PHP_EOL, trim(file_get_contents($fv2))));
		}
		if (count($ga)>0)
		{
			if ($idx==2)
			{
				$fz = "var ".$gl."A = new Array(); ".$gl."A[0]=''; ";
				$k = 1; $exst = array();
				foreach ($ga as $gv)
				{
					if ($gv[0]=='#' || $gv[0]=='.') continue;
					$gz = explode("\t", $gv);
					if (!isset($gz[1])) $gz[1]=$gz[0];
					if (in_array($gz[1], $exst)) continue;
					if (!isset($gz[2])) $gz[2]=$gz[0];
					$fz .= $gl."A[".$k."]='".$gz[2]."'; ";
					$exst[] = $gz[1];
					$k++;
				}	
			}
			if ($idx==0)
				echo '<select class="CEL" name="'.$gn.'_Options" style="width:100px;" onChange="document.getElementById(\''.$gl.'\').value=this.value; ">'.PHP_EOL;
			if ($idx==10)
				echo '<select class="CEL" name="'.$gn.'_Options" style="width:100px;" onChange="if (document.getElementById(\''.$gl.'\').value!=\'\') document.getElementById(\''.$gl.'\').value+=\',\'; document.getElementById(\''.$gl.'\').value+=this.value; ">'.PHP_EOL;
			else if ($idx==1)
				echo '<select class="CEL" name="'.$gn.'_Options" style="width:100px;" onChange="document.getElementById(\''.$gl.'\').value=this.value; document.getElementById(\''.$gl.'Text\').value=this.options[this.selectedIndex].text;">'.PHP_EOL;
			else if ($idx==2)
				echo '<select class="CEL" name="'.$gn.'_Options" style="width:100px;" onChange="javascript: '.$fz.' document.getElementById(\''.$gl.'\').value=this.value; document.getElementById(\''.$gl.'Text\').value=this.options[this.selectedIndex].text; document.getElementById(\''.$gl.'Alias\').value='.$gl.'A[this.selectedIndex];">'.PHP_EOL;
			if (is_dir($fv)) echo '<option value="">Select new from</option>'.PHP_EOL;
			else echo '<option value="">Select from</option>'.PHP_EOL;
			$exst2 = array();
			foreach ($ga as $gv)
			{
				if ($gv[0]=='#' || $gv[0]=='.') continue;
				if (trim($gv)=='') continue;
				$gz = explode("\t", $gv);
				if (!isset($gz[1])) $gz[1]=$gz[0];
				if (in_array($gz[1], $exst2)) continue;
				if (!isset($gz[2])) $gz[2]=$gz[0];
				$exst2[] = $gz[1];
				if ($gz[1]=="------------")
					echo '<option value=\"\" disabled>----------------</option>'.PHP_EOL;
				else if ($gn=="Genome")
				{
					$gx = trim($gz[1]);
					if ($gx[0]=='/' || $gx[0]=='.')
						echo '<option value="'.trim($gz[1]).'">'.trim($gz[0]).'</option>'.PHP_EOL;
					else echo '<option value="'.$aj2.'/genomes/'.trim($gz[1]).'">'.trim($gz[0]).'</option>'.PHP_EOL;
				}
				else if ($gn=="Model") 
				{
					$gx = trim($gz[1]);
					if ($gx[0]=='/' || $gx[0]=='.')
						echo '<option value="'.trim($gz[1]).'">'.trim($gz[0]).'</option>'.PHP_EOL;
					else echo '<option value="'.$aj2.'/models/'.trim($gz[1]).'">'.trim($gz[0]).'</option>'.PHP_EOL;
				}
				else
				{
					echo '<option value="'.trim($gz[1]).'">'.trim($gz[0]).'</option>'.PHP_EOL;
				}
			}
			echo '</select>'.PHP_EOL;
		}
	}
}

//Save, Retrieve or Delete suffix infos 2013-06-06
function retrieve_suffix($fn, $df)
{
	global $aj2, $aj_user;
	//if ($df==true) { unset($_REQUEST); $_REQUEST = array(); }
	if (!isset($aj2)) $aj2 = $_SESSION['aj_root'];
	if (!isset($aj_user)) $aj_user = $_SESSION['aj_user'];
	$fv = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.suffix_'.$fn.'.info';
	if (!file_exists($fv)) return false;
	if (($inf = fopen($fv, "r"))==NULL) return false;
	while ($sn=fgets($inf))
	{
		$rq = explode(' =:= ', $sn);
		if (!$df && isset($_REQUEST[trim($rq[0])])) continue;
		if (trim($rq[0])!='Submit') $_REQUEST[trim($rq[0])] = trim($rq[1]);
	}
	fclose($inf);
	return true;
}

//save_suffix onto suffix folder
function save_suffix($fn, $df)
{
	global $aj2, $aj_user;
	if (!isset($aj2)) $aj2 = $_SESSION['aj_root'];
	if (!isset($aj_user)) $aj_user = $_SESSION['aj_user'];
	$dn = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user;
	if (!file_exists($dn)) mkdir ($dn, 0755, true);
	$fv = $dn.'/.suffix_'.$fn.'.info';
	if (($outf = fopen($fv, "w"))==NULL) return false;
	foreach ($_REQUEST as $key => $value)
	{
		if ($key=='Submit' || $key=='Default') continue;
		fprintf($outf, "%s =:= %s", $key, addslashes($value).PHP_EOL);
	}
	fclose($outf);
	if ($df) return save_suffix('default', false);
	return true;
}

//delete_suffix and default if
function delete_suffix($fn)
{
	global $aj2, $aj_user;
	$df=0;
	if (!isset($aj2)) $aj2 = $_SESSION['aj_root'];
	if (!isset($aj_user)) $aj_user = $_SESSION['aj_user'];
	$fv = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.suffix_'.$fn.'.info';
	$fd = $_SERVER['DOCUMENT_ROOT'].$aj2.'/suffix/'.$aj_user.'/.suffix_default.info';
	if (!file_exists($fv)) return false;
	if (($inf = fopen($fd, "r"))!=NULL)
	{
		while ($sn=fgets($inf))
		{
			$rq = explode(' =:= ', $sn);
			if (trim($rq[0])=='Suffix_File')
			{
				if (trim($fn)==trim($rq[1]))
				{
					$df=1; break;
				}
			}
		}
		fclose($inf);
	}
	unlink($fv);
	if($df) unlink($fd);
	return true;
}

//remove url's . or .. and its parent directory
function clean_url_parent ($url)
{
	$uc = explode ('/', $url);
	$nc = count($uc);
	for ($j=1;$j<$nc;$j++)
	{
		if ($uc[$j]=='.') unset($uc[$j]);
		else if ($uc[$j]=='..')
		{
			unset($uc[$j]);
			$z=$j;
			while (!isset($uc[$z]) && $z>=0) $z--;
			if ($z>=0 && isset($uc[$z])) unset($uc[$z]);
		}
	}
	return implode('/', $uc);
}

//the track's keys definition
function get_track_keys ()
{
	return "Keys =:= Track_Name =,= Track_Type =,= Track_Path =,= Track_URL =,= Icon =,= Height =,= Scale =,= boxHeight =,= boxWidthMin =,= Single =,= Control =,= alignControl =,= NoArrows =,= NoLabels =,= showWalks =,= active =,= group =,= Server =,= Table_List =,= Title_List =,= Info_List =,= Options_List =,= Include =,= Append_Assembly =,= iBase =,= Reads =,= Policy =,= Class =,= Color =,= Data_Type"; 
}

function get_track_new_array ()
{
	$track = array();
	$kstr = explode(" =:= ", get_track_keys());
	$keys = explode(" =,= ", $kstr[1]);
	foreach($keys as $key)
	{
		$track[$key] = '';
	}
	return $track;
}

function get_track_db_str ($ht, $g)
{
	return 'Track =:= '.$ht['Track_Name'].' =,= '.$ht['Track_Type'].' =,= '.$ht['Track_Path'].' =,= '.$ht['Track_URL'].' =,= '.$ht['Icon'].' =,= '.$ht['Height'].' =,= '.$ht['Scale'].' =,= '.$ht['boxHeight'].' =,= '.$ht['boxWidthMin'].' =,= '.$ht['Single'].' =,= '.$ht['Control'].' =,= '.$ht['alignControl'].' =,= '.$ht['NoArrows'].' =,= '.$ht['NoLabels'].' =,= '.$ht['showWalks'].' =,= '.$ht['active'].' =,= '.$g.' =,= '.$ht['Server'].' =,= '.$ht['Table_List'].' =,= '.$ht['Title_List'].' =,= '.$ht['Info_List'].' =,= '.$ht['Options_List'].' =,= '.$ht['Include'].' =,= '.$ht['Append_Assembly'].' =,= '.$ht['iBase'].' =,= '.$ht['Reads'].' =,= '.$ht['Policy'].' =,= '.$ht['Class'].' =,= '.$ht['Color'].' =,= '; 
}

function saveConfigDB($HT, $hx, $cors='')
{
	global $aj2, $aj_user;
	$ownPage = '';
	if (isset($HT[0]['Node_Path']))
		$ownPage = substr($HT[0]['Node_Path'], 0, strpos($HT[0]['Node_Path'],'/'));
	if (trim($ownPage)=='') $ownPage = 'NewPage';
	$ownPath = $aj2."/tracks/".$aj_user."/";
	if ((!isset($hx['Provider_Name'])||$hx['Provider_Name']=='')&&(isset($hx['Author']) && $hx['Author']!='')) $hx['Provider_Name']=$hx['Author'];
	if ((!isset($hx['Author'])||$hx['Author']=='')&&(isset($hx['Provider_Name']) && $hx['Provider_Name']!='')) $hx['Author']=$hx['Provider_Name'];
	if ((!isset($hx['Provider_Mail'])||$hx['Provider_Mail']=='')&&(isset($hx['Email']) && $hx['Email']!='')) $hx['Provider_Mail']=$hx['Email'];
	if ((!isset($hx['Email'])||$hx['Email']=='')&&(isset($hx['Provider_Mail']) && $hx['Provider_Mail']!='')) $hx['Email']=$hx['Provider_Mail'];
	if (!isset($hx['Bookmarks_URL'])||$hx['Bookmarks_URL']=='') $hx['Bookmarks_URL']=$aj2.'/includes/common_bookmarks.php';
	
	if ($cors=='CORS') $fg = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/CORS/.'.$ownPage.'.db'; 
	else if ($cors=='') $fg = $_SERVER['DOCUMENT_ROOT'].$aj2.'/pages/'.$aj_user.'/.'.$ownPage.'.db';
	else $fg = $cors;
	for ($i=3;$i>=0;$i--)
	{
		if (file_exists($fg.'bak'.$i)) copy($fg.'bak'.$i, $fg.'bak'.($i+1));
	}
	if (file_exists($fg)) copy($fg, $fg.'bak0');
	else
	{
		$dg = substr($fg, 0, strrpos($fg, '/'));
		if (!file_exists($dg)) mkdir($dg, 0755, true);
	}

	$outf = fopen($fg, "w");
	fprintf($outf, "%s", get_track_keys().PHP_EOL);
		$no = count($HT); $g=1;
	for ($i=0; $i<$no; $i++)
	{
		if ($i>0 && $HT[$i]['Track_Path']!=$HT[$i-1]['Track_Path']) $g++;
		if (!isset($HT[$i]['Table_List']) || $HT[$i]['Table_List']=='')
		{
			$fn = $_SERVER['DOCUMENT_ROOT'].$HT[$i]['Track_URL'];
			if (file_exists($fn))
			{
				$ft = getTableInfo($fn);
				$HT[$i]['Table_List'] = $ft['Table'];
				if (isset($ft['Server'])) $HT[$i]['Server']=$ft['Server'];
				if (isset($ft['Title'])) $HT[$i]['Title_List']=$ft['Title'];
				if (isset($ft['Info'])) $HT[$i]['Info_List']=$ft['Info'];
				if (isset($ft['Options'])) $HT[$i]['Options_List']=$ft['Options'];
				if (isset($ft['Append'])) $HT[$i]['Append_Assembly']=$ft['Append'];
				if (isset($ft['iBase'])) $HT[$i]['iBase']=$ft['iBase'];
				if (isset($ft['Include'])) $HT[$i]['Include']=$ft['Include'];
			}
		}
		else if ($HT[$i]['Data_Type']=='SimpleData' || $HT[$i]['Data_Type']=='SimpleTrack' || $HT[$i]['Data_Type']=='MySQLData' || $HT[$i]['Data_Type']=='MySQLTrack')
{
			$fn = $_SERVER['DOCUMENT_ROOT'].$HT[$i]['Track_URL'];
			if (strpos($HT[$i]['Track_URL'], $ownPath)!=-1  && (!file_exists($fn) || (isset($HT[$i]['Force']) && $HT[$i]['Force']!=false)))
			{
				saveTrackPage($HT[$i], $hx);
			}
		}
		if (!isset($HT[$i]['Options_List'])) $HT[$i]['Options_List'] = '';
		if (!isset($HT[$i]['iBase'])) $HT[$i]['iBase'] = '';
		if (!isset($HT[$i]['NoArrows'])) $HT[$i]['NoArrows'] = '';
		if (!isset($HT[$i]['NoLabels'])) $HT[$i]['NoLabels'] = '';
		if (!isset($HT[$i]['Reads'])) $HT[$i]['Reads'] = '';
		if (!isset($HT[$i]['Server'])) $HT[$i]['Server'] = '';
		if (!isset($HT[$i]['Append_Assembly'])) $HT[$i]['Append_Assembly'] = '';
		if (!isset($HT[$i]['active'])) $HT[$i]['active'] = 0;
		if (!isset($HT[$i]['Data_Type'])) $HT[$i]['Data_Type']="PageTrack";
		if (!isset($HT[$i]['Icon'])) $HT[$i]['Icon']='silk_bricks';

		if ($HT[$i]['Reads'] == '' && file_exists($HT[$i]['Table_List'].'/.readno'))
		{
			$HT[$i]['Reads'] = trim(file_get_contents($HT[$i]['Table_List'].'/.readno'));
		}
		$tk = get_track_db_str($HT[$i], $g);
		fprintf($outf,"%s", $tk.PHP_EOL);
	}
	foreach ($hx as $ky => $vu)
	{
		if ($ky != 'Data_Type' && strstr($ky, "Node_")==NULL)
			fprintf($outf, "%s =:= %s", $ky, addslashes($vu).PHP_EOL);
	}
	if (!isset($hx['jBuilder']))
		fprintf ($outf, "jBuilder =:= %s%s", trim(file_get_contents(".version")), PHP_EOL);

	fclose($outf);

	return array ($ownPage, $HT, $hx);
}
 
function saveTrackPage ($ht, $hx)
{
        global $aj2, $aj_user;
        $head = '<?PHP';

        if (isset($hx['AllowCORS']) && $hx['AllowCORS'] == true)
                $head .= PHP_EOL.http_cors();
        if (isset($hx['Provider_Name']) && $hx['Provider_Name']!='')
                $head .= PHP_EOL.'$provider_name = \''.addslashes($hx['Provider_Name']).'\';';
        if (isset($hx['Provider_Mail']) && $hx['Provider_Mail']!='')
                $head .= PHP_EOL.'$provider_email = \''.addslashes($hx['Provider_Mail']).'\';';
        if (isset($hx['Institution']) && $hx['Institution']!='')
                $head .= PHP_EOL.'$institution_name = \''.addslashes($hx['Institution']).'\';';
        if (isset($hx['Institution_Link']) && $hx['Institution_Link']!='')
                $head .= PHP_EOL.'$institution_url = \''.addslashes($hx['Institution_Link']).'\';';
        if (isset($hx['Institution_Logo']) && $hx['Institution_Logo']!='')
                $head .= PHP_EOL.'$institution_logo = \''.addslashes($hx['Institution_Logo']).'\';';
        if ($ht['Data_Type']=='MySQLData' || $ht['Data_Type']=='MySQLTrack')
        {
                if (isset($ht['Append_Assembly']) && $ht['Append_Assembly']!='')
                        $head .= PHP_EOL.'$append_assembly = true;';
                $head .= PHP_EOL.'$server = \''.$ht['Server'].'\';';
        }
        $head .= PHP_EOL.'$table = \''.addslashes($ht['Table_List']).'\';';
        $head .= PHP_EOL.'$title = \''.addslashes($ht['Title_List']).'\';';
        $head .= PHP_EOL.'$info = \''.addslashes($ht['Info_List']).'\';';
        $head .= PHP_EOL.'$species = \''.addslashes($hx['Organism']).'\';';
        $head .= PHP_EOL.'$aj2 = \''.$aj2.'\';';

        if ($ht['Data_Type']=='MySQLData' || $ht['Data_Type']=='MySQLTrack')
        {
                if (isset($ht['Options_List']) && $ht['Options_List']!='') $head .= PHP_EOL.'$options = \''.$ht['Options_List'].'\';';

                if (isset($ht['iBase']) && $ht['iBase']!='') $head .= PHP_EOL.'$ibase = 1;';
        }
        $head .= PHP_EOL.'require_once ($_SERVER[\'DOCUMENT_ROOT\'].$aj2.\'/includes/'.$ht['Include'].'\');'.PHP_EOL;
$head .= '?>'.PHP_EOL;

        $fn = $_SERVER['DOCUMENT_ROOT'].$ht['Track_URL'];
        $dn = substr($fn, 0, strrpos($fn, '/'));
        if (!is_dir($dn)) mkdir ($dn, 0700, true);
        $outf = fopen($fn, "w");
        fprintf($outf, "%s", $head);
        fclose ($outf);
}
 
//find js path in a page
function pg_2_js ($str, $ua)
{
	$sn = $str; $url=''; $dn='';
	while ($sn=strstr($sn, "<script"))
	{
		$n = strpos($sn, "</script>");
		$un = substr($sn, 0, $n);
		$url = trim(substr(strstr($un, "src="), 5),">'\"");
		if (strstr($url, "/ext-")) ;
		else if (strstr($url, "/aj-min")) ;
		else if (strstr($url, "/excanvas")) ;
		else if (strstr($url, "google")) ;
		else
		{
			if (strncmp($url, "http://", 7)==0) ;
			else if (strncmp($url, "https://", 8)==0) ;
			else if ($url!='' && $url[0]=='/')
			{
				if (!isset($ua['scheme']))
					$url = $_SERVER['DOCUMENT_ROOT'].$url; 
				else $url = $ua['scheme'].'://'.$ua['host'].$url;
			}
			else
			{
				if (strstr($ua['path'], "/")!=null) $ph = substr($ua['path'], 0, strrpos($ua['path'], "/")); 
				if (!isset($ua['scheme'])) $url = clean_url_parent ($ph.'/'.$url);
				else $url = clean_url_parent ($ua['scheme'].'://'.$ua['host'].$ph.'/'.$url);
			}
			$se = efile_get_contents($url);
			if (isset($se['str'])) $dn .= $se['str'];
		}
		$sn = substr($sn, $n);
	}
	return $dn;
}

//convert an Anno-J page which contains AnnoJ.config to a json string
function js_2_json ($sn)
{
	$str = '';
	if (strstr($sn, "AnnoJ.config"))
	{ 
		$str = strstr($sn, "AnnoJ.config");
		$str = substr($str, strpos($str, "{"));
		$str = substr_in_brackets ($str);
		$str = substr_in_brackets ($str);
		$str = str_replace('\'', '"', $str);
		$str = preg_replace('/(\w+)\s{0,1}:/', '"\1":', str_replace(array("\r\n", "\r", "\n", "\t"), "", $str));
		$str = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $str);
		$str = str_replace(",}", "}", $str);
		$str = str_replace(",]", "]", $str);
		$str = str_replace(",)", ")", $str);
		$str = str_replace('\"', '\'', $str);
		$str = str_replace('"http"://', 'http://', $str);
		$str = str_replace('"https"://', 'https://', $str);
		$str = str_replace('"ftp"://', 'ftp://', $str);
	}
	return $str;
}

//sub function of js_2_json
function substr_in_brackets ($sn)
{
	$k=0; $l=0; $r=0; $n=strlen($sn); $str='';
	$sn = str_replace("\t", " ", $sn);	
	for ($i=0;$i<$n;$i++)
	{
		if ($sn[$i]=='/' && $sn[$i+1]=='/') 
		{
			if ($i-1>=0 && $sn[$i-1]!=':')
			{
				$p = strpos(substr($sn, $i), "\n");
				$i += $p;   
			}	    
		}
		while ($sn[$i]==' ' && ($sn[$i+1]==' '|| $sn[$i+1]==':' || $sn[$i+1]==')'|| $sn[$i+1]==']'|| $sn[$i+1]=='}')) $i++;
		$str .= $sn[$i];
		if ($sn[$i]=='{') { $l++; $k++; };
		if ($sn[$i]=='}') { $r++; $k--; if ($k==0) break; }
	}
	return $str.'';
}

function js_2_db_tracks ($ja)
{
	$HT = array();
	foreach ($ja['tracks'] as $track)
	{
		$ht = get_track_new_array ();
		if (isset($track['name'])) $ht['Track_Name']=$track['name'];
		if (isset($track['type'])) $ht['Track_Type']=$track['type'];
		if (isset($track['path'])) $ht['Track_Path']=$track['path'];
		if (isset($track['data'])) $ht['Track_URL']=$track['data'];
		if (isset($track['iconCls'])) $ht['Icon']=$track['iconCls'];
		else $ht['Icon']='silk_bricks';
		if (isset($track['height'])) $ht['Height']=$track['height'];
		if (isset($track['boxHeight'])) $ht['boxHeight']=$track['boxHeight'];
		if (isset($track['boxWidthMin'])) $ht['boxWidthMin']=$track['boxWidthMin'];
		if (isset($track['scale'])) $ht['Scale']=$track['scale'];
		if (isset($track['single'])) $ht['Single']=$track['single'];
		if (isset($track['showControls'])) $ht['Control']=$track['showControls'];
		if (isset($track['showWalks'])) $ht['showWalks']=$track['showWalks'];
		if (isset($track['alignControl'])) $ht['alignControl']=$track['alignControl'];
		if (isset($track['showArrows'])) $ht['NoArrows']=!$track['showArrows'];
		if (isset($track['showLabels'])) $ht['NoLabels']=$track['showLabels'];
		if (isset($track['class'])) $ht['Class']=$track['class'];
		if (isset($track['policy'])) $ht['Policy']=$track['policy'];
		if (isset($track['color'])) $ht['Color']=$track['color'];
		if (false !== ($i = array_search($track['id'], $ja['active'])))
			$ht['active']=$i+1;
		$HT[] = $ht;		
	}
	return $HT;
}

function js_2_db_suffix ($jx)
{
        $HX = array();
        foreach ($jx as $key => $val)
        {
		if ($key == 'tracks' || $key == 'active') continue;
		if ($key=='title') $HX['Page_Title']=$val;
		if ($key=='Title') $HX['Page_Title']=$val;
		if ($key=='genome') $HX['Genome_URL']=$val;
		if ($key=='bookmarks') $HX['Bookmarks_URL']=$val;
		if ($key=='location')
		{
			foreach ($val as $ik => $iv)
			{
				if ($ik=='assembly') $HX['Assembly']=$iv;
				if ($ik=='position') $HX['Position']=$iv;
				if ($ik=='bases') $HX['Bases']=$iv;
				if ($ik=='pixels') $HX['Pixels']=$iv;
			}
		}

		if ($key=='admin')
		{
			foreach ($val as $ik => $iv)
			{
				if ($ik=='name') $HX['Author']=$iv;
				if ($ik=='email') $HX['Email']=$iv;
				if ($ik=='notes') $HX['Notes']=$iv;
			}
		}

		if ($key=='settings')
		{
			foreach ($val as $ik => $iv) 
			{
				if ($ik=='baseline') $HX['Baseline']=$iv;
				if ($ik=='display') $HX['Display']=$iv;
				if ($ik=='activate') $HX['Activate']=$iv;
				if ($ik=='scale') $HX['GlobalScale']=$iv;
				if ($ik=='multi') $HX['Multi']=$iv;
				if ($ik=='yaxis') $HX['Yaxis']=$iv;
				if ($ik=='hic_d') $HX['HiC_d']=$iv;
			}
		}
		if ($key=='citation') $HX['Citation']=$val;

	}
	return $HX;
}

function efile_get_contents ($fn)
{
	$se = array();
	set_error_handler(create_function('$severity, $message, $file, $line',
		'throw new ErrorException($message, $severity, $severity, $file, $line);'));
	try { $se['str'] = file_get_contents($fn, true); }
	catch (Exception $e) { $se['err'] = $e->getMessage(); }
	restore_error_handler();
	return $se;
}
?>
