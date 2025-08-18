<?php
  /* Debug */
  // error_reporting(E_ALL);
  // ini_set('display_errors', '1');

  // Nom du fichier contenant la liste des utilisateurs
  if(!$Config->multi_scodoc) {
  	Admin::$file = "$path/data/annuaires/utilisateurs.json";
  } else {
  	Admin::$file = "$path/data/annuaires/".$_COOKIE['composante']."_utilisateurs.json";
  }

  class Admin {
    static $file;

  /************************************/
  /* listeUtilisateurs
      Créé le fichier "utilisateurs.json" s'il n'existe pas
      Ajoute la liste d'un département si elle n'existe pas
      Récupère la liste des utilisateurs d'un département

      Entrée :
          $dep (string) : Nom du département concerné
          $statut (string) : Statut ('administrateurs' ou 'vacataires') des utilisateurs à retourner

      Retour :
          [array] liste des utilisateurs
  */
  /************************************/
  public static function listeUtilisateurs($dep, $statut) {
    if (!file_exists(self::$file)) {  // La liste des utilisateurs n'existe pas
      touch(self::$file);

      $json = [
        $dep => [
          $statut => []
        ]
      ];

      file_put_contents(
        self::$file,
        json_encode($json) //, JSON_PRETTY_PRINT)
      );
    }

    $json = json_decode(file_get_contents(self::$file));
    if (!isset($json -> $dep)) {	// Le département n'existe pas
      $json -> $dep = (object)[$statut => []];

      file_put_contents(
        self::$file,
        json_encode($json) //, JSON_PRETTY_PRINT)
      );
    }

    if (!isset($json -> $dep -> $statut)) {	// Aucun utilisateur n'existe avec le statut
      $json -> $dep -> $statut =[];

      file_put_contents(
        self::$file,
        json_encode($json) //, JSON_PRETTY_PRINT)
      );
    }

    return $json -> $dep -> $statut;
  }

  /************************************/
  /* modifUtilisateur
      Enregistre un nouvel utilisateur ou modifie un utilisatru existant dans un département
    
      Entrée :
          $dep (string) : Nom du département concerné
          $statut (string) : Statut ('administrateurs' ou 'vacataires') des utilisateurs à retourner
          $ancien (string) : vide pour ajouter un nouvel utilisateur ou ancien Id d'un utilisateur à modifier
          $nouveau (string) : Id du nouvel utilisateur ou nouveau Id en cas de modification
          $nom (string) : Name de l'utilisateur à modifier
  */
  /************************************/
  public static function modifUtilisateur($dep, $statut, $ancien, $nouveau, $nom) {
    $json = json_decode(file_get_contents(self::$file));
    $util = $json -> $dep -> $statut;

    if (!empty($ancien) && in_array($ancien, array_column($util, 'id')) === FALSE)
      return ['result' => "Erreur : Utilisateur $ancien inconnu dans la liste des $statut du département $dep"];

    if (($ancien != $nouveau) && (in_array($nouveau, array_column($util, 'id')) === TRUE))
      return ['result' => "Erreur : Utilisateur $nouveau déjà enregistré dans la liste des $statut du département $dep"];

    if (empty($ancien))					// Enregistrement d'un nouveau vacataire
      array_push($util, (object)['id' => $nouveau, 'name' => $nom]);
    else												// Modification d'un vacataire existant
      $util[array_search($ancien, array_column($util, 'id'))] = (object)['id' => $nouveau, 'name' => $nom];

    usort($util, function($a, $b) { return $a->name <=> $b->name;});

    $json -> $dep -> $statut = array_values($util);

    file_put_contents(
      self::$file,
      json_encode($json) //, JSON_PRETTY_PRINT)
    );

    return ['result' => "OK"];
  }

  /************************************/
  /* supUtilisateur
      Supprime un utilisateur dans un département
    
      Entrée :
          $dep (string) : Nom du département concerné
          $statut (string) : Statut ('administrateurs' ou 'vacataires') de l'utilisateur à supprimer
          $id (string) : ID de l'utilisateur à supprimer
  */
  /************************************/
  public static function supUtilisateur($dep, $statut, $id) {
    $json = json_decode(file_get_contents(self::$file));
    $util = $json -> $dep -> $statut;
    if(in_array($id, array_column($util, 'id')) === FALSE)
      return ['result' => "Erreur : Utilisateur $id inconnu dans la liste des $statut du département $dep"];

    unset($util[array_search($id, array_column($util, 'id'))]);
    $json -> $dep -> $statut = array_values($util);

    file_put_contents(
      self::$file,
      json_encode($json)//, JSON_PRETTY_PRINT)
    );

    return ['result' => "OK"];
  }

  /************************************/
  /* tri
      Compare la propriété Nom des objets pour les classer par ordre alphabétique
    
      Entrée :
          $a, $b (array) : les deux objets à comparer
      Sortie :
          1 : si $a doit être classé après $b
          -1 : si $b doit être classé après $a
  */
  /************************************/
  private static function tri($a, $b) {
    return $a->name > $b->name;
  }
}