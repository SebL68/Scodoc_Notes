<?php
	///////////////////////////////////////
	// Basic Config of the phpCAS client //
	///////////////////////////////////////
	// Full Hostname of your CAS Server
	$cas_host = 'cas.uha.fr';
	// Context of the CAS Server
	$cas_context = '/cas/';
	// Port of your CAS server. Normally for a https server it's 443
	$cas_port = 443;
	// Path to the ca chain that issued the cas server certificate
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

	$cas_server_ca_cert_path = ''; 							// Si vous n'utilisez pas cas.pem
	//$cas_server_ca_cert_path = $path . '/config/cas.pem';	// Si vous utilisez cas.pem
?>