<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once $path.'/includes/default_config.php';
	include_once $path.'/includes//'.$Config->auth_class;	// Class Auth

	Auth::doAuth();
?>
