<?PHP
require_once '../includes/common.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With');

if ($action=='syndicate')
{
	$genome = array(
		'institution' => array(
			'name' => 'salk',
			'url' => '',
			'logo' => ''
		),
		'engineer' => array(
			'name' => 'chrl test',
			'email' => 'christopher.keown@gmail.com',
		),
		'service' => array(
			'title' => 'chrl test',
			'version' => '1',
			'description' => 'blah',
		),
		'genome' => array(
			'assemblies' => array(
				array( 'id' => 'L', 'size' => 48503 ),
			)
		)
	);
	respond($genome);
}

$genome_dir = '/cndd/ckeown/scratch_aj2/chr/chr';
$ibase = 1;

include_once '../includes/common_genome.php';

error('Invalid action requested: '.$action);
?>
