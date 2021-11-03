<?php
	if(!isset($_SESSION)){ session_start(); }

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

	require_once $path . '/lib/CAS/include/CAS.php';
	require_once $path . '/lib/CAS/config/cas_uha.php';

	// Initialize phpCAS
	phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
		
	// force CAS authentication
	phpCAS::setNoCasServerValidation() ;
	//phpCAS::setCasServerCACert($cas_server_ca_cert_path);
	phpCAS::forceAuthentication(); 

	$_SESSION['id'] = phpCAS::getUser();

	header('Location: '. $_GET['href']);
?>