<?PHP

if ($action=='range')
{
        if (isset($genome_dir))
        {
		$fn = $genome_dir.$assembly;

                if (($inf=fopen($fn, 'r'))==NULL)
                        error('Can not open sequence file.');

                fseek($inf, $left, SEEK_SET);
                $ln = $right-$left+1;
                $seq = fread($inf, $ln);
                fclose($inf);

		if (!isset($ibase)) $ibase=1;
		$left += $ibase;
                
                $data=array('GENOME', $left, $seq);

                respond($data);
        }
        else if (isset($server) && isset($table))
        {
                include 'common_PDO.php';

                if (isset($append_assembly) && $append_assembly) $table .= $assembly;

                $query = 'select start, sequence from '.$table.' force index (start) where assembly = '.$assembly.' and start <= '.$right.' and end > '.$left.' order by start asc';
                $stmt = $pdo->prepare($query);
                $stmt->execute();

                $seq = ''; $st=-1;

                while ($r = $stmt->fetch(PDO::FETCH_NUM))
		{
			if ($st==-1) $st=$r[0];
			$seq .= trim($r[1]);
		}

                $data=array('GENOME', $st, $seq);
        
                respond($data);
        }
}
?>
