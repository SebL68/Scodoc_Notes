<?php
global $argv;

if (isset($argv)) {
    $path = dirname(dirname(realpath($argv[0])));           // Exécution par CLI
}
else {
    $path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');    // Exécution par serveur web
}

include_once "$path/config/config.php";
include_once "$path/includes/LDAPIO.php";

setCron();
?>