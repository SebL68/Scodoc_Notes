<?php
/***************************************/
/* Déclaration du domaine DNS de l'UFR */
/***************************************/

$DNS = "uha.fr"; 

/***************************************/
/* Déclaration des constantes globales */
/***************************************/

$CONSTANTES = [
	'INCONNU'				=> 0,
	'ETUDIANT'				=> 10,
	'PERSONNEL'				=> 20,
	'ADMINISTRATEUR'		=> 30,
	'SUPERADMINISTRATEUR'	=> 40
];

foreach($CONSTANTES as $const => $val) {
	define($const, $val);
}

/*****************************/
/* Déclaration de la clé JWT */
/*****************************/

$key = 'Votre_clé_JWT_personnelle';

/********************************/
/* Logins pour accéder à Scodoc */
/********************************/
/*	Il faut créer un compte par département qui fini par la nom du département
	Exemple : login_MMI
	
	Pour des raisons de sécurité, il est recommandé que ce compte ait le statut "secrétariat" l'accès sera alors uniquement en lecture 
*/

$acces = [
  '__ac_name' => 'identifiant_scodoc_',			// Dans Scodoc, ajouter le nom du département à la fin du login
  '__ac_password' => 'mot_de_passe_scodoc'
];

$scodoc_url = 'https://{URL_scodoc}/ScoDoc';	// Remplacer URL_scodoc par l'URL de votre Scodoc

/************************/
/* Gestion des absences */
/************************/

$creneaux = [
	[8, 10],
	[10, 12],
	[14, 16],
	[16, 18],
	[18, 20]
];
?>

