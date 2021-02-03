<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
/* Debug */
	/*error_reporting(E_ALL);
  ini_set('display_errors', '1');*/
  function listeVacataires($dep){
    global $path;

    $file = "$path\\LDAP\\vacataires.json";

    $json = json_decode(file_get_contents($file));
    return $json->$dep->vacataires;
  }

  function modifVacataire($dep, $ancien, $nouveau){
    global $path;

    $file = "$path\\LDAP\\vacataires.json";

    $json = json_decode(file_get_contents($file));
    $vac = $json->$dep->vacataires;
    if(array_search($ancien, $vac) === FALSE)
      return ['result' => "Erreur : Vacataire $ancien inconnu dans le département $dep"];
    
    $vac[array_search($ancien, $vac)] = $nouveau;
    $json->$dep->vacataires = $vac;

    file_put_contents(
      $file, 
      json_encode($json)
    );

    return ['result' => "OK"];
  }

  function supVacataire($dep, $email){
    global $path;

    $file = "$path\\LDAP\\vacataires.json";

    $json = json_decode(file_get_contents($file));
    $vac = $json->$dep->vacataires;
    if(array_search($email, $vac) === FALSE)
      return ['result' => "Erreur : Vacataire $ancien inconnu dans le département $dep"];

    unset($vac[array_search($email, $vac)]);
    $json->$dep->vacataires = array_values($vac);

    file_put_contents(
      $file, 
      json_encode($json)
    );

    return ['result' => "OK"];
  }

?>