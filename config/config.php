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
	/**********************************/
	/* Activation des modules du site */
	/**********************************/
		/* 
			L'accès enseignants permet aux enseignants de :
				- voir les notes de n'importe quel étudiant,
				- obtenir des documents bien pratiques,
				- gérer les absences sur la passerelle (système différent de Scodoc).

			Cet accès nécessite de maintenir à jour les listes d'utilisateurs dans les fichiers /data/annuaires - le but étant de différencier un étudiant d'un enseignant.
			Ces listes peuvent être générées automatiquement avec LDAP - voir la suite de la configuration.
			Il est également possible d'ajouter les utilisateurs en tant que "vacataire" dans le menu "Comptes" du site sans passer par LDAP.

			Acutellement les comptes sont gérés par des adresses mail - à voir s'il est nécessaire de configurer l'accès par des nip données par le CAS - me contacter.
		*/
		public static $acces_enseignants = false;
		public static $afficher_absences = false;	// En dessous du relevé de notes étudiants
		public static $module_absences = false;		// nécessite l'$acces_enseignants - ce module est différent de celui de Scodoc, il est géré entièrement par la passerelle.

	/*********************************/
	/* Données retournées par le CAS */
	/*********************************/
		public static $CAS_return_type = 'nip';	// Autre valeur possible : 'mail'


	/********************************/
	/* Accès à Scodoc               */
	/********************************/
	/*	Il faut créer compte avec un accès "secrétariat" qui a accès à tous les départements */

		public static $scodoc_url = 'https://iutmscodoc9.uha.fr/ScoDoc';
		public static $scodoc_login = [
			'__ac_name' => 'LOGIN_SCODOC',
			'__ac_password' => 'MDP_SCODOC'
		];
		
		public static $scodoc_login2 = 'LOGIN_SCODOC';	// Test pour la nouvelle API
		public static $scodoc_psw = 'MDP_SCODOC';
		
	/*******************************************/
	/* Déclaration du domaine DNS de l'UFR pour
		les mails utilisateurs dans la zone admin
	/*******************************************/
		public static $DNS = 'uha.fr';
		
	/********************************/
	/* Clé pour les jetons JWT      */
	/********************************/
		public static $JWT_key = ''; // Laisser vide si on n'utilise pas les jetons JWT

	/********************************************/
	/* Class à utiliser pour l'authentification */
	/* On peut alors utiliser un autre système  */
	/********************************************/
		public static $auth_class = 'auth_CAS.class.php';	

	
/* __________________________________________________________ */
/*															  */
/* LDAP n'est pas obligatoire et dépend des modules utilisés  */
/* __________________________________________________________ */


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
		public static $LDAP_password = 'Mot_De_Passe';

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
/* ________________ */
/*				    */
/* Fin config LDAP  */
/* ________________ */


	/****************************/
	/* Configuration du serveur */
	/****************************/
		public static $webServerUser = 'www-data';	/* Nécessaire ? */
		public static $webServerGroup = 'www-data';	/* Nécessaire ? */
		public static $PHP_cmd = '/usr/bin/php';
	
	/**************************************************/
	/* Gestion des absences - si le module est activé */
	/**************************************************/
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