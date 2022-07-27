<?php
  // Script de mise à jour du format du fichier utilisateurs.json
  // Le fichier à convertir doit être compatible avec la version 5:0:0 du site

  /* Debug */
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  $path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
  $file = "$path/data/annuaires/utilisateurs.json";
  $backup = "$path/data/annuaires/utilisateurs_bak.json";
  if(!copy($file, $backup))
    die("La sauvegarde du fichier <b>utilisateurs.json</b> a echouée.");

  $json = json_decode(file_get_contents($file));

  foreach($json as $dep => $d) {
    $json_new[$dep] = [
      "mail" => $d->{"mail"},
      "administrateurs" => [],
      "vacataires" => []
    ];

    // Traitement des Administrateurs
    foreach($d -> {"administrateurs"} as $id)
    {
      $tab = explode("@", $id);
      if (sizeof($tab) != 2)
        die("Format du fichier <b>utilisateurs.json</b> incorrect.");

      $tab2 = explode('.', $tab[0]);
      $name = ucwords($tab2[1].' '.$tab2[0], " -");

      $json_new[$dep]["administrateurs"][]=array("id"=>$id, "name"=>$name);
    }

    // Traitement des Vacataires
    foreach($d -> {"vacataires"} as $id)
    {
      $tab = explode("@", $id);
      $tab2 = explode('.', $tab[0]);
      $name = ucwords($tab2[1].' '.$tab2[0], " -");

      $json_new[$dep]["vacataires"][]=array("id"=>$id, "name"=>$name);
    }

    // Enregistrement du fichier au nouveau format
    file_put_contents(
      $file,
      json_encode($json_new, JSON_PRETTY_PRINT)
    );
  }

  echo "Mise à jour terminée.";
?>