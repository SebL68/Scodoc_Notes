<?php
/************************************************/
/* Class contenant les données de configuration */
/************************************************/
	class Config {
		public static $config_version = '1.0.0';
		
/***********************/
/* Options d'affichage */
/***********************/
		// public static $releve_PDF = false; // Affiche ou non l'option pour mettre aux étudiants de télécharger leur relevé au format PDF.
		public static $nom_IUT = 'IUT'; // Nom de votre IUT, par exemple : 'IUT de Mulhouse'.

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
		public static $CAS_return_type = 'nip';	// Valeurs possibles : 
													//  - 'nip' : numéro d'étudiant
													//  - 'idCAS' : un identificant autre (mail, identifiant LDAP ou autres)

	/* Certains nip ne correspondent pas à ce qui est dans Scodoc, parfois une lettre à changer
		La fonction nipModifier fonction permet d'appliquer une modification avant d'utilliser le nip / mail
		
		Voir /includes/annuaire.class.php -> getStudentNumberFromMail()
	*/

		public static function nipModifier($nip){
			//return '2'.substr($nip, 1); // Exemple pour remplacer la première lettre du nip par un 2
			return $nip;
			
		}

/********************************/
/* Accès à Scodoc               */
/********************************/
	/*	Il faut créer compte avec un accès "secrétariat" qui a accès à tous les départements */

		public static $scodoc_url = 'https://iutmscodoc9.uha.fr/ScoDoc';	// Attention, il doit y avoir /Scodoc à la fin	
		public static $scodoc_login = 'LOGIN_SCODOC';
		public static $scodoc_psw = 'MDP_SCODOC';
		
/*****************************************************/
/* Configuration du format des ID et Nom des comptes */ 
/* utilisateurs dans la partie Admininistration      */
/*****************************************************/
	// Format de l'ID : Adresse mail @uha.fr
	public static $idReg = '^[a-z0-9_-]+[.][a-z0-9_-]+@uha.fr$';
	public static $idPlaceHolder = 'prenom.nom@uha.fr';
	public static $idInfo = 'Adresse mail de l\x27utilisateur';					// Message affiché dans l'infobulle - \x27 = unicode de l'apostrophe

	// Format du Nom : Une chaine de caractères commençant par une lettre majuscule
	public static $nameReg = '^[A-Z][a-zA-Z\xc0-\xff\x27 -]*$';					// \xc0-\xff = plage unicodes des caractères accentués - \x27 = unicode de l'apostrophe
	public static $namePlaceHolder = 'Nom Prénom';
	public static $nameInfo = 'Nom et prénom de l\x27utilisateur';			// Message affiché dans l'infobulle - \x27 = unicode de l'apostrophe

/********************************/
/* Clé pour les jetons JWT      */
/********************************/
		// public static $JWT_key = ''; // Clé de cryptage JWT : une chaine de caratères aléatoires. Laisser vide si vous n'utilisez pas les jetons
		
/********************************************/
/* Class à utiliser pour l'authentification */
/* On peut alors utiliser un autre système  */
/********************************************/
		/* Si vous souhaitez utiliser un autre système d'auhtentification :
			- prenez pour exemple ce qui est dans auth_CAS.class.php,
			- créez votre propre class, par exemple auth_OAuth.class.php,
			- utilisez le même nom de class que dans la class d'origine,
			- utilisez les mêmes méthodes et renvoyez les données suivant les mêmes formats
		*/
		// public static $auth_class = 'auth_CAS.class.php'; 
		
/*******************************************************/
/* Class à utiliser pour accéder au service d'annuaire */
/* On peut aussi utiliser un autre système             */
/*******************************************************/
		/* Voir commentaires pour auth_CAS.class.php */
		// public static $service_annuaire_class = 'service_annuaire_LDAP.class.php';
/* __________________________________________________________ */
/*                                                            */
/* LDAP n'est pas obligatoire et dépend des modules utilisés  */
/* __________________________________________________________ */
/**********************/
/* Configuration LDAP */
/**********************/
	// Identifiants pour accéder au serveur LDAP
		public static $LDAP_url = 'ldap://ldap.uha.fr:389';
		public static $LDAP_user = 'uid=didev,ou=dsa,dc=uha,dc=fr';
		public static $LDAP_password = 'MDP_LDAP';

		public static $LDAP_verify_TLS = true;	// Active ou désactive le TLS pour la connexion LDAP

	// Désignation du Distinguished Name dans LDAP
		public static $LDAP_dn = 'dc=uha,dc=fr';

	// Champs LDAP utilisés pour créer les listes d'utilisateurs
		public static $LDAP_uid = 'uid';      	// Numéro d'étudiant ou d'enseignant
		public static $LDAP_idCAS = 'mail';		// Ce champs reflète l'idCAS qui se trouve dans le LDAP - peut être différent de mail.
		public static $LDAP_autocompletion = 'mail';	// Données qui servent pour l'autocomplétion du mode enseignant
		/* !!!!!!!!!!!!!!!!!
			LDAP_autocompletion peut être différent de LDAP_idCAS que si $CAS_return_type = 'nip'
			La raison est que si $CAS_return_type != 'nip', on utilise le fichier pour faire le lien entre l'idCAS et le nip
		!!!!!!!!!!!!!!!!! */

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
		/* Voir commentaires pour auth_CAS.class.php */
		//public static $scheduler_class = 'scheduler_crontab.class.php';

	/**************************/
	/* Paramétrage de crontab */
	/**************************/
		//public static $tmp_dir = '/tmp';		// Dossier temporaire utilisé lors de la programmation de CRON
		//public static $CRON_delay = '0 0 * * *';	// Périodicité de mise à jour des listes d'utilisateurs :
													// '*/2 * * * *' => Toutes les 2 minutes
													// '0 * * * *'   => Toutes les heures à xxh00
													// '0 0 * * *'   => Tous les jours à 00h00
/* ________________ */
/*                  */
/* Fin config LDAP  */
/* ________________ */

/*******************************************************/
/* Ajout de données "custom" à afficher dans le relevé 
	Permet également de modifier les data avant l'envoi */
/*******************************************************/
		/*public static function customOutput($output){
			/////////////////////////////////////////////////////////////////////
			// $output contient les data juste avant de les renvoyer
			// Il faut tester ce qui est renvoyé pour ajouter les données si nécessaire
			// Il est possible d'ajouter 
			// if(isset($output['relevé'])){
			//	$output['releve'] = ['custom' => "Données au format HTML"];
			// }
			// Ces données sont insérées dans le relevé BUT entre la fiche étudiant et la synthèse
			
			/////////////////////////////////////////////////////////////////////
			// Autre exemple, pour supprimer les données de la partie synthèse :
			// if( isset($output['relevé']) 
			// 	&& isset($output['relevé']->type) 
			// 	&& $output['relevé']->type == 'BUT'
			// ){
			// 	$output['relevé']->ues = array();
			// }
			
			////////////////////////////////////////////////////////////////////
			return $output;
		}*/
		
/*******************************************/
/* Permet de modifier les photos renvoyées */
/*******************************************/
		/*public static function customPic($nip){
		
		}*/
		
/****************************/
/* Configuration du serveur */
/****************************/
		// public static $PHP_cmd = '/usr/bin/php';		
		
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
?>
