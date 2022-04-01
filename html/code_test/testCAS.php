Authentification en cours ...
<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	require_once $path.'/includes/default_config.php';
	
	require_once $path . '/lib/CAS/CAS.php';
	require_once $path . '/config/cas_config.php';
	
	phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context, 'https://notes.iutmulhouse.uha.fr/');

	phpCAS::forceAuthentication();

	echo "C'est bien authentifié";
?>