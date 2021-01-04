<?php
	ob_start("ob_gzhandler");
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Credentials: true');
	header('Content-type:application/json');

/* Debug */
	/*error_reporting(E_ALL);
	ini_set('display_errors', '1');*/

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/auth.php";
	include_once "$path/includes/LDAPData.php";
	include_once "$path/includes/serverIO.php"; // Fonctions de communication vers le serveur Scodoc

	$authData = (object) authData();

/* Utilisateur qui n'est pas dans la composante : n'est pas autorisé. */
	if($authData->statut == 'none'){ returnError("Ce site est réservé aux étudiants et personnels de l'IUT."); }

	if(isset($_GET['q'])){
		switch($_GET['q']){

			/*******************************
			0	get donnéesAuthentification :
					Retourne les données de l'utilisateur : son identifiant et son statut (étudiant ou personnel)
					Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=donnéesAuthentification

			0	get listeEtudiants :
					Liste tous les étudiants du LDAP
					Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listeEtudiants

			0	get semestresDépartement : 
					Liste des semestres actifs d'un département
					Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=semestresDépartement&dep=MMI

			0	get listesEtudiantsDépartement : 
					Liste les étudiants d'un département
					Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listesEtudiantsDépartement&dep=MMI

			0	get semestresEtudiant :
					Liste les identifiants semestres qu'un étudiant a suivi
					Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=semestresEtudiant&etudiant=alexandre.aab@uha.fr

			0	get relevéEtudiant :
					Relevé de note de l'étudiant au format JSON
					Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=relevéEtudiant&semestre=SEM8871&etudiant=alexandre.aab@uha.fr

			0	get dataPremièreConnexion :
					Récupère les données d'authentification, les semestres et le premier relevé (évite de faire 3 requêtes)
					Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=dataPremièreConnexion


			*******************************/
			case 'donnéesAuthentification':
				$output = (array) $authData;
				break;

			case 'listeEtudiants':
				// Uniquement pour les personnels IUT.
				if($authData->statut != 'personnel'){ returnError(); }
				$output = getAllLDAPStudents();
				break;

			case 'semestresDépartement':
				$output = getDepartmentSemesters($_GET['dep']);	
				break;

			case 'listesEtudiantsDépartement':
				// Uniquement pour les personnels IUT.
				if($authData->statut != 'personnel'){ returnError(); }
				$output = getStudentsListsDepartement($_GET['dep']);
				break;

			case 'semestresEtudiant':
				// Uniquement les personnels IUT peuvent demander le relevé d'une autre personne.
				if($authData->statut != 'personnel' && isset($_GET['etudiant'])){ returnError(); }
				// Si c'est un personnel, on transmet l'étudiant par get, sinon on prend l'identifiant de la session.
				$output = getStudentSemesters(['id' => $_GET['etudiant'] ?? $authData->session]);
				break;

			case 'relevéEtudiant':
				// Uniquement les personnels IUT peuvent demander le relevé d'une autre personne.
				if($authData->statut != 'personnel' && isset($_GET['etudiant'])){ returnError(); } 
				// Si c'est un personnel, on transmet l'étudiant par get, sinon on prend l'identifiant de la session.
				$output = getReportCards([
					'semestre' => $_GET['semestre'], 
					'id' => $_GET['etudiant'] ?? $authData->session
				]);
				break;

			case 'dataPremièreConnexion':
				if($authData->statut == 'etudiant'){
					if($authData->session == 'Compte_Demo.test@uha.fr'){
						include 'data_demo.php';
					} else {
						$nip = getStudentNumberFromMail($authData->session);
						$dep = getStudentDepartment($nip);
						$semestres = getStudentSemesters([
							'nip' => $nip, 
							'dep' => $dep
						]);
						$output = [
							'auth' => (array) $authData,
							'semestres' => $semestres,
							'relevé' => getReportCards([
								'semestre' => $semestres[0],
								'nip' => $nip, 
								'dep' => $dep
							])
						];
					}
				}else if($authData->statut == 'personnel'){
					$output = [
						'auth' => (array) $authData,
						'etudiants' => getAllLDAPStudents()
					];
				}
				break;

		}	
		if($output){
			echo json_encode($output/*, JSON_PRETTY_PRINT*/);
		}else{
			returnError('Mauvaise requête.');
		}
	}

	function returnError($msg = "Vous n'êtes pas un personnel habilité pour accéder à cette ressource."){
		exit(
			json_encode(
				array(
					'erreur' => $msg
				)
			)
		);
	}

/*******************************/
/* getDepartmentSemesters()
	Liste des semestres actif d'un département

	Entrée :
		$dep : [string] département - exemple : 'MMI'

	Sortie :
		[
			{
				'titre' => 'titre du semestre',
				'semestre_id' => 'code semestre' // exemple : 'SEM8871'
			},
			etc.
		]

*******************************/
	function getDepartmentSemesters($dep){
		$json = json_decode(
			Ask_Scodoc(
				'/Scolarite/Notes/formsemestre_list',
				$dep
			)
		);
		$output = [];
		foreach($json as $value){
			if($value->etat == "1"){
				$output[] = [
					'titre' => $value->titre_num,
					'semestre_id' => $value->formsemestre_id
				];
			}
		}
		return $output;
	}

/*******************************/
/* getStudentSemesters()
	Liste les identifiants semestres qu'un étudiant a suivi

	Entrée :
		['id' => $id] : [string] identifiant de l'étudiant - exemple : 'jean.dupont@uha.fr'
	ou
		[
			'nip' => '21800202', 	// numéro étudiant
			'dep' => 'MMI' 			// département de l'étudiant
		]

	Sortie :
		Tableau des codes semestres
		["SEM8871", "SEM8833", etc.]

*******************************/
	function getStudentSemesters($data){
		$data = (object) $data;
		if($data->id){
			$nip = getStudentNumberFromMail($data->id);
			$dep = getStudentDepartment($nip);
		}else{
			$nip = $data->nip;
			$dep = $data->dep;
		}
		
		$json = json_decode(
				Ask_Scodoc(
					'/Scolarite/Notes/etud_info',
					$dep,
					[
						'code_nip' => $nip,
						'format' => 'json'
					]
				)
			);
		if($json != ''){
			$output = [];
		
			for($i=0 ; $i<count($json->insemestre) ; $i++){
				$output[] = $json->insemestre[$i]->formsemestre_id;
			}
			return $output;
		}else{
			returnError(
				"Problème de compte, vous n'êtes pas dans Scodoc ou votre numéro d'étudiant est erroné, si le problème persiste, contactez votre responsable en lui précisant : il y a peut être un .0 à la fin du numéro d'étudiant dans Scodoc."
			);
		}
	}
/*******************************/
/* getReportCards()
	Renvoie les notes d'un étudiants pour un semestre choisi

	Entrée :
		[
			'semestre' => 'SEM8833', 		// Semestre
			'id' => 'jean.dupont@uha.fr', 	// Identifiant de l'étudiant
		]
	ou
		[
			'semestre' => 'SEM8833', 		// Semestre
			'nip' => '21800202', 			// numéro étudiant
			'dep' => 'MMI' 					// département de l'étudiant
		]

	Sortie :
		// JSON avec les données du relevé de notes.

*******************************/
	function getReportCards($data){
		$data = (object) $data;
		if($data->id){
			$nip = getStudentNumberFromMail($data->id);
			$dep = getStudentDepartment($nip);
		}else{
			$nip = $data->nip;
			$dep = $data->dep;
		}

		$output = json_decode(
			Ask_Scodoc(
				'/Scolarite/Notes/Notes/formsemestre_bulletinetud',
				$dep,
				[
					'formsemestre_id' => $data->semestre,
					'code_nip' => $nip,
					'format' => 'json',
					'version' => 'long'
				]
			)
		);

		if($output->rang){
			return $output;
		}else{
			returnError(
				"Relevé non disponible pour ce semestre, veuillez contacter votre responsable en lui précisant : vérifier si l'export des notes du semestre est autorisé dans Scodoc."
			);
		}
	}

/*******************************/
/* getStudentsListsDepartement()
	Liste les étudiants d'un département par semestre actif

	Entrée :
		$dep : [string] département - exemple : 'MMI'

	Sortie :
		[
			{
				'titre': 'Nom du semestre',
				'semestre_id': 'SEM8732',
				'groupes': ['groupe 1', 'groupe 2', etc.], // Exemple : TP11, TP12, etc.
				'etudiants': [
					{
						'nom': 'nom de l'étudiant',
						'prenom': 'prenom de l'étudiant',
						'groupe': 'groupe 1'
					},
					etc.
				]
			},
			etc. avec les autres semestres d'un département, exemple : 1er année, 2ième année...
		]
		
*******************************/
	function getStudentsListsDepartement($dep){
		$dataSEM = getDepartmentSemesters($dep);
		$output = [];
		foreach($dataSEM as $value){
			$value = (object) $value;
			$data_students = (object) getStudentsInSemester($dep, $value->semestre_id);
			$output[] = [
				'titre' => $value->titre,
				'semestre_id' => $value->semestre_id,
				'groupes' => $data_students->groupes,
				'etudiants' =>  $data_students->etudiants
			];
		}
		return $output;
	}

/*******************************/
/* getStudentDepartment()
	Récupère le département d'un étudiant à partir de son numéro d'étudiant

	Entrée :
		$nip : [string] numéro d'étudiant - exemple : "21600306"

	Sortie :
		"département" - exemple : "MMI"
*/
	function getStudentDepartment($nip){
		return Ask_Scodoc(
			'/get_etud_dept',
			'',
			[
				'code_nip' => $nip
			]
		);
	}

/*******************************/
/* getStudentsInSemester()
	Liste de tous les étudiants dans un semestre

	Entrées : 
		$dep : [string] département - exemple : MMI
		$sem : [string] code semestre Scodoc - exemple : SEM8871

	Sortie :
		{
			'groupes' => ['groupe 1', 'groupe2', etc.], 
			'etudiants' => [
				{
					'nom' => 'nom de l'étudiant',
					'prenom' => 'prenom de l'étudiant',
					'groupe' => 'groupe de l'étudiant',
					'num_etudiant' => 'numero de l'étudiant',
					'email' => 'email UHA de l'étudiant'
				},
				etc.
			]
		}

*******************************/
	function getStudentsInSemester($dep, $sem){
		global $path;
		$json = json_decode(
			Ask_Scodoc(
				'/Scolarite/Notes/Notes/groups_view',
				$dep,
				[
					'formsemestre_id' => $sem,
					'with_codes' => 1,
					'format' => 'json'
				]
			)
		);

		$groupes = [];
		$output_json = [];
		foreach($json as $value){
			$groupe = findTP($value);
			if(!in_array($groupe, $groupes)){
				$groupes[] = $groupe;
			}

			$output_json[] = [
				'nom' => $value->nom_disp,
				'prenom' => $value->prenom,
				'groupe' => $groupe,
				'num_etudiant' => $value->code_nip,
				'email' => getStudentMailFromNumber($value->code_nip)
				// 'num_ine' => $value->code_ine
				// 'email_perso' => $value->emailperso
			];
		}
		sort($groupes);
		return [
			'groupes' => $groupes, 
			'etudiants' => $output_json
		];
	}

	function findTP($json){
		// Recherche du groupe TP dans la key Pxxxx
		foreach($json as $key => $value){
			if($key[0] == "P"){
				return $json->$key;
			}
		};
	}
?>