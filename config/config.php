<?php
/************************************************/
/* Class contenant les données de configuration */
/************************************************/
	class Config {
		public static $config_version = '1.0.0';
		/* Sensitif à la case */
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
		
/*******************************************/
/* Déclaration du domaine DNS de l'UFR pour
	les mails utilisateurs dans la zone admin
/*******************************************/
		public static $DNS = 'uha.fr';

/* __________________________________________________________ */
/*															  */
/* LDAP n'est pas obligatoire et dépend des modules utilisés  */
/* __________________________________________________________ */
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