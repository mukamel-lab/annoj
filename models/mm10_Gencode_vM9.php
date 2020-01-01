<?PHP
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With');

$provider_name = 'Junhao Li';
$provider_email = 'jul307@ucsd.edu';
$institution_name = 'UCSD';
$species = 'Mus musculus';
$table = '/home/aj2_data/gene_models/mm10_Gencode_vM9';
$title = 'mm10_Gencode_vM9';
$info = 'mm10_Gencode_vM9';
$aj2 = '/aj2';
require_once ($_SERVER['DOCUMENT_ROOT'].$aj2.'/includes/simple_models.php');
?>
