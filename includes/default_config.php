<?php
	if(!isset($path) && $path != ''){
		$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	}	
	include_once $path.'/config/config.php';

	if(!isset(Config::$config_version) || Config::$config_version != '1.0.0'){
		die(
			json_encode(
				array(
					'erreur' => 'ADMIN : Le fichier config est obsolète, veuillez récupérer et compléter la nouvelle version à partir du GIT.'
				)
			)
		);
	}

	$Config = (object) [];

/***********************/
/* Options d'affichage */
/***********************/
		$Config->releve_PDF = Config::$releve_PDF ?? true; // Affichage de l'option pour que les étudiants puissent télécharger leur relevé en version PDF.

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
		$Config->acces_enseignants = Config::$acces_enseignants ?? false;
		$Config->afficher_absences = Config::$afficher_absences ?? false;	// En dessous du relevé de notes étudiants
		$Config->module_absences = Config::$module_absences ?? false;		// nécessite l'acces_enseignants - ce module est différent de celui de Scodoc, il est géré entièrement par la passerelle.

/*********************************/
/* Données retournées par le CAS */
/*********************************/
		$Config->CAS_return_type = Config::$CAS_return_type ?? 'nip';	// Valeurs possibles : 
														//  - 'nip' : numéro d'étudiant
														//  - 'idCAS' : un identificant autre (mail, identifiant LDAP ou autres)

	/* Certains nip ne correspondent pas à ce qui est dans Scodoc, parfois une lettre à changer
		La fonction nipModifier fonction permet d'appliquer une modification avant d'utilliser le nip / mail
		
		Voir /includes/annuaire.class.php -> getStudentNumberFromIdCAS()
	*/

		/*$nipModifier = function($nip){
			Config::nipModifier();
		};*/

/********************************/
/* Accès à Scodoc               */
/********************************/
	/*	Il faut créer compte avec un accès "secrétariat" qui a accès à tous les départements */

		$Config->scodoc_url = Config::$scodoc_url;	// Attention, il doit y avoir /Scodoc à la fin	
		$Config->scodoc_login = Config::$scodoc_login;
		$Config->scodoc_psw = Config::$scodoc_psw;
		
/*******************************************/
/* Déclaration du domaine DNS de l'UFR pour
	les mails utilisateurs dans la zone admin
/*******************************************/
		$Config->DNS = Config::$DNS;
		
/********************************/
/* Clé pour les jetons JWT      */
/********************************/
		$Config->JWT_key = Config::$JWT_key ?? ''; // Laisser vide si vous n'utilisez pas les jetons

/********************************************/
/* Class à utiliser pour l'authentification */
/* On peut alors utiliser un autre système  */
/********************************************/
		$Config->auth_class = Config::$auth_class ?? 'auth_CAS.class.php';	

	
/* __________________________________________________________ */
/*															  */
/* LDAP n'est pas obligatoire et dépend des modules utilisés  */
/* __________________________________________________________ */


	/*******************************************************/
	/* Class à utiliser pour accéder au service d'annuaire */
	/* On peut aussi utiliser un autre système             */
	/*******************************************************/
		$Config->service_annuaire_class = Config::$service_annuaire_class ?? 'service_annuaire_LDAP.class.php';

	/**********************/
	/* Configuration LDAP */
	/**********************/
	// Identifiants pour accéder au serveur LDAP
		$Config->LDAP_url = Config::$LDAP_url ?? '';
		$Config->LDAP_user = Config::$LDAP_user ?? '';
		$Config->LDAP_password = Config::$LDAP_password ?? '';

		$Config->LDAP_password =  Config::$LDAP_verify_TLS ?? true;

	// Désignation du Distinguished Name dans LDAP
		$Config->LDAP_dn = Config::$LDAP_dn ?? '';

	// Champs LDAP utilisés pour les listes d'utilisateurs
		$Config->LDAP_uid = Config::$LDAP_uid ?? 'uid';      // Numéro d'étudiant ou d'enseignant
		$Config->LDAP_mail = Config::$LDAP_mail ?? 'mail';

	// Filtre LDAP de l'UFR (supannaffectation)
		$Config->LDAP_filtre_ufr = Config::$LDAP_filtre_ufr ?? '';
	// Filtre LDAP étudiants (edupersonaffiliation)
		$Config->LDAP_filtre_statut_etudiant = Config::$LDAP_filtre_statut_etudiant ?? '';
	// Filtre LDAP enseignants (edupersonaffiliation)
		$Config->LDAP_filtre_enseignant = Config::$LDAP_filtre_enseignant ?? '';
	// Filtre LDAP BIATSS (edupersonaffiliation)
		$Config->LDAP_filtre_biatss = Config::$LDAP_filtre_biatss ?? '';

	/**********************************************************/
	/* Class à utiliser pour gérer la planification de tâches */
	/* On peut aussi utiliser un autre système                */
	/**********************************************************/
		$Config->scheduler_class = Config::$scheduler_class ?? 'scheduler_crontab.class.php';

	/**************************/
	/* Paramétrage de crontab */
	/**************************/
		$Config->tmp_dir = Config::$tmp_dir ?? '/tmp';			// Dossier temporaire utilisé lors de la programmation de CRON
		$Config->CRON_delay = Config::$CRON_delay ?? '0 0 * * *';	// Périodicité de mise à jour des listes d'utilisateurs :
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
		$Config->PHP_cmd = Config::$PHP_cmd ?? '/usr/bin/php';
	
/**************************************************/
/* Gestion des absences - si le module est activé */
/**************************************************/
		$Config->absences_creneaux = Config::$absences_creneaux ?? [
			[8, 10],
			[10, 12],
			[14, 16],
			[16, 18],
			[18, 20]
		];

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