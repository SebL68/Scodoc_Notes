<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include $path.'/config/config.php';
	require $path.'//includes/'.Config::$auth_class;	// Class Auth

	Auth::logout();
?>
