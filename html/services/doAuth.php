<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once $path.'/config/config.php';
	include_once $path.'/includes/'.Config::$auth_class;	// Class Auth

	Auth::doAuth();
?>
