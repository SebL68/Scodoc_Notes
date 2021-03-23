<?php
/*****************************************/
/* Gère la communication des données entre
	le client (typiquement le navigateur) 
	et le serveur */

/*****************************************/

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
	include_once "$path/includes/absencesIO.php";
	include_once "$path/includes/adminIO.php";

	$authData = (object) authData();

/* Utilisateur qui n'est pas dans la composante : n'est pas autorisé. */
	if($authData->statut == INCONNU){ returnError("Ce site est réservé aux étudiants et personnels de l'IUT."); }

/******************************************
 * 
 * Fonctions de communication disponibles
 * 
 * 
	0	get donnéesAuthentification :
	Retourne les données de l'utilisateur : son identifiant et son statut (étudiant ou personnel)
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=donnéesAuthentification

	0	get listeEtudiants :
	Liste tous les étudiants du LDAP
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listeEtudiants

	0	get semestresDépartement : 
	Liste des semestres actifs d'un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=semestresDépartement&dep=MMI

	0	get listeEtudiantsSemestre : 
	Liste les étudiants d'un semestre
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listeEtudiantsSemestre&dep=MMI&semestre=SEM8871

	0	get listesEtudiantsDépartement : 
	Liste les étudiants d'un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listesEtudiantsDépartement&dep=MMI

	0	get semestresEtudiant :
	Liste les identifiants semestres qu'un étudiant a suivi
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=semestresEtudiant&etudiant=alexandre.aab@uha.fr

	0	get relevéEtudiant :
	Relevé de note de l'étudiant au format JSON
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=relevéEtudiant&semestre=SEM8871&etudiant=alexandre.aab@uha.fr
	
	0	get UEEtModules :
	Récupère les UE et les modules d'un semestre
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=UEEtModules&dep=MMI&semestre=SEM8871
	
	0	get listeDépartements :
	Récupère les UE et les modules d'un semestre
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listeDépartements

	0	get dataPremièreConnexion :
	Récupère les données d'authentification, les semestres et le premier relevé (évite de faire 3 requêtes)
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=dataPremièreConnexion

	0	set setAbsence :
	Change l'absence d'un étudiant
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=setAbsence&dep=MMI&semestre=SEM9743&matiere=M4101&etudiant=fares.abdelkrim@uha.fr&date=2021-01-30&creneau=18&statut=absent

	0	set getAbsence :
	Récupère les absences d'un étudiant ou des étudiants d'un semestre complet
	Ne pas transmettre le GET étudiant pour obtenir tout le semestre
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=getAbsence&dep=MMI&semestre=SEM9743&etudiant=fares.abdelkrim@uha.fr


	0	get listeVacataires :
	Récupère la liste des vacataires d'un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listeVacataires&dep=MMI

	0	set modifVacataire :
	Enregistre l'adresse mail d'un vacataire existant ou nouveau dans un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=modifVataire&dep=MMI&ancienMail=ancien.nom@uha.fr&nouveau.nom@uha.fr
			
	0	set supVacataire :
	Supprime l'adresse mail d'un vacataire existant dans un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=supVataire&dep=MMI&email=prenom.nom@uha.fr

*******************************/
	if(isset($_GET['q'])){
		switch($_GET['q']){

			case 'donnéesAuthentification':
				$output = (array) $authData;
				break;

			case 'listeEtudiants':
				// Uniquement pour les personnels IUT.
				if($authData->statut < PERSONNEL){ returnError(); }
				$output = getAllLDAPStudents();
				break;

			case 'semestresDépartement':
				$output = getDepartmentSemesters($_GET['dep']);	
				break;

			case 'listeEtudiantsSemestre':
				// Uniquement pour les personnels IUT.
				if($authData->statut < PERSONNEL){ returnError(); }
				$output = getStudentsInSemester($_GET['dep'], $_GET['semestre']);
				if(isset($_GET['absences']) && $_GET['absences'] == 'true'){
					$output['absences'] = getAbsence(
						$_GET['dep'],
						$_GET['semestre']
					);
				}
				break;
			case 'listesEtudiantsDépartement':
				// Uniquement pour les personnels IUT.
				if($authData->statut < PERSONNEL){ returnError(); }
				$output = getStudentsListsDepartement($_GET['dep']);
				break;

			case 'semestresEtudiant':
				// Uniquement les personnels IUT peuvent demander le relevé d'une autre personne.
				if($authData->statut < PERSONNEL && isset($_GET['etudiant'])){ returnError(); }
				// Si c'est un personnel, on transmet l'étudiant par get, sinon on prend l'identifiant de la session.
				$output = getStudentSemesters(['id' => $_GET['etudiant'] ?? $authData->session]);
				break;

			case 'relevéEtudiant':
				// Uniquement les personnels IUT peuvent demander le relevé d'une autre personne.
				if($authData->statut < PERSONNEL && isset($_GET['etudiant'])){ returnError(); } 
				// Si c'est un personnel, on transmet l'étudiant par get, sinon on prend l'identifiant de la session.
				$output = getReportCards([
					'semestre' => $_GET['semestre'], 
					'id' => $_GET['etudiant'] ?? $authData->session
				]);
				break;
			
			case 'UEEtModules':
				if($authData->statut < PERSONNEL ){ returnError(); }
				$output = UEAndModules($_GET['dep'], $_GET['semestre']);
				break;

			case 'listeDépartements':
				$output = getDepartmentsList();
				break;
			
			case 'dataPremièreConnexion':
				if($authData->statut == ETUDIANT){
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
				}else if($authData->statut >= PERSONNEL){
					$output = [
						'auth' => (array) $authData,
						'etudiants' => getAllLDAPStudents()
					];
				}
				break;

		/*************************/
			case 'setAbsence':
				if($authData->statut < PERSONNEL ){ returnError(); }
				setAbsence(
					$authData->session,
					$_GET['dep'],
					$_GET['semestre'],
					$_GET['matiere'],
					$_GET['etudiant'],
					$_GET['date'],
					$_GET['creneau'],
					$_GET['statut']
				);
				$output = [
					'result' => "OK"
				];
				break;

		/*************************/
			case 'getAbsence':
				if($authData->statut < PERSONNEL ){ returnError(); }
				$output = getAbsence(
					$_GET['dep'],
					$_GET['semestre'],
					$_GET['etudiant'] ?? ''
				);
				break;


		/*************************/
			case 'listeVacataires':
				if($authData->statut < ADMINISTRATEUR ){ returnError(); }
				$output = listeVacataires($_GET['dep']);
				break;

			case 'modifVacataire':
				if($authData->statut < ADMINISTRATEUR ){ returnError(); }
				$output = modifVacataire($_GET['dep'], $_GET['ancienMail'], $_GET['nouveauMail']);
				break;

			case 'supVacataire':
				if($authData->statut < ADMINISTRATEUR ){ returnError(); }
				$output = supVacataire($_GET['dep'], $_GET['email']);
				break;

		}	
		if($output !== ''){
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
?>