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

$DEPARTEMENTS = [
	'GEA',
	'GEII',
	'GLT',
	'GMP',
	'MMI',
	'SGM'
];

/*****************************/
/* Déclaration de la clé JWT */
/*****************************/

$key = 'Votre_clé_JWT_personnelle';

/********************************/
/* Logins pour accéder à Scodoc */
/********************************/
/*	Il faut créer compte avec un accès "secrétariat" qui a accès à tous les départements */

$acces = [
  '__ac_name' => 'identifiant_scodoc',			// Dans Scodoc, ajouter le nom du département à la fin du login
  '__ac_password' => 'mot_de_passe_scodoc'
];

$scodoc_url = 'https://{URL_scodoc}/ScoDoc';	// Remplacer URL_scodoc par l'URL de votre Scodoc

/****************************/
/* Configuration du serveur */
/****************************/
$webServerUser = "www-data";		
$webServerGroup = "www-data";
$PHP_cmd = "/usr/bin/php";			// Commande pour exécuter un script en ligne de commande

$tmp_dir = "/tmp";                  // Dossier temporaire utilisé lors de la programmation de CRON
$CRON_delay = "*/2 * * * *";        // Périodicité de mise à jour des listes d'utilisateurs :
                                    // "*/2 * * * *" => Toutes les 2 minutes
                                    // "0 * * * *"   => Toutes les heures à xxh00
                                    // "0 0 * * *"   => Tous les jours à 00h00

/**********************/
/* Configuration LDAP */
/**********************/
// Identifiants pour accéder au serveur LDAP
$LDAP_url = "ldap://{URL_LDAP}:{n°_port}";
$LDAP_user = "identifiant_LDAP";
$LDAP_password = "mot_de_passe_LDAP";

// Désignation du Distinguished Name dans LDAP
$LDAP_dn = "dn_organisation";

// Champs LDAP utilisés pour les listes d'utilisateurs
$LDAP_uid = "uid";      // Numéro d'étudiant
$LDAP_mail = "mail";	// Adresse mail d'étudiant ou de personnel

// Filtre LDAP de l'UFR (supannaffectation à adapter selon l'organisation de l'annuaire)
$LDAP_filtre_ufr = "supannaffectation=Nom_de_l'UFR";
// Filtre LDAP étudiants (edupersonaffiliation à adapter selon l'organisation de l'annuaire)
$LDAP_filtre_statut_etudiant = "edupersonaffiliation=student";
// Filtre LDAP enseignants (A adapter selon l'organisation de l'annuaire)
$LDAP_filtre_enseignant = "&(!(edupersonaffiliation=staff))(edupersonaffiliation=teacher)(!(edupersonaffiliation=affiliate))(!(edupersonaffiliation=student))";
// Filtre LDAP BIATSS (A adapter selon l'organisation de l'annuaire)
$LDAP_filtre_biatss = "&(edupersonaffiliation=staff)(!(edupersonaffiliation=teacher))(!(edupersonaffiliation=affiliate))";


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