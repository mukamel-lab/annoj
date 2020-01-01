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
			'name' => 'hg19',
			'email' => 'christopher.keown@gmail.com',
		),
		'service' => array(
			'title' => 'Homo sapiens',
			'version' => 'hg19',
			'description' => 'hg19',
		),
		'genome' => array(
			'assemblies' => array(
				array( 'id' => '1', 'size' => 249250621 ),
				array( 'id' => '10', 'size' => 135534747 ),
				array( 'id' => '11', 'size' => 135006516 ),
				array( 'id' => '12', 'size' => 133851895 ),
				array( 'id' => '13', 'size' => 115169878 ),
				array( 'id' => '14', 'size' => 107349540 ),
				array( 'id' => '15', 'size' => 102531392 ),
				array( 'id' => '16', 'size' => 90354753 ),
				array( 'id' => '17', 'size' => 81195210 ),
				array( 'id' => '18', 'size' => 78077248 ),
				array( 'id' => '19', 'size' => 59128983 ),
				array( 'id' => '2', 'size' => 243199373 ),
				array( 'id' => '20', 'size' => 63025520 ),
				array( 'id' => '21', 'size' => 48129895 ),
				array( 'id' => '22', 'size' => 51304566 ),
				array( 'id' => '3', 'size' => 198022430 ),
				array( 'id' => '4', 'size' => 191154276 ),
				array( 'id' => '5', 'size' => 180915260 ),
				array( 'id' => '6', 'size' => 171115067 ),
				array( 'id' => '7', 'size' => 159138663 ),
				array( 'id' => '8', 'size' => 146364022 ),
				array( 'id' => '9', 'size' => 141213431 ),
				array( 'id' => 'L', 'size' => 48502 ),
				array( 'id' => 'M', 'size' => 16571 ),
				array( 'id' => 'X', 'size' => 155270560 ),
				array( 'id' => 'Y', 'size' => 59373566 ),
			)
		)
	);
	respond($genome);
}

$genome_dir = '/cndd/projects/Public_Datasets/references/hg19/aj2/genome/chr';
$ibase = 1;

include_once '../includes/common_genome.php';

error('Invalid action requested: '.$action);
?>
