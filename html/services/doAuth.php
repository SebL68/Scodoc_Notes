<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	require Config::$auth_class;	// Class Auth

	Auth::doAuth();
?>