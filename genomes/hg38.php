<?PHP
require_once '../includes/common.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With');

if ($action=='syndicate')
{
	$genome = array(
		'institution' => array(
			'name' => 'UCSD',
			'url' => 'http://brainome.ucsd.edu/',
			'logo' => ''
		),
		'engineer' => array(
			'name' => 'hg38',
			'email' => 'christopher.keown@gmail.com',
		),
		'service' => array(
			'title' => 'Homo sapiens',
			'version' => 'hg38',
			'description' => 'hg38',
		),
		'genome' => array(
			'assemblies' => array(
				array( 'id' => '1', 'size' => 248956422 ),
				array( 'id' => '10', 'size' => 133797422 ),
				array( 'id' => '11', 'size' => 135086622 ),
				array( 'id' => '12', 'size' => 133275309 ),
				array( 'id' => '13', 'size' => 114364328 ),
				array( 'id' => '14', 'size' => 107043718 ),
				array( 'id' => '15', 'size' => 101991189 ),
				array( 'id' => '16', 'size' => 90338345 ),
				array( 'id' => '17', 'size' => 83257441 ),
				array( 'id' => '18', 'size' => 80373285 ),
				array( 'id' => '19', 'size' => 58617616 ),
				array( 'id' => '2', 'size' => 242193529 ),
				array( 'id' => '20', 'size' => 64444167 ),
				array( 'id' => '21', 'size' => 46709983 ),
				array( 'id' => '22', 'size' => 50818468 ),
				array( 'id' => '3', 'size' => 198295559 ),
				array( 'id' => '4', 'size' => 190214555 ),
				array( 'id' => '5', 'size' => 181538259 ),
				array( 'id' => '6', 'size' => 170805979 ),
				array( 'id' => '7', 'size' => 159345973 ),
				array( 'id' => '8', 'size' => 145138636 ),
				array( 'id' => '9', 'size' => 138394717 ),
				array( 'id' => 'L', 'size' => 48502 ),
				array( 'id' => 'M', 'size' => 16569 ),
				array( 'id' => 'X', 'size' => 156040895 ),
				array( 'id' => 'Y', 'size' => 57227415 ),
			)
		)
	);
	respond($genome);
}

$genome_dir = '/home/aj2_data/genomes/hg38/genome/chr';
$ibase = 1;

include_once '../includes/common_genome.php';

error('Invalid action requested: '.$action);
?>
