<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once $path.'/includes/default_config.class.php';
	include_once $path.'//includes/'.$Config->auth_class;	// Class Auth

	Auth::logout();
?>
