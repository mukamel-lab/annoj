<?PHP

include_once 'global_settings.php';

if (!isset($dsn)) 
{
	$dsn = 'mysql:host='.$server;
	if (isset($database)) $dsn .= ';dbname='.$database;
	if (isset($port)) $dsn .= ';port='.$port;
}

try
{
	$pdo = new PDO ($dsn, $user, $pass);
} catch (PDOException $e)
{
	die ($e->getMessage());
}

?>
