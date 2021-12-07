<?php
	function loadCsv($fn = 'miniatlas_tracks.csv') {
		// Load the miniatlas_tracks.csv
		print "<script type='text/javascript'>\n";
		print "var tracks = []\n";
		
		$row = 1;
		if (($handle = fopen($fn, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        $num = count($data);
		        if ($row==1) {
		        	$header = $data;
		        } else {
		            print "tracks.push({ \n";
			        for ($c=0; $c < $num; $c++) {
			            print "   '".$header[$c]."' : '".$data[$c]."',\n";
			        };
			        print "}); \n";
		        };
		        $row++;
		    }
		    fclose($handle);
		}
		print "</script>\n";
	}
?>
