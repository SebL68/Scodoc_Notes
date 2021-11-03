<?php
	session_start();
	$_SESSION = array();

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

	require_once $path . '/lib/CAS/include/CAS.php';
	require_once $path . '/lib/CAS/config/cas_uha.php';

	phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
	phpCAS::logoutWithRedirectService('https://notes.iutmulhouse.uha.fr/');
?>
