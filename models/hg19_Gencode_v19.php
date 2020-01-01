<?PHP
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With');

$provider_name = 'Junhao Li';
$provider_email = 'jul307@ucsd.edu';
$institution_name = 'UCSD';
$species = 'Homo sapiens';
$table = '/home/aj2_data/gene_models/hg19_Gencode_v19';
$title = 'hg19_Gencode_v19';
$info = 'hg19_Gencode_v19';
$aj2 = '/aj2';
require_once ($_SERVER['DOCUMENT_ROOT'].$aj2.'/includes/simple_models.php');
?>
