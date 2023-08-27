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

/***********************************/	
/* Config modifiée par l'interface */
/***********************************/
	$file = $path.'/config/config.json';
	$configJSON = [];

	if(file_exists($file)){
		$configJSON = json_decode(file_get_contents($file), true);
	}

/***********************************/

	$Config = new stdClass();

		$Config->passerelle_version = '6:0:0';

/***********************/
/* Options d'affichage */
/***********************/
		$Config->releve_PDF = $configJSON['releve_PDF'] ?? Config::$releve_PDF ?? true; // Affichage de l'option pour que les étudiants puissent télécharger leur relevé en version PDF.
		$Config->etudiant_modif_photo = $configJSON['etudiant_modif_photo'] ?? Config::$etudiant_modif_photo ?? true; // Autoriser les étudiants à modifier leur photo.
		$Config->nom_IUT = $configJSON['nom_IUT'] ?? Config::$nom_IUT ?? 'IUT'; // Nom de l'IUT, par exemple : 'IUT de Mulhouse'.
		$Config->message_non_publication_releve = $configJSON['message_non_publication_releve'] ?? Config::$message_non_publication_releve ?? 'Le responsable de votre formation a décidé de ne pas publier le relevé de notes de ce semestre.'; // Message si le relevé n'est pas publié.


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
		*/
		// PAS IMPLEMENTÉ $Config->afficher_releves = Config::$afficher_releves ?? true;		// Permet d'utiliser la passerelle uniquement pour les absences en standalone
		$Config->acces_enseignants = $configJSON['acces_enseignants'] ?? Config::$acces_enseignants ?? false;
		
		$Config->afficher_absences = $configJSON['afficher_absences'] ?? Config::$afficher_absences ?? false;	// En dessous du relevé de notes étudiants
		$Config->module_absences = $configJSON['module_absences'] ?? Config::$module_absences ?? false;		// nécessite l'acces_enseignants - ce module est différent de celui de Scodoc, il est géré entièrement par la passerelle.
		$Config->data_absences_scodoc = $configJSON['data_absences_scodoc'] ?? Config::$data_absences_scodoc ?? false;	// Choisir si les absences sont stockées sur la passerelle ou dans Scodoc.
		$Config->metrique_absences = $configJSON['metrique_absences'] ?? Config::$metrique_absences ?? 'heure';	// Choisir le type de métrique pour l'affichage des totaux absences aux étudiants.
		$Config->autoriser_justificatifs = $configJSON['autoriser_justificatifs'] ?? Config::$autoriser_justificatifs ?? false;	// Choisir si les étudiants peuvent déposer des justificatifs d'absences qui seront importés dans Scodoc.
		$Config->liste_dep_ok_jusiticatifs = $configJSON['liste_dep_ok_jusiticatifs'] ?? Config::$liste_dep_ok_jusiticatifs ?? '';	// Liste des départements autorisant les justificatifs
		$Config->message_rapport_absences = $configJSON['message_rapport_absences'] ?? Config::$message_rapport_absences ?? "Les causes de l’absence doivent être notifiées par écrit à l'aide d'un justificatif dans les 48 heures à compter du début de l’absence au secrétariat du département. Voir règlement intérieur pour les motifs légitimes d'absence.";	//Message au début du rapport d'absences, après le relevé de notes.
		$Config->message_justificatifs = $configJSON['message_justificatifs'] ?? Config::$message_justificatifs ?? "";	// Message à ajouter dans la page justificatifs.

		$Config->cloisonner_enseignants = $configJSON['cloisonner_enseignants'] ?? Config::$cloisonner_enseignants ?? false; // Permettre a un enseignant d'avoir accès à tous les départements ou que ceux dans lesquels il intervient (onglet Comptes).
		
		$Config->doc_afficher_nip = $configJSON['doc_afficher_nip'] ?? Config::$doc_afficher_nip ?? true; // Permet d'avoir la data num étudiant dans Documents -> Données étudiants
		$Config->doc_afficher_id = $configJSON['doc_afficher_id'] ?? Config::$doc_afficher_id ?? true; // Permet d'avoir la data identifiant dans Documents -> Données étudiants
		$Config->doc_afficher_date_naissance = $configJSON['doc_afficher_date_naissance'] ?? Config::$doc_afficher_date_naissance ?? true; // Permet d'avoir la data date de naissance dans Documents -> Données étudiants

/*********************/
/* Analyse du trafic */
/*********************/
	/*
		Module optionnel d'analyse des connexions au site
		Ce module est interne à la passerelle et conforme au RGPD.
		Il peut dans une certains mesure remplacer un système de type Google Analytics ou Matomo.
		Si vous souhaitez utiliser un autre système, vous pouvez compléter le fichier analytics.php 
	*/
		$Config->analystics_interne = $configJSON['analystics_interne'] ?? Config::$analystics_interne ?? false;
		$Config->analyse_temps_requetes = $configJSON['analyse_temps_requetes'] ?? Config::$analyse_temps_requetes ?? false;	// Temps requêtes avec Scodoc - enregistré dans /data/analytics/temps.csv

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

	/* Il est également possible de récupérer le nip dans d'autres paramètres CAS, voir si c'est disponible dans /code_test/testCAS.php
		Par défaut : false - prend l'id d'identification retourné par le CAS
		Exemple de config : 'umCodeEtudiant'
	*/

		$Config->CAS_nip_key = Config::$CAS_nip_key ?? false;

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

/********************************************/
/* OU accès à un autre système de données   */
/********************************************/
		$Config->service_data_class = Config::$service_data_class ?? 'scodoc.class.php';	// autre valeur possible : 'service_data_standalone.class.php'
		
/*********************************************/
/* Configuration du format des ID et Nom des */
/* comptes utilisateurs dans la partie Admin */
/*********************************************/
	/* Contribution de Denis Graef */

		$Config->idReg = $configJSON['idReg'] ?? Config::$idReg ?? '^.+$';										// On accepte tous les ID CAS
		$Config->idPlaceHolder = $configJSON['idPlaceHolder'] ?? Config::$idPlaceHolder ?? 'Identifiant CAS';			// Place Holder pour saisie de l'ID CAS
		$Config->idInfo = $configJSON['idInfo'] ?? Config::$idInfo ?? 'Ajoutez l\'identifiant CAS';			// Infobulle pour saisie de l'ID CAS
		$Config->nameReg = $configJSON['nameReg'] ?? Config::$nameReg ?? '^.+$';									// On accepte tous les Noms
		$Config->namePlaceHolder = $configJSON['namePlaceHolder'] ?? Config::$namePlaceHolder ?? 'Nom utilisateur';		// Place Holder pour saisie du Nom de l'utilisateur
		$Config->nameInfo = $configJSON['nameInfo'] ?? Config::$nameInfo ?? 'Indiquez le nom';						// Infobulle pour saisie du Nom de l'utilisateur
				
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
		$Config->absence_heureDebut = $configJSON['absence_heureDebut'] ?? Config::$absence_heureDebut ?? 8;
		$Config->absence_heureFin = $configJSON['absence_heureFin'] ?? Config::$absence_heureFin ?? 20;
		$Config->absence_pas = $configJSON['absence_pas'] ?? Config::$absence_pas ?? 0.5;
		$Config->absence_dureeSeance = $configJSON['absence_dureeSeance'] ?? Config::$absence_dureeSeance ?? 2;

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

/*******************************/
/* Methodes de config          */
/*******************************/
$accepted_input = [
	'message_non_publication_releve',
	'releve_PDF',
	'etudiant_modif_photo',
	'acces_enseignants',
	'cloisonner_enseignants',
	'analystics_interne',
	'analyse_temps_requetes',
	'nom_IUT',

	'doc_afficher_nip',
	'doc_afficher_id',
	'doc_afficher_date_naissance',

	'idReg',
	'idPlaceHolder',
	'idInfo',
	'nameReg',
	'namePlaceHolder',
	'nameInfo',

	'module_absences',
	'afficher_absences',
	'data_absences_scodoc',
	'metrique_absences',
	'autoriser_justificatifs',
	'liste_dep_ok_jusiticatifs',
	'message_rapport_absences',
	'message_justificatifs',
	'absence_heureDebut',
	'absence_heureFin',
	'absence_pas',
	'absence_dureeSeance'
];

$Config->getAllConfig = function() {
	global $Config;
	global $accepted_input;
	$output = [];
	foreach ($accepted_input as $key) {
		$output[$key] = ((array)$Config)[$key];
	}
	return $output;
};

$Config->getConfig = function() {
	global $Config;
	global $user;

	$output = ($Config->getAllConfig)();
	$output['session'] 	= $user->getId();
	$output['name' ]	= $user->getName();
	$output['statut' ]	= $user->getStatut();
	
	return $output;
};

$Config->setConfig = function($key, $value) {
	global $path;
	global $accepted_input;
	
	if(!in_array($key, $accepted_input)) {
		returnError("Option non modifiable");
	}

	$file = $path.'/config/config.json';

	$configJSON = [];

	if(file_exists($file)){
		$configJSON = json_decode(file_get_contents($file), true);
	}

	switch(true){
		case $value === 'true': $configJSON[$key] = true; break;
		case $value === 'false': $configJSON[$key] = false; break;
		case is_numeric($value): $configJSON[$key] = floatval($value); break;
		case $value === '': unset($configJSON[$key]); break;
		default: $configJSON[$key] = $value;
	}

	if(file_put_contents(
			$file, 
			json_encode($configJSON)
		) === false
	) {
		returnError("Fichier non enregistré - problème de droits ? - le dossier config doit appartenir à www-data.");
	}
};
