<?PHP
if ($action == 'walk')
{
	$id = isset($_GET['id']) ? clean_string($_GET['id']) : '';
	$assembly = isset($_GET['assembly']) ? clean_string($_GET['assembly']) : false;
	$position = isset($_GET['position']) ? clean_string($_GET['position']) : false;
	if ($id == '' && ($assembly === false || $position === false))
		error('Illegal id and location provided in walk request');
	
	$dir = isset($_GET['dir']) ? clean_string($_GET['dir']) : false;
	if ($dir === false || ($dir != 'F' && $dir != 'R')) error('Illegal walk direction in the request');

	$position = intval($position);
	$pr = null; $nr = null; $r = '';

	$fn = $table.'/walks.txt';
	if (!file_exists($fn) && file_exists($table.'/models.txt')) $fn = $table.'/models.txt';

	if (file_exists($fn))
	{
		if ($inf=fopen($fn, "r"))
		{
			if ($id == '')
			{
				while ($sn=fgets($inf))
				{
					$sa = explode("\t", trim($sn));
					if (count($sa)<4) continue;
					if ($sa[1] == $assembly && $position < $sa[2])
					{
						$nr = $sn;
						break;
					}
					$pr = $sn;
				}
				if ($nr == null) $nr = $pr;
				if ($pr == null) $pr = $sn;
			}
			else
			{  
				while ($sn=fgets($inf))
				{  
					$sa = explode("\t", trim($sn));
					if (count($sa)<4) continue;
					if ($sa[0] == $id)
					{  
						if ($nr = fgets($inf))
						{
							$na = explode("\t", trim($nr));
							if (count($na)<4) continue;
							while ($na[1]===$sa[1] && $na[2] < $sa[3])
							{
								if (!($nr = fgets($inf))) break;
								$nz = explode("\t", trim($nr));
								if (count($na)<4) continue;
								$na = $nz;
							}
						}
						if ($pr==null) $pr = $sn;
						if ($nr==null) $nr = $sn;
						break;
					}  
					$pr = $sn;
				}
			}  
		}
		fclose ($inf);
		if ($dir == 'F' && $nr != '')
		{  
			$sa = explode("\t", trim($nr));
			$r = array ('id' => $sa[0], 'assembly' => $sa[1], 'start' => $sa[2], 'end' => $sa[3]);
		}  
		if ($dir == 'R' && $pr != '')
		{  
			$sa = explode("\t", trim($pr));
			$r = array ('id' => $sa[0], 'assembly' => $sa[1], 'start' => $sa[2], 'end' => $sa[3]);
		}
	}  
	respond($r);
}
?>
