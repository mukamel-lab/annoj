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
			'name' => 'mm10',
			'email' => 'emukamel@ucsd.edu',
		),
		'service' => array(
			'title' => 'Mus musculus',
			'version' => 'mm10',
			'description' => 'mm10 - mouse genome',
		),
		'genome' => array(
			'assemblies' => array(
				array( 'id' => '1', 'size' => 195471971 ),
				array( 'id' => '10', 'size' => 130694993 ),
				array( 'id' => '11', 'size' => 122082543 ),
				array( 'id' => '12', 'size' => 120129022 ),
				array( 'id' => '13', 'size' => 120421639 ),
				array( 'id' => '14', 'size' => 124902244 ),
				array( 'id' => '15', 'size' => 104043685 ),
				array( 'id' => '16', 'size' => 98207768 ),
				array( 'id' => '17', 'size' => 94987271 ),
				array( 'id' => '18', 'size' => 90702639 ),
				array( 'id' => '19', 'size' => 61431566 ),
				array( 'id' => '2', 'size' => 182113224 ),
				array( 'id' => '3', 'size' => 160039680 ),
				array( 'id' => '4', 'size' => 156508116 ),
				array( 'id' => '5', 'size' => 151834684 ),
				array( 'id' => '6', 'size' => 149736546 ),
				array( 'id' => '7', 'size' => 145441459 ),
				array( 'id' => '8', 'size' => 129401213 ),
				array( 'id' => '9', 'size' => 124595110 ),
				array( 'id' => 'L', 'size' => 48502 ),
				array( 'id' => 'X', 'size' => 171031299 ),
				array( 'id' => 'Y', 'size' => 91744698 ),
			)
		)
	);
	respond($genome);
}

// $genome_dir = '/cndd/projects/Public_Datasets/references/mm10/aj2/genome/chr';
$ibase = 1;

include_once '../includes/common_genome.php';

error('Invalid action requested: '.$action);
?>
