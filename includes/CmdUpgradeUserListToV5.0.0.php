<?php
  // Script de conversion du fichier "utilisateurs.json" pour la version 5:0:0 du site.
  // Ce script est exécutable en CLI ou par le serveur web.
  // En cas de réussite, l'ancienne version du fichier est sauvegardée dans "utilisateurs_bak.json".
  // En cas d'échec, le fichier "utilisateurs.json" reste inchangé.

  global $argv;

  if(isset($argv)) {
      $path = dirname(dirname(realpath($argv[0])));           // Exécution par CLI
      define ("NL", "\n");
  }
  else {
      $path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');    // Exécution par serveur web
      define ("NL", "<br>");
  }

  echo 'Conversion du fichier "utilisateurs.json" vers le format compatible avec la version 5:0:0 du site...'.NL;
  
  /* Debug */
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  $file = "$path/data/annuaires/utilisateurs.json";

  // Décodage du fichier utilisateurs.json
  $json = json_decode(file_get_contents($file));
  if(json_last_error() !== JSON_ERROR_NONE)
    die('Echec de la conversion du fichier "utilisateurs.json".'.NL.'(=> Erreur JSON dans le fichier "utilisateurs.json".)');

  // Sauvegarde du fichier "utilisateurs.json" original
  $backup = "$path/data/annuaires/utilisateurs_bak.json";
  if(!copy($file, $backup))
    die('Echec de la conversion du fichier "utilisateurs.json".'.NL.'(=> Impossible d\'enregistrer le fichier "utilisateurs_bak.json".)');

  // Conversion de chaque département
  foreach($json as $dep => $d) {
    // Initialisation de la structure JSON pour le département $dep
    $json_new[$dep] = [
      "mail" => $d->{"mail"},
      "administrateurs" => [],
      "vacataires" => []
    ];

    // Traitement des Administrateurs et des Vacataires
    foreach(["administrateurs", "vacataires"] as $statut) {
      foreach($d -> {$statut} as $id)
      {
        if(is_string($id)) {           // L'ancien ID est une chaine de caractères
          $tab = explode("@", $id);
          if(sizeof($tab) == 2) {      // Ancien ID = adresse mail => On extrait le nom à partir de l'adresse
            $tab2 = explode('.', $tab[0]);
            if(sizeof($tab2) == 2)     // L'adresse mail contient le nom et le prénom
              $name = ucwords($tab2[1].' '.$tab2[0], " -");
            else                        // L'adresse mail ne contient qu'un nom
              $name = ucfirst($tab2[0]);
          }
          else                          // Ancien ID = chaine de caractère => On le recopie dans le nom
            $name = $id;
        }
        else {                          // Format du fichier "utilisateurs.json incorrect : On remet le fichier initial en place
          rename($backup, $file);
          die('Echec de la conversion du fichier "utilisateurs.json".'.NL.'(=> Format incorrect.)');
        }

        $json_new[$dep][$statut][]=array("id"=>$id, "name"=>$name);
      }
    }
  }

  // Enregistrement du fichier au nouveau format
  file_put_contents(
    $file,
    json_encode($json_new, JSON_PRETTY_PRINT)
  );

  echo 'Fin de la conversion du fichier "utilisateurs.json" : pas d\'erreur !'.NL.'(=> Sauvegarde du fichier original dans "utilisateurs_bak.json".)';