<?PHP
if ($action == 'syndicate')
{
	$syndication = array
	(
		'institution' => array
		(
			'name' => (isset($institution_name))?$institution_name:'UCSD',
			'url'  => (isset($institution_url))?$institution_url:'http://brainome.ucsd.edu',
			'logo' => (isset($institution_logo))?$institution_logo:'/var/www/html/aj2/img/logo_ucsd.png'
		),
		'engineer' => array
		(
			'name'  => (isset($provider_name))?$provider_name:'Eran Mukamel',
			'email' => (isset($provider_email))?$provider_email:'emukamel@ucsd.edu'
		),
		'service' => array
		(
			'title'       => (isset($title))?$title:'No title provided',
			'species'     => (isset($species))?$species:'Mus musculus',
			'access'      => (isset($access))?$access:'public',
			'version'     => 'Unspecified',
			'format'      => 'Unspecified',
			'server'      => '',
			'description' => (isset($info))? $info:'No information available'
		)
	);
}
?>

