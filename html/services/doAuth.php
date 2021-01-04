<?php
	if(!isset($_SESSION)){ session_start(); }

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

	require_once $path . '/CAS/include/CAS.php';
	require_once $path . '/CAS/config/cas_uha.php';

	// Initialize phpCAS
	phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
		
	// force CAS authentication
	phpCAS::setNoCasServerValidation() ;
	//phpCAS::setCasServerCACert($cas_server_ca_cert_path);
	phpCAS::forceAuthentication(); 

	$_SESSION['id'] = phpCAS::getUser();

/**************************************/
/* Pour se faire passer pour un autre utilisateur // DEV ONLY */
/**************************************/
	/*if($_SESSION['id'] == "sebastien.lehmann@uha.fr"){
		$_SESSION['id'] = "alexandre.aab@uha.fr";
	}*/

	header('Location: '. $_GET['href']);
?>