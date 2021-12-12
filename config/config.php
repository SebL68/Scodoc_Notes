<?php
/************************************************/
/* Class contenant les données de configuration */
/************************************************/
	class Config {
		public static $departements = [
				'GEA',
				'GEII',
				'GLT',
				'GMP',
				'MMI',
				'SGM'
			];
	/*******************************************/
	/* Déclaration du domaine DNS de l'UFR pour
		les mails utilisateurs dans la zone admin
	/*******************************************/
		public static $DNS = 'uha.fr';
		
	/********************************/
	/* Clé pour les jetons JWT      */
	/********************************/
		public static $JWT_key = ''; // Clé pour les jetons ou rien pour désactiver

	/********************************************/
	/* Class à utiliser pour l'authentification */
	/* On peut alors utiliser un autre système  */
	/********************************************/
		public static $auth_class = 'auth_CAS.class.php';	
	
	/*******************************************************/
	/* Class à utiliser pour accéder au service d'annuaire */
	/* On peut aussi utiliser un autre système             */
	/*******************************************************/
		public static $service_annuaire_class = 'service_annuaire_LDAP.class.php';

	/**********************/
	/* Configuration LDAP */
	/**********************/
	// Identifiants pour accéder au serveur LDAP
		public static $LDAP_url = 'ldap://ldap.uha.fr:389';
		public static $LDAP_user = 'uid=didev,ou=dsa,dc=uha,dc=fr';
		public static $LDAP_password = 'MDP_LDAP';

	// Désignation du Distinguished Name dans LDAP
		public static $LDAP_dn = 'dc=uha,dc=fr';

	// Champs LDAP utilisés pour les listes d'utilisateurs
		public static $LDAP_uid = 'uid';      // Numéro d'étudiant ou d'enseignant
		public static $LDAP_mail = 'mail';

	// Filtre LDAP de l'UFR (supannaffectation)
		public static $LDAP_filtre_ufr = 'supannaffectation=Institut Universitaire de Technologie de Mulhouse';
	// Filtre LDAP étudiants (edupersonaffiliation)
		public static $LDAP_filtre_statut_etudiant = 'edupersonaffiliation=student';
	// Filtre LDAP enseignants (edupersonaffiliation)
		public static $LDAP_filtre_enseignant = '&(!(edupersonaffiliation=staff))(edupersonaffiliation=teacher)(!(edupersonaffiliation=affiliate))(!(edupersonaffiliation=student))';
	// Filtre LDAP BIATSS (edupersonaffiliation)
		public static $LDAP_filtre_biatss = '&(edupersonaffiliation=staff)(!(edupersonaffiliation=teacher))(!(edupersonaffiliation=affiliate))';

	/**********************************************************/
	/* Class à utiliser pour gérer la planification de tâches */
	/* On peut aussi utiliser un autre système                */
	/**********************************************************/
		public static $scheduler_class = 'scheduler_crontab.class.php';

	/**************************/
	/* Paramétrage de crontab */
	/**************************/
		public static $tmp_dir = '/tmp';			// Dossier temporaire utilisé lors de la programmation de CRON
		public static $CRON_delay = '0 0 * * *';	// Périodicité de mise à jour des listes d'utilisateurs :
													// '*/2 * * * *' => Toutes les 2 minutes
													// '0 * * * *'   => Toutes les heures à xxh00
													// '0 0 * * *'   => Tous les jours à 00h00

	/********************************/
	/* Accès à Scodoc               */
	/********************************/
	/*	Il faut créer compte avec un accès "secrétariat" qui a accès à tous les départements */

		public static $scodoc_url = 'https://iutmscodoc9.uha.fr/ScoDoc';
		public static $scodoc_login = [
			'__ac_name' => 'utilisateurScodoc',
			'__ac_password' => 'MDP_Scodoc'
		];
		
		public static $scodoc_login2 = 'utilisateurScodoc';
		public static $scodoc_psw = 'MDP_Scodoc';

	/****************************/
	/* Configuration du serveur */
	/****************************/
		public static $webServerUser = 'www-data';	/* Nécessaire ? */
		public static $webServerGroup = 'www-data';	/* Nécessaire ? */
		public static $PHP_cmd = '/usr/bin/php';
	
	/************************/
	/* Gestion des absences */
	/************************/
		public static $absences_creneaux = [
			[8, 10],
			[10, 12],
			[14, 16],
			[16, 18],
			[18, 20]
		];
	}

/***************************************/
/* Déclaration des constantes globales */
/***************************************/
	$CONSTANTES = [
		'INCONNU'             => 0,
		'ETUDIANT'            => 10,
		'PERSONNEL'           => 20,
		'ADMINISTRATEUR'      => 30,
		'SUPERADMINISTRATEUR' => 40
	];

	foreach($CONSTANTES as $const => $val) {
		define($const, $val);
	}
?>