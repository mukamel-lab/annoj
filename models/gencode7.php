<?PHP
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With');

$provider_name = 'Gencode 7';
$provider_email = 'christopher.keown@gmail.com';
$institution_name = 'UCSD';
$institution_url = 'http://brainome.ucsd.edu/';
$species = 'Mus musculus';
$table = '/cndd/projects/Public_Datasets/references/mm10/aj2/gencode';
$title = 'Gencode 7';
$info = 'Gencode 7';
$aj2 = '/aj2';
require_once ($_SERVER['DOCUMENT_ROOT'].$aj2.'/includes/simple_models.php');
?>
