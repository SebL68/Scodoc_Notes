<?php
/*****************************************************/
/* Fonctions de communication avec les listings LDAP */
/*****************************************************/
$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

/*********************/
/*   Configurations  */
/*********************/

include_once "$path/includes/config.php";

$STUDENTS_PATH = "$path/LDAP/liste_etu_iutmulhouse.txt";

$STAFF_PATH = [
	$path . '/LDAP/liste_ens_iutmulhouse.txt',
	$path . '/LDAP/liste_biat_iutmulhouse.txt',
	$path . '/LDAP/vacataires.txt'
];
/* !!! Il faut certainement vérifier si les "pattern" dans les fonctions et la sélection dans getAllLDAPStudents() correspondent à vos fichiers d'export LDAP !!! */

$ADMIN_PATH = "$path/LDAP/administrateurs.json";

/****************************************************/
/* getStudentNumberFromMail()
	Recherche du numéro d'étudiant en fonction de son mail dans le listing LDAP

	Entrée :
		$mail: [string] - 'jean.dupont@uha.fr' // adresse mail de l'utilisateur 
	
	Sortie :
		[string] - '21912345' // numéro d'étudiant
*/
/****************************************************/

function getStudentNumberFromMail($mail){
	// Recherche du numero d'étudiant en fonction de son mail dans une chaîne de caractère de type :
	// e1912345:jean.dupont@uha.fr
	// Attention, le listing LDAP fourni e1912345 alors que le vrai numéro est 21912345.

	$handle = fopen($GLOBALS['STUDENTS_PATH'], 'r');
	while(($line = fgets($handle, 1000)) !== FALSE){
		$data = explode(":", $line);
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
	Recherche du mail de l'étudiant en fonction de son numéro dans le listing LDAP

	Entrée :
		$num: [string] - '21912345' // numéro d'étudiant
	
	Sortie :
		[string] - 'jean.dupont@uha.fr' // adresse mail de l'utilisateur 
*/
/****************************************************/
function getStudentMailFromNumber($num){
	// Recherche du mail en fonction du numéro dans une chaîne de caractère de type :
	// e1912345:jean.dupont@uha.fr
	// Attention, le listing LDAP fourni e1912345 alors que le vrai numéro est 21912345.

	$handle = fopen($GLOBALS['STUDENTS_PATH'], 'r');
	while(($line = fgets($handle, 1000)) !== FALSE){
		$data = explode(":", $line);
		if(substr($data[0], 1) == substr($num, 1))
			return rtrim($data[1]);
	}
}

/*******************************/
/* getAllLDAPStudents()
	Liste de l'ensemble des étudiants du LDAP

	Entrée :
		/

	Sortie :
		["etudiant1@uha.fr", "etudiant2@uha.fr", etc.]
*/
/*******************************/
function getAllLDAPStudents(){
	$handle = fopen($GLOBALS['STUDENTS_PATH'], "r");
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
function statut($user){
	/* Retour : ETUDIANT, PERSONNEL, ADMINISTRATEUR ou INCONNU */
	if(!isset($_SESSION['statut']) || $_SESSION['statut'] == ''){
		global $path;
		$pattern = "/". $user ."/i";

		/* Test étudiant */
		if(preg_grep($pattern, file($GLOBALS['STUDENTS_PATH']))){
			$_SESSION['statut'] = ETUDIANT;
			return $_SESSION['statut'];
		}
		/* Test administrateur */
		foreach(json_decode(file_get_contents($GLOBALS['ADMIN_PATH'])) as $departement => $admins){
			if(preg_grep($pattern, $admins)){
				$_SESSION['statut'] = ADMINISTRATEUR;
				return $_SESSION['statut'];
			}
		}
		/* Test personnel */
		foreach($GLOBALS['STAFF_PATH'] as $staffPath){
			if(preg_grep($pattern, file($staffPath))){
				$_SESSION['statut'] = PERSONNEL;
				return $_SESSION['statut'];
			}
		}

		$_SESSION['statut'] = INCONNU;
	}
	return $_SESSION['statut'];	
}
?>