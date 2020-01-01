<?PHP
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With');

$append_assembly = true;
$provider_name = 'Huaming';
$provider_email = 'h@salk.edu';
$species = 'Arabidopsis thaliana';
$table = '/cndd/ckeown/scratch_aj2/Araport';
$title = 'Arabidopsis test';
$info = 'test again';
$aj2 = '/aj2';
$ibase = 1;
require_once ($_SERVER['DOCUMENT_ROOT'].$aj2.'/includes/simple_models.php');
?>
