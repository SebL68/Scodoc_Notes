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
Annuaire::$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
include_once "$path/config/config.php";

Annuaire::$STUDENTS_PATH = "$path/data/annuaires/liste_etu.txt";

Annuaire::$VAC_PATH = "$path/data/annuaires/vacataires.json";

Annuaire::$STAFF_PATH = [
	$path.'/data/annuaires/liste_ens.txt',
	$path.'/data/annuaires/liste_biat.txt'
];
/* !!! Il faut certainement vérifier si les "pattern" dans les fonctions et la sélection dans getAllStudents() correspondent à vos fichiers d'annuaires !!! */

Annuaire::$ADMIN_PATH = "$path/data/annuaires/administrateurs.json";

class Annuaire{
	static $path;
	static $STUDENTS_PATH;
	static $VAC_PATH;
	static $STAFF_PATH;
	static $ADMIN_PATH;
	
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
		// Attention, le listing LDAP fourni e1912345 alors que le vrai numéro est 21912345.

		self::checkFile(self::$STUDENTS_PATH);
		$handle = fopen(self::$STUDENTS_PATH, 'r');
		while(($line = fgets($handle, 1000)) !== FALSE){
			$data = explode(':', $line);
			if(rtrim($data[1]) == $mail)
				return '2'.substr($data[0], 1);
		}

		exit(
			json_encode(
				array(
					'erreur' => "Votre compte n'est pas encore dans l'annuaire. La mise à jour est faite en général tous les 15 jours, si le problème persiste, contactez votre responsable."
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
	public static function statut($user){
		/* Retour : ETUDIANT, PERSONNEL, ADMINISTRATEUR ou INCONNU */
		if(!isset($_SESSION['statut']) || $_SESSION['statut'] == ''){
			
			/* Vérification de l'existence des fichiers de listes */
			self::checkFile(self::$VAC_PATH);
			self::checkFile(self::$STUDENTS_PATH);
			self::checkFile(self::$ADMIN_PATH);
			foreach(self::$STAFF_PATH as $staffPath){
				self::checkFile($staffPath);
			}

			self::$path;
			$pattern = '/'. $user .'/i';

			/* Test vacataire */
			if(preg_grep($pattern, file(self::$VAC_PATH))){
				$_SESSION['statut'] = PERSONNEL;
				return $_SESSION['statut'];
			}

			/* Test étudiant */
			if(preg_grep($pattern, file(self::$STUDENTS_PATH))){
				$_SESSION['statut'] = ETUDIANT;
				return $_SESSION['statut'];
			}
			/* Test administrateur */
			foreach(json_decode(file_get_contents(self::$STAF_PATH)) as $departement => $admins){
				if(preg_grep($pattern, $admins)){
					$_SESSION['statut'] = ADMINISTRATEUR;
					return $_SESSION['statut'];
				}
			}
			/* Test personnel */
			foreach(self::$STAFF_PATH as $staffPath){
				if(preg_grep($pattern, file($staffPath))){
					$_SESSION['statut'] = PERSONNEL;
					return $_SESSION['statut'];
				}
			}

			$_SESSION['statut'] = INCONNU;
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
?>