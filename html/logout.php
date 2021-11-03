<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/config/authentification.class.php";

	Auth::logout();
?>
