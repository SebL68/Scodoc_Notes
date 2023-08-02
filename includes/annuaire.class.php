<?php
/****************************/
/*  Class Annuaire
        Cette classe permet de rechercher les informations des utilisateurs issues des annuaires

        La méthode attendue est :
            - Annuaire::getStudentNumberFromIdCAS()
            - Annuaire::getStudentIdCASFromNumber()
            - Annuaire::statut()

/****************************/

/*********************/
/*   Configurations  */
/*********************/
$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
include_once "$path/includes/default_config.php";

Annuaire::$SUPER_ADMIN_PATH = "$path/data/annuaires/super_admin.txt";
Annuaire::$STUDENTS_PATH = "$path/data/annuaires/liste_etu.txt";
Annuaire::$USERS_PATH = "$path/data/annuaires/utilisateurs.json";
Annuaire::$STAF_PATH = [
	$path.'/data/annuaires/liste_ens.txt',
	$path.'/data/annuaires/liste_biat.txt'
];

class Annuaire{
	static $SUPER_ADMIN_PATH;
	static $STUDENTS_PATH;
	static $USERS_PATH;
	static $STAF_PATH;
	
	/****************************************************/
	/* getStudentNumberFromIdCAS()
		Recherche du numéro d'étudiant en fonction de son identifiant CAS dans l'annuaire
		Prend en compte si le CAS renvoie déjà un numéro d'étudiant

		Entrée :
			$id: [string] - 'jean.dupont@uha.fr' // identifiant 
		
		Sortie :
			[string] - '21912345' // numéro d'étudiant
	*/
	/****************************************************/

	public static function getStudentNumberFromIdCAS($id){
		global $Config;
		if($Config->CAS_return_type != 'nip'){
			self::checkFile(self::$STUDENTS_PATH);
			$handle = fopen(self::$STUDENTS_PATH, 'r');
			while(($line = fgets($handle, 1000)) !== FALSE){
				if($line != ''){
					$data = explode(':', $line);
					if(strcasecmp(rtrim($data[1]), $id) == 0)
						return Config::nipModifier($data[0]);
				}
			}
		} else {
			return Config::nipModifier($id);
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
	/* getStudentIdCASFromNumber()
		Recherche du idCAS de l'étudiant en fonction de son numéro dans l'annuaire

		Entrée :
			$num: [string] - '21912345' // numéro d'étudiant
		
		Sortie :
			[string] - 'jean.dupont@uha.fr' // idCAS de l'utilisateur 
	*/
	/****************************************************/
	public static function getStudentIdCASFromNumber($num){
		global $Config;
		if($Config->CAS_return_type == 'nip'){
			return $num;
		} else {
			self::checkFile(self::$STUDENTS_PATH);
			$handle = fopen(self::$STUDENTS_PATH, 'r');
			while(($line = fgets($handle, 1000)) !== FALSE){
				if($line != ''){
					$data = explode(':', $line);
					if(substr($data[0], 1) == substr($num, 1))
						return rtrim($data[1]);
				}
			}
		}
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
	public static function statut($user, $justAsk = false){
		global $Config;
		if($justAsk || !isset($_SESSION['statut']) || $_SESSION['statut'] == ''){
			if($user == ""){
				return INCONNU;
			}

		/* 
		L'ordre est ici important : un étudiant peut également avoir le statut de personnel si par exemple il est en apprentissage à l'IUT.

		De même, certains personnels peuvent encore avoir le statut d'étudiant (ATER par exemple).

		On peut donc forcer un statut en plaçant la personne comme admin ou vacataire, sinon par défaut c'est étudiant et enfin personnel.
		*/
			
			/* Test super administrateur */
			if(file_exists(self::$SUPER_ADMIN_PATH)){
				$pattern = '/\b'. preg_quote($user) .'\b/i';
				if(preg_grep($pattern, file(self::$SUPER_ADMIN_PATH))){
					return SUPERADMINISTRATEUR;
				}
			}

			if($Config->acces_enseignants != true){
				return ETUDIANT;
			}

			/* Test administrateur */
			if(file_exists(self::$USERS_PATH)){
				foreach(json_decode(file_get_contents(self::$USERS_PATH)) as $departement => $dep){
					foreach($dep->administrateurs as $identifiant){
						if($user == $identifiant->id){
							return ADMINISTRATEUR;
						}
					}
				}
			
			/* Test vacataire */
				foreach(json_decode(file_get_contents(self::$USERS_PATH)) as $departement => $dep){
					foreach($dep->vacataires as $identifiant){
						if($user == $identifiant->id){
							return PERSONNEL;
						}
					}
				}
			}
			
			/* Test étudiant */
			if(file_exists(self::$STUDENTS_PATH)){
				$pattern = '/:'. preg_quote($user) .'\b/i';
				if(preg_grep($pattern, file(self::$STUDENTS_PATH))){
					return ETUDIANT;
				}
			}

			/* Test personnel */
			$pattern = '/\b'. preg_quote($user) .'\b/i';
			foreach(self::$STAF_PATH as $stafPath){
				if(file_exists($stafPath)){
					if(preg_grep($pattern, file($stafPath))){
						return PERSONNEL;
					}
				}
			}

			return ETUDIANT;
		}
	}

	/****************************************************/
	/* getPersonnelDepartements() 
		Recherche les départements dans lesquels le personnel est affecté

		Entrée :
			$user: [string] - 'jean.dupont@uha.fr' // adresse mail de l'utilisateur 
		
		Sortie :
			[array] - ["dep1", "dep2", "dep3"] // liste des départements
	*/
	/****************************************************/
	public static function getPersonnelDepartements($user){
		$output = [];
		if(file_exists(self::$USERS_PATH)){
			foreach(json_decode(file_get_contents(self::$USERS_PATH)) as $departement => $dep){
				foreach($dep->administrateurs as $identifiant){
					if($user == $identifiant->id){
						$output[] = $departement;
					}
				}
				if(isset($dep->vacataires)) {
					foreach($dep->vacataires as $identifiant){
						if($user == $identifiant->id){
							$output[] = $departement;
						}
					}
				}
				
			}
		}
		return $output;
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