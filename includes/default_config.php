<?php
	if(!isset($path) && $path != ''){
		$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	}	
	include_once $path.'/config/config.php';

	if(!isset(Config::$config_version) || Config::$config_version != '1.0.0'){
		die(
			json_encode(
				array(
					'erreur' => 'ADMIN : Le fichier config est obsolète, veuillez récupérer et compléter la nouvelle version à partir de GIT.'
				)
			)
		);
	}

	$Config = new stdClass();

		$Config->passerelle_version = '5:0:3:stable';

/***********************/
/* Options d'affichage */
/***********************/
		$Config->releve_PDF = Config::$releve_PDF ?? true; // Affichage de l'option pour que les étudiants puissent télécharger leur relevé en version PDF.
		$Config->nom_IUT = Config::$nom_IUT ?? 'IUT'; // Nom de l'IUT, par exemple : 'IUT de Mulhouse'.
		$Config->message_non_publication_releve = Config::$message_non_publication_releve ?? 'Le responsable de votre formation a décidé de ne pas publier le relevé de notes de ce semestre.'; // Message si le relevé n'est pas publié.


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

/*********************/
/* Analyse du trafic */
/*********************/
	/*
		Module optionnel d'analyse des connexions au site
		Ce module est interne à la passerelle et conforme au RGPD.
		Il peut dans une certains mesure remplacer un système de type Google Analytics ou Matomo.
		Si vous souhaitez utiliser un autre système, vous pouvez compléter le fichier analytics.php 
	*/
		$Config->analystics_interne = Config::$analystics_interne ?? false;

/*********************************/
/* Données retournées par le CAS */
/*********************************/
		$Config->CAS_return_type = Config::$CAS_return_type ?? 'nip';	// Valeurs possibles : 
														//  - 'nip' : numéro d'étudiant
														//  - 'idCAS' : un identificant autre (mail, identifiant LDAP ou autres)

	/* Certains nip ne correspondent pas à ce qui est dans Scodoc, parfois une lettre à changer
		La fonction nipModifier fonction permet d'appliquer une modification avant d'utilliser le nip / mail
	*/

		/*$nipModifier = function($nip){
			return Config::nipModifier($nip);
		};*/

	/* La passerelle tente de récupérer le nom de l'utilisateur depuis le CAS (champs cn ou displayName), mais cette donnée n'est pas toujours disponible. Il est alors parfois possible de récupérer ce nom de l'utilisateur à afficher à partir de l'idCAS.

	Si aucune de ces solutions ne fonctionne, le système affiche par défaut 'Mme, M.'. */

		$Config->nameFromIdCAS = function($idCAS) {
			if(method_exists(Config::class, 'nameFromIdCAS')){
				return Config::nameFromIdCAS($idCAS);
			}else{
				return;
			}
		};

/********************************/
/* Accès à Scodoc               */
/********************************/
	/*	Il faut créer compte avec un accès "secrétariat" qui a accès à tous les départements */

		$Config->scodoc_url = Config::$scodoc_url;	// ⚠️⚠️⚠️ Attention, il doit y avoir /Scodoc à la fin	
		$Config->scodoc_login = Config::$scodoc_login;
		$Config->scodoc_psw = Config::$scodoc_psw;
		
/*********************************************/
/* Configuration du format des ID et Nom des */
/* comptes utilisateurs dans la partie Admin */
/*********************************************/
	/* Contribution de Denis Graef */

		$Config->idReg = Config::$idReg ?? '^.+$';										// On accepte tous les ID CAS
		$Config->idPlaceHolder = Config::$idPlaceHolder ?? 'Identifiant CAS';			// Place Holder pour saisie de l'ID CAS
		$Config->idInfo = Config::$idInfo ?? 'Ajoutez l\x27identifiant CAS';			// Infobulle pour saisie de l'ID CAS (\x27 = unicode de l'apostrophe)
		$Config->nameReg = Config::$nameReg ?? '^.+$';									// On accepte tous les Noms
		$Config->namePlaceHolder = Config::$namePlaceHolder ?? 'Nom utilisateur';		// Place Holder pour saisie du Nom de l'utilisateur
		$Config->nameInfo = Config::$nameInfo ?? 'Indiquez le nom';						// Infobulle pour saisie du Nom de l'utilisateur
				
/********************************/
/* Clé pour les jetons JWT      */
/********************************/
	// Les jetons JWT peuvent être utilisés pour se faire passer pour n'importe quel utilisateur
	// C'est également le seul moyen d'avoir le statut superadministrateur
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

		$Config->LDAP_verify_TLS =  Config::$LDAP_verify_TLS ?? true;
		$Config->LDAP_protocol_3 = Config::$LDAP_protocol_3 ?? false; /* Active les options : 
																			ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
																			ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3); */

	// Désignation du Distinguished Name dans LDAP
		$Config->LDAP_dn = Config::$LDAP_dn ?? '';

	// Champs LDAP utilisés pour les listes d'utilisateurs
		$Config->LDAP_uid = Config::$LDAP_uid ?? 'uid';
		$Config->LDAP_idCAS = Config::$LDAP_idCAS ?? 'mail';

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
		$Config->absence_heureDebut = Config::$absence_heureDebut ?? 8;
		$Config->absence_heureFin = Config::$absence_heureFin ?? 20;
		$Config->absence_pas = Config::$absence_pas ?? 0.5;
		$Config->absence_dureeSeance = Config::$absence_dureeSeance ?? 2;

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
