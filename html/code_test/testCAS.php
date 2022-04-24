Authentification en cours ...<br>
<style>
	body{
		background: #111;
		color: #FFF;
		font-family: arial;
	}
</style>

<?php
	/* Debug */
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	require_once $path.'/includes/default_config.php';
	
	/* CAS config */
	require_once $path . '/lib/CAS/CAS.php';
	require_once $path . '/config/cas_config.php';

	phpCAS::setLogger();
	phpCAS::setVerbose(true);
	
	phpCAS::proxy(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
	phpCAS::setNoCasServerValidation();

	/* Authentification */
	phpCAS::forceAuthentication();
	
	$attribs= phpCAS::getAttributes();

	echo 'Informations sur le CAS : <br>';
	var_dump($attribs);

	echo "<br><br>C'est bien authentifié, votre identifiant est :<br>";
	echo phpCAS::getUser();
?>