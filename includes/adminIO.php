<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
/* Debug */
	/*error_reporting(E_ALL);
  ini_set('display_errors', '1');*/
  function listeVacataires($dep){
    global $path;

    $file = "$path\\LDAP\\vacataires.json";

    $json = json_decode(file_get_contents($file));
    return $json->$dep;
  }

?>