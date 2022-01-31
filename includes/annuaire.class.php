<?php
/****************************/
/*  Class Annuaire
        Cette classe permet de rechercher les informations des utilisateurs issues des annuaires

        La méthode attendue est :
            - Annuaire::getStudentNumberFromMail()
            - Annuaire::getStudentMailFromNumber()
            - Annuaire::getAllStudents()
            - Annuaire::statut()

/****************************/

/*********************/
/*   Configurations  */
/*********************/
$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
include_once "$path/includes/default_config.php";

Annuaire::$STUDENTS_PATH = "$path/data/annuaires/liste_etu.txt";
Annuaire::$USERS_PATH = "$path/data/annuaires/utilisateurs.json";
Annuaire::$STAF_PATH = [
	$path.'/data/annuaires/liste_ens.txt',
	$path.'/data/annuaires/liste_biat.txt'
];
/* !!! Il faut certainement vérifier si les "pattern" dans les fonctions et la sélection dans getAllStudents() correspondent à vos fichiers d'annuaires !!! */

class Annuaire{
	static $STUDENTS_PATH;
	static $USERS_PATH;
	static $STAF_PATH;
	
	/****************************************************/
	/* getStudentNumberFromMail()
		Recherche du numéro d'étudiant en fonction de son mail dans l'annuaire

		Entrée :
			$mail: [string] - 'jean.dupont@uha.fr' // adresse mail de l'utilisateur 
		
		Sortie :
			[string] - '21912345' // numéro d'étudiant
	*/
	/****************************************************/

	public static function getStudentNumberFromMail($mail){
		// Recherche du numero d'étudiant en fonction de son mail dans une chaîne de caractère de type :
		// e1912345:jean.dupont@uha.fr
		global $Config;
		if($Config->CAS_return_type != 'nip'){
			self::checkFile(self::$STUDENTS_PATH);
			$handle = fopen(self::$STUDENTS_PATH, 'r');
			while(($line = fgets($handle, 1000)) !== FALSE){
				$data = explode(':', $line);
				if(strcasecmp(rtrim($data[1]), $mail) == 0)
					return Config::nipModifier($data[0]);
			}
		} else {
			return Config::nipModifier($mail);
		}

		exit(
			json_encode(
				array(
					'erreur' => "Votre compte n'est pas encore dans l'annuaire. Si le problème persiste, contactez votre responsable."
				)
			)
		);
	}

	/****************************************************/
	/* getStudentMailFromNumber()
		Recherche du mail de l'étudiant en fonction de son numéro dans l'annuaire

		Entrée :
			$num: [string] - '21912345' // numéro d'étudiant
		
		Sortie :
			[string] - 'jean.dupont@uha.fr' // adresse mail de l'utilisateur 
	*/
	/****************************************************/
	public static function getStudentMailFromNumber($num){
		// Recherche du mail en fonction du numéro dans une chaîne de caractère de type :
		// e1912345:jean.dupont@uha.fr
		// Attention, le listing LDAP fourni e1912345 alors que le vrai numéro est 21912345.

		self::checkFile(self::$STUDENTS_PATH);
		$handle = fopen(self::$STUDENTS_PATH, 'r');
		while(($line = fgets($handle, 1000)) !== FALSE){
			$data = explode(':', $line);
			if(substr($data[0], 1) == substr($num, 1))
				return rtrim($data[1]);
		}
	}

	/*******************************/
	/* getAllStudents()
		Liste de l'ensemble des étudiants de l'annuaire

		Entrée :
			/

		Sortie :
			["etudiant1@uha.fr", "etudiant2@uha.fr", etc.]
	*/
	/*******************************/
	public static function getAllStudents(){
		self::checkFile(self::$STUDENTS_PATH);
		$handle = fopen(self::$STUDENTS_PATH, 'r');
		$output = [];
		while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
			$output[] = rtrim($data[1]);
		}
		return $output;
	}

	/****************************************************/
	/* statut() 
		Recherche du statut de l'utilisateur à partir de son mail

		Entrée :
			$user: [string] - 'jean.dupont@uha.fr' // adresse mail de l'utilisateur 
		
		Sortie :
			[int] - ETUDIANT | PERSONNEL | ADMINISTRATEUR | INCONNU // INCONNU une personne connue mais pas dans les listings de la composante.
	*/
	/****************************************************/
	public static function statut($user, $forceRenew = false){
		if($forceRenew || !isset($_SESSION['statut']) || $_SESSION['statut'] == ''){
			
			/* Vérification de l'existence des fichiers de listes */
			self::checkFile(self::$STUDENTS_PATH);
			self::checkFile(self::$USERS_PATH);
			foreach(self::$STAF_PATH as $stafPath){
				self::checkFile($stafPath);
			}

			$pattern = '/'. $user .'/i';

		/* 
		L'ordre est ici important : un étudiant peut également avoir le statut de personnel si par exemple il est en apprentissage à l'IUT.

		De même, certains personnels peuvent encore avoir le statut d'étudiant (ATER par exemple).

		On peut donc forcer un statut en plaçant la personne comme admin ou vacataire, sinon par défaut c'est étudiant et enfin personnel.
		*/
			/* Test administrateur */
			foreach(json_decode(file_get_contents(self::$USERS_PATH)) as $departement => $dep){
				if(preg_grep($pattern, $dep->administrateurs)){
					$_SESSION['statut'] = ADMINISTRATEUR;
					return $_SESSION['statut'];
				}
			}
			
			/* Test vacataire */
			foreach(json_decode(file_get_contents(self::$USERS_PATH)) as $departement => $dep){
				if(preg_grep($pattern, $dep->vacataires)){
					$_SESSION['statut'] = PERSONNEL;
					return $_SESSION['statut'];
				}
			}

			/* Test étudiant */
			if(preg_grep($pattern, file(self::$STUDENTS_PATH))){
				$_SESSION['statut'] = ETUDIANT;
				return $_SESSION['statut'];
			}

			/* Test personnel */
			foreach(self::$STAF_PATH as $stafPath){
				if(preg_grep($pattern, file($stafPath))){
					$_SESSION['statut'] = PERSONNEL;
					return $_SESSION['statut'];
				}
			}

			//$_SESSION['statut'] = INCONNU;
			$_SESSION['statut'] = ETUDIANT;
		}
		return $_SESSION['statut'];	
	}

	/****************************************************/
	/* checkFile() 
		Vérifie l'existance du fichier liste passé en paramètre

		Entrée :
			$file : [string] - Nom du fichier 
		
		Sortie :
			Le cas échéant : Affiche un message d'erreur et interrompt le script
	*/
	/****************************************************/
	private static function checkFile($file)
	{
		if(!file_exists($file))
			returnError("Fichier inexistant : <b>$file</b><br>Veuillez mettre les listes des utilisateurs à jour.");
	}
}