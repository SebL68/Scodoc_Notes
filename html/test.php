<?php
/* Debug */
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	require_once "$path/includes/default_config.php";

	require_once "$path/includes/annuaire.class.php";

	echo Annuaire::statut('sebastien.lehmann@uha.fr');