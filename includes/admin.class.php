<?php
	// Nom du fichier contenant la liste des utilisateurs
	Admin::$file = "$path/data/annuaires/utilisateurs.json";

	class Admin {
		static $file;
	
	/* Debug */
	/*error_reporting(E_ALL);
	ini_set('display_errors', '1');*/

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
			//chmod(self::$file, 0664);
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
					$ancien (string) : vide pour ajouter un nouvel utilisateur ou ancien email d'un utilisateur à modifier
					$nouveau (string) : email du nouvel utilisateur ou nouveau email en cas de modification
	*/
	/************************************/
	public static function modifUtilisateur($dep, $statut, $ancien, $nouveau) {
		$json = json_decode(file_get_contents(self::$file));
		$util = $json -> $dep -> $statut;
		if (!empty($ancien) && in_array($ancien, $util) === FALSE)
			return ['result' => "Erreur : Utilisateur $ancien inconnu dans la liste des $statut du département $dep"];
		if (in_array($nouveau, $util) === TRUE)
			return ['result' => "Erreur : Utilisateur $nouveau déjà enregistré dans la liste des $statut du département $dep"];

		if (empty($ancien))					// Enregistrement d'un nouveau vacataire
			array_push($util, $nouveau);
		else												// Modification d'un vacataire existant
			$util[array_search($ancien, $util)] = $nouveau;
		usort($util, 'self::tri');
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
					$email (string) : email de l'utilisateur à supprimer
	*/
	/************************************/
	public static function supUtilisateur($dep, $statut, $email) {
		$json = json_decode(file_get_contents(self::$file));
		$util = $json -> $dep -> $statut;
		if (array_search($email, $util) === FALSE)
			return ['result' => "Erreur : Utilisateur $ancien inconnu dans la liste des $statut du département $dep"];

		unset($util[array_search($email, $util)]);
		$json -> $dep -> $statut = array_values($util);

		file_put_contents(
			self::$file,
			json_encode($json)//, JSON_PRETTY_PRINT)
		);

		return ['result' => "OK"];
	}

	/************************************/
	/* tri
			Compare les noms et prénoms de deux emails pour les classer par ordre alphabétique
		
			Entrée :
					$a, $b (string) : les deux emails à comparer
			Sortie :
					1 : si $a doit être classé après $b
					-1 : si $b doit être classé après $a
	*/
	/************************************/
	private static function tri($a, $b) {
		$taba = explode(".", $a);
		$tabb = explode(".", $b);
		if ($taba[1] > $tabb[1]) return 1;
		if ($taba[1] < $tabb[1]) return -1;
		if ($taba[0] > $tabb[0]) return 1;
		return -1;
	}
}