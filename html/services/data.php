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

	include_once "$path/config/config.php";
	include_once "$path/includes/absences.class.php";
	include_once "$path/includes/admin.class.php";
	include_once "$path/includes/user.class.php";
	include_once "$path/includes/annuaire.class.php";
	include_once "$path/includes/".Config::$service_annuaire_class;	// Class Service_Annuaire
	include_once "$path/includes/".Config::$scheduler_class;				// Class Scheduler
	$user = new User();

/* Utilisateur qui n'est pas dans la composante : n'est pas autorisé. */
	if($user->getStatut() == INCONNU){ returnError("Ce site est réservé aux étudiants et personnels de l'IUT."); }

/******************************************
 * 
 * Fonctions de communication disponibles
 * 
 * 
	0	get donnéesAuthentification :
	Retourne les données de l'utilisateur : son identifiant et son statut (étudiant ou personnel)
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=donnéesAuthentification

	0	get listeEtudiants :
	Liste tous les étudiants de l'annuaire
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
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=modifVacataire&dep=MMI&ancienMail=ancien.nom@uha.fr&nouveau.nom@uha.fr
			
	0	set supVacataire :
	Supprime l'adresse mail d'un vacataire existant dans un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=supVacataire&dep=MMI&email=prenom.nom@uha.fr


	0	get listeAdministrateurs :
	Récupère la liste des administrateurs d'un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listeAdministrateurs&dep=MMI

	0	set modifAdministrateur :
	Enregistre l'adresse mail d'un administrateur existant ou nouveau dans un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=modifAdministrateur&dep=MMI&ancienMail=ancien.nom@uha.fr&nouveau.nom@uha.fr
			
	0	set supAdministrateur :
	Supprime l'adresse mail d'un administrateur existant dans un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=supAdministrateur&dep=MMI&email=prenom.nom@uha.fr

	0	set updateLists :
	Met les listes des utilisateurs à jour à partir du serveur d'annuaire
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=updateLists

	0	set setUpdateLists :
	Configure le gestionnaire de tâches pour la mise à jour automatique des listes d'utilisateurs à partir du serveur d'annuaires
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=setUpdateLists

	0	set setStudentPic :
	Enregistre la photo d'un étudiant - les données sont transmise comme un fichier
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=setStudentPic

	0	get getStudentPic :
	Récupère la photo d'un étudiant - sortie sous la forme d'une image
	Un personnel ou plus peut récupéréer la photo de n'importe quel étudiant en paramètre GET
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=getStudentPic&email=sebastien.lehmann@uha.fr

	0	set deleteStudentPic :
	Supprime la photo d'un étudiant
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=deleteStudentPic

*******************************/

	if(isset($_GET['q'])){
		switch($_GET['q']){

			case 'donnéesAuthentification':
				$output = [
					'session' => $user->getSessionName(),
					'statut' => $user->getStatut()
				];
				break;

			case 'listeEtudiants':
				// Uniquement pour les personnels IUT.
				if($user->getStatut() < PERSONNEL){ returnError(); }
				include_once "$path/includes/annuaire.class.php";
				$output = Annuaire::getAllStudents();
				break;

			case 'semestresDépartement':
				include_once "$path/includes/serverIO.php";
				$output = getDepartmentSemesters($_GET['dep']);			// includes/serverIO.php
				break;

			case 'listeEtudiantsSemestre':
				// Uniquement pour les personnels IUT.
				if($user->getStatut() < PERSONNEL){ returnError(); }
				include_once "$path/includes/serverIO.php";
				$output = getStudentsInSemester(						// includes/serverIO.php
					$_GET['dep'], 
					$_GET['semestre']
				); 
				if(isset($_GET['absences']) && $_GET['absences'] == 'true'){
					$output['absences'] = Absences::getAbsence(
						$_GET['dep'],
						$_GET['semestre']
					);
				}
				break;
			case 'listesEtudiantsDépartement':
				// Uniquement pour les personnels IUT.
				if($user->getStatut() < PERSONNEL){ returnError(); }
				include_once "$path/includes/serverIO.php";
				$output = getStudentsListsDepartement($_GET['dep']);	// includes/serverIO.php
				break;

			case 'semestresEtudiant':
				// Uniquement les personnels IUT peuvent demander le relevé d'une autre personne.
				if($user->getStatut() < PERSONNEL && isset($_GET['etudiant'])){ returnError(); }
				// Si c'est un personnel, on transmet l'étudiant par get, sinon on prend l'identifiant de la session.
				include_once "$path/includes/serverIO.php";
				$output = getStudentSemesters(							// includes/serverIO.php
					['id' => $_GET['etudiant'] ?? $user->getSessionName()]
				);	
				break;

			case 'relevéEtudiant':
				// Uniquement les personnels IUT peuvent demander le relevé d'une autre personne.
				if($user->getStatut() < PERSONNEL && isset($_GET['etudiant'])){ returnError(); } 
				// Si c'est un personnel, on transmet l'étudiant par get, sinon on prend l'identifiant de la session.
				include_once "$path/includes/annuaire.class.php";
				include_once "$path/includes/serverIO.php";
				$nip = Annuaire::getStudentNumberFromMail($_GET['etudiant'] ?? $user->getSessionName());
				$dep = getStudentDepartment($nip);						// includes/serverIO.php
				$output = [
					'relevé' => getReportCards([						// includes/serverIO.php
						'semestre' => $_GET['semestre'], 
						'nip' => $nip, 
						'dep' => $dep
					]),
					'absences' => Absences::getAbsence(
						$dep,
						$_GET['semestre'],
						$_GET['etudiant'] ?? $user->getSessionName()
					) ?? []
				];
				break;
			
			case 'UEEtModules':
				if($user->getStatut() < PERSONNEL ){ returnError(); }
				include_once "$path/includes/serverIO.php";
				$output = UEAndModules($_GET['dep'], $_GET['semestre']);// includes/serverIO.php
				break;

			case 'listeDépartements':
				include_once "$path/includes/serverIO.php";
				$output = getDepartmentsList();							// includes/serverIO.php
				break;
			
			case 'dataPremièreConnexion':
				if($user->getStatut() == ETUDIANT){
					if($user->getStatut() == 'Compte_Demo.test@uha.fr'){
						include 'data_demo.php';
					} else {
						include_once "$path/includes/annuaire.class.php";
						include_once "$path/includes/serverIO.php";
						$nip = Annuaire::getStudentNumberFromMail($user->getStatut());
						$dep = getStudentDepartment($nip);				// includes/serverIO.php
						$semestres = getStudentSemesters([				// includes/serverIO.php
							'nip' => $nip, 
							'dep' => $dep
						]);
						$output = [
							'auth' => [
								'session' => $user->getSessionName(),
								'statut' => $user->getStatut()
							],
							'semestres' => $semestres,
							'relevé' => getReportCards([				// includes/serverIO.php
								'semestre' => $semestres[0],
								'nip' => $nip, 
								'dep' => $dep
							]),
							'absences' => Absences::getAbsence(
								$dep,
								$semestres[0],
								$user->getStatut()
							) ?? []
						];
					}
				}else if($user->getStatut() >= PERSONNEL){
					include_once "$path/includes/annuaire.class.php";
					$output = [
						'auth' => [
							'session' => $user->getSessionName(),
							'statut' => $user->getStatut()
						],
						'etudiants' => Annuaire::getAllStudents()
					];
				}
				break;

		/*************************/
			case 'setAbsence':
				if($user->getStatut() < PERSONNEL ){ returnError(); }
				Absences::setAbsence(
					$user->getSessionName(),
					$_GET['dep'],
					$_GET['semestre'],
					$_GET['matiere'],
					$_GET['matiereComplet'],
					$_GET['UE'],
					$_GET['etudiant'],
					$_GET['date'],
					$_GET['creneau'],
					$_GET['creneauxIndex'],
					$_GET['statut']
				);
				$output = [
					'result' => "OK"
				];
				break;

		/*************************/
			case 'getAbsence':
				if($user->getStatut() < PERSONNEL ){ returnError(); }
				$output = Absences::getAbsence(
					$_GET['dep'],
					$_GET['semestre'],
					$_GET['etudiant'] ?? ''
				);
				break;

		/*************************/
			case 'listeVacataires':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				$output = Admin::listeUtilisateurs($_GET['dep'], 'vacataires');
				break;

			case 'modifVacataire':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				$output = Admin::modifUtilisateur(
					$_GET['dep'], 
					'vacataires',
					$_GET['ancienMail'], 
					$_GET['nouveauMail']
				);
				break;

			case 'supVacataire':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				$output = Admin::supUtilisateur(
					$_GET['dep'],
					'vacataires',
					$_GET['email']
				);
				break;

		/*************************/
			case 'listeAdministrateurs':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				$output = Admin::listeUtilisateurs($_GET['dep'], 'administrateurs');
				break;

			case 'modifAdministrateur':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				$output = Admin::modifUtilisateur(
					$_GET['dep'],
					'administrateurs',
					$_GET['ancienMail'], 
					$_GET['nouveauMail']
				);
				break;

			case 'supAdministrateur':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				$output = Admin::supUtilisateur(
					$_GET['dep'], 
					'administrateurs',
					$_GET['email']
				);
				break;

		/*************************/
			case 'updateLists':
				if($user->getStatut() < SUPERADMINISTRATEUR ){ returnError(); }
				$output = Service_Annuaire::updateLists();
				break;

			case 'setUpdateLists':
				if($user->getStatut() < SUPERADMINISTRATEUR ){ returnError(); }
				$output = Scheduler::setUpdateLists();
				break;

		/************************/
		/* Gestion des photos	*/
		/************************/
			case 'setStudentPic':
				if($user->getStatut() < ETUDIANT){ returnError(); }
				move_uploaded_file($_FILES['image']['tmp_name'], "$path/studentsPic/$user->getStatut().jpg");
				chmod("$path/studentsPic/$user->getStatut().jpg", 0664);
				$output = [
					'result' => "OK"
				];
				break;

			case 'getStudentPic':
				if ($user->getStatut() > ETUDIANT && isset($_GET['email'])) {
					$url = "$path/data/studentsPic/" . $_GET['email'] . ".jpg";
				} else {
					$url = "$path/data/studentsPic/" . $user->getStatut() . '.jpg';
				}
				if(!file_exists($url)){ // Image par défaut si elle n'existe pas
					header('Content-type:image/svg+xml');
					echo '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#0b0b0b" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
					return;
				} else {
					header('Content-type:image/jpeg');
					echo file_get_contents($url);
					return;
				}
				break;

			case 'deleteStudentPic':
				unlink("$path/data/studentsPic/" . $user->getStatut() . '.jpg');
				$output = [
					'result' => "OK"
				];
				break;
		}	
		if($output != ''){
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