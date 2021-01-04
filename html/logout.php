<?php
	session_start();
	$_SESSION = array();

	require_once '../CAS/include/CAS.php';
	require_once '../CAS/config/cas_uha.php';

	phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
	phpCAS::logoutWithRedirectService('https://notes.iutmulhouse.uha.fr/');
?>
