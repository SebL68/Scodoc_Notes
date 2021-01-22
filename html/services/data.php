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

	$authData = (object) authData();

/* Utilisateur qui n'est pas dans la composante : n'est pas autorisé. */
	if($authData->statut == INCONNU){ returnError("Ce site est réservé aux étudiants et personnels de l'IUT."); }

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
?>