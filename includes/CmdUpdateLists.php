<?php
global $argv;

if (isset($argv)) {
    $path = dirname(dirname(realpath($argv[0])));           // Exécution par CLI
}
else {
    $path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');    // Exécution par serveur web
}

include_once "$path/includes/default_config.php";
include_once "$path/includes/".$Config->service_annuaire_class;	// Class Service_Annuaire

Service_Annuaire::updateLists();