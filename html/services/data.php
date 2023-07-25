<?php
/***********************************************************************************************/
/* Gère la communication des données entre le client (typiquement le navigateur) et le serveur */
/***********************************************************************************************/

	ob_start("ob_gzhandler");
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Headers: Authorization');
	header('Content-type:application/json');

/* Debug */
	/*error_reporting(E_ALL);
	ini_set('display_errors', '1');*/

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

	require_once "$path/includes/default_config.php";
	require_once "$path/includes/absences.class.php";
	require_once "$path/includes/admin.class.php";
	require_once "$path/includes/user.class.php";
	require_once "$path/includes/annuaire.class.php";
	require_once "$path/includes/".$Config->service_annuaire_class;	// Class Service_Annuaire
	require_once "$path/includes/".$Config->scheduler_class;		// Class Scheduler
	require_once "$path/includes/".$Config->service_data_class;		// Class service_data - typiquement Scodoc
	require_once "$path/includes/analytics.class.php";

	$user = new User();

/*******************************/
/* Mise en maintenance du site */
/*******************************/
	//if($user->getId() != 'sebastien.lehmann@uha.fr') returnError('Site en cours de maintenance ...');

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
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listeEtudiantsSemestre&dep=MMI&semestre=871

	0	get listesEtudiantsDépartement : 
	Liste les étudiants d'un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listesEtudiantsDépartement&dep=MMI

	0	get semestresEtudiant :
	Liste les identifiants semestres qu'un étudiant a suivi
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=semestresEtudiant&etudiant=123456

	0	get relevéEtudiant :
	Relevé de note de l'étudiant au format JSON
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=relevéEtudiant&semestre=SEM8871&etudiant=alexandre.aab@uha.fr
	
	0	get modules :
	Récupère les modules d'un semestre
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=modules&semestre=871
	
	0	get listeDépartements :
	Récupère les UE et les modules d'un semestre
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listeDépartements

	0	get dataPremièreConnexion :
	Récupère les données d'authentification, les semestres et le premier relevé (évite de faire 3 requêtes)
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=dataPremièreConnexion

	0	set setAbsence :
	Change l'absence d'un étudiant
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=setAbsence&dep=MMI&semestre=743&matiere=M4101&etudiant=fares.abdelkrim@uha.fr&date=2021-01-30&creneau=18&statut=absent

	0	set getAbsence :
	Récupère les absences d'un étudiant ou des étudiants d'un semestre complet
	Ne pas transmettre le GET étudiant pour obtenir tout le semestre
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=getAbsence&dep=MMI&semestre=743&etudiant=fares.abdelkrim@uha.fr


	0	get listeVacataires :
	Récupère la liste des vacataires d'un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listeVacataires&dep=MMI

	0	set modifVacataire :
	Enregistre l'id et le nom d'un vacataire existant ou nouveau dans un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=modifVacataire&dep=MMI&ancienId=ancien.nom@uha.fr&nouveauId=nouveau.nom@uha.fr&nouveauName=NomNouveau
			
	0	set supVacataire :
	Supprime l'enregistrement (id et name) d'un vacataire existant dans un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=supVacataire&dep=MMI&id=prenom.nom@uha.fr


	0	get listeAdministrateurs :
	Récupère la liste des administrateurs d'un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=listeAdministrateurs&dep=MMI

	0	set modifAdministrateur :
	Enregistre l'id et le nom d'un administrateur existant ou nouveau dans un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=modifAdministrateur&dep=MMI&ancienId=ancien.nom@uha.fr&nouveauId=nouveau.nom@uha.fr&nouveauName=NomNouveau
			
	0	set supAdministrateur :
	Supprime l'enregistrement (id et name) d'un administrateur existant dans un département
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=supAdministrateur&dep=MMI&id=prenom.nom@uha.fr

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
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=getStudentPic&nip=20222123

	0	set deleteStudentPic :
	Supprime la photo d'un étudiant
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=deleteStudentPic

*******************************/

	if(isset($_GET['q'])){

		sanitize($_GET['q']);

		switch($_GET['q']){

			case 'donnéesAuthentification':
				$output = [
					'session' => $user->getId(),
					'name' => $user->getName(),
					'statut' => $user->getStatut(),
					'config' => ($Config->getConfig)()
				];
				break;

			case 'getStatut':
				if(!isset($_GET['user']) || $_GET['user'] == ""){ break; }
				$output = [
					'statut' => Annuaire::statut($_GET['user'], true)
				];
				break;

			case 'listeEtudiants':
				// Uniquement pour les personnels IUT.
				if($user->getStatut() < PERSONNEL){ returnError(); }
				$Scodoc = new Scodoc();
				$output = $Scodoc->getAllStudents();
				break;

			case 'semestresDépartement':
				$Scodoc = new Scodoc();
				sanitize($_GET['dep']);
				$output = $Scodoc->getDepartmentSemesters($_GET['dep']);
				break;

			case 'listeEtudiantsSemestre':
				// Uniquement pour les personnels IUT.
				if($user->getStatut() < PERSONNEL){ returnError(); }
				sanitize($_GET['semestre']);
				$Scodoc = new Scodoc();
				$output = $Scodoc->getStudentsInSemester(
					$_GET['semestre']
				); 
				if(isset($_GET['absences']) && $_GET['absences'] == 'true'){
					$output['absences'] = Absences::getAbsence(
						$_GET['semestre']
					);
				}
				break;
			case 'listesEtudiantsDépartement':
				// Uniquement pour les personnels IUT.
				if($user->getStatut() < PERSONNEL){ returnError(); }
				sanitize($_GET['dep']);
				$Scodoc = new Scodoc();
				$output = $Scodoc->getStudentsListsDepartement($_GET['dep']);
				break;

			case 'semestresEtudiant':
				// Uniquement les personnels IUT peuvent demander le relevé d'une autre personne.
				if($user->getStatut() < PERSONNEL && isset($_GET['etudiant'])){ returnError(); }
				// Si c'est un personnel, on transmet l'étudiant par get, sinon on prend l'identifiant de la session.
				sanitize($_GET['etudiant'] ?? '');
				$Scodoc = new Scodoc();
				$nip = $_GET['etudiant'] ?? $user->getId();
				$output = $Scodoc->getStudentSemesters($nip);
				break;

			case 'relevéEtudiant':
				// Uniquement les personnels IUT peuvent demander le relevé d'une autre personne.
				if($user->getStatut() < PERSONNEL && isset($_GET['etudiant'])){ returnError(); } 
				// Si c'est un personnel, on transmet l'étudiant par get, sinon on prend l'identifiant de la session.
				sanitize($_GET['etudiant'] ?? '');
				sanitize($_GET['semestre']);
				$Scodoc = new Scodoc();
				$nip = $_GET['etudiant'] ?? $user->getId();
				$output = [
					'relevé' => $Scodoc->getReportCards($_GET['semestre'], $nip),
					'absences' => Absences::getAbsence(
						$_GET['semestre'],
						$nip
					) ?? []
				];
				break;
			
			case 'modules':
				if($user->getStatut() < PERSONNEL ){ returnError(); }
				sanitize($_GET['semestre']);
				$Scodoc = new Scodoc();
				$output = $Scodoc->modules($_GET['semestre']);
				break;

			case 'listeDépartements':
				$Scodoc = new Scodoc();
				$output = $Scodoc->getDepartmentsList();
				break;
			
			case 'dataPremièreConnexion':
				$Scodoc = new Scodoc();
				if($user->getStatut() == ETUDIANT){
					$nip = $user->getId();
					$semestres = $Scodoc->getStudentSemesters($nip);
					$output = [
						'config' => ($Config->getConfig)(),
						'auth' => [
							'session' => $nip,
							'name' => $user->getName(),
							'statut' => $user->getStatut()
						],
						'semestres' => $semestres,
						'relevé' => $Scodoc->getReportCards(end($semestres)['formsemestre_id'], $nip),
						'absences' => Absences::getAbsence(
							end($semestres)['formsemestre_id'],
							$nip
						) ?? []
					];
				}else if($user->getStatut() >= PERSONNEL){
					$output = [
						'config' => ($Config->getConfig)(),
						'auth' => [
							'session' => $user->getId(),
							'name' => $user->getName(),
							'statut' => $user->getStatut()
						],
						'etudiants' => $Scodoc->getAllStudents()
					];
				}
				break;

		/*************************/
			case 'setAbsence':
				if($user->getStatut() < PERSONNEL ){ returnError(); }
				if($user->getStatut() < ADMINISTRATEUR && $_GET['statut'] == 'justifie' ){ returnError(); }
				
				sanitize($_GET['semestre']);
				//sanitize($_GET['matiere']);
				//sanitize($_GET['matiereComplet']);
				sanitize($_GET['etudiant']);
				sanitize($_GET['date']);
				sanitize($_GET['debut']);
				sanitize($_GET['fin']);
				sanitize($_GET['statut']);

				if($_GET['etudiant'] == null){
					returnError('Un problème est survenu : étudiant non défini.');
				}
				if($_GET['debut'] == 0){
					returnError('Un problème est survenu : créneau à 0 - pourriez-vous envoyer un mail à sebastien.lehmann@uha.fr pour expliquer comment cette erreur s\'est produite ?');
				}

				$output = Absences::setAbsence(
					$user->getId(),
					$_GET['semestre'],
					$_GET['matiere'],
					$_GET['matiereComplet'],
					$_GET['etudiant'],
					$_GET['date'],
					$_GET['debut'],
					$_GET['fin'],
					$_GET['statut']
				);
				break;

			case 'getAbsence':
				if($user->getStatut() < PERSONNEL ){ returnError(); }
				sanitize($_GET['semestre']);
				sanitize($_GET['etudiant']);
				$output = Absences::getAbsence(
					$_GET['semestre'],
					$_GET['etudiant'] ?? ''
				);
				break;

			case 'setJustifie':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				sanitize($_GET['semestre']);
				sanitize($_GET['etudiant']);
				sanitize($_GET['date']);
				sanitize($_GET['debut']);
				sanitize($_GET['justifie']);
				$output = Absences::setJustifie(
					$_GET['semestre'],
					$_GET['etudiant'],
					$_GET['date'],
					$_GET['debut'],
					$_GET['justifie']
				);
				break;

		/*************************/
			case 'listeVacataires':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				sanitize($_GET['dep']);
				$output = Admin::listeUtilisateurs($_GET['dep'], 'vacataires');
				break;

			case 'modifVacataire':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				sanitize($_GET['dep']);
				sanitize($_GET['ancienId']);
				sanitize($_GET['nouveauId']);
				sanitize($_GET['nouveauName']);
				$output = Admin::modifUtilisateur(
					$_GET['dep'], 
					'vacataires',
					$_GET['ancienId'], 
					$_GET['nouveauId'],
					$_GET['nouveauName']
				);
				break;

			case 'supVacataire':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				sanitize($_GET['dep']);
				sanitize($_GET['id']);
				$output = Admin::supUtilisateur(
					$_GET['dep'],
					'vacataires',
					$_GET['id']
				);
				break;

		/*************************/
			case 'listeAdministrateurs':
				if($user->getStatut() < PERSONNEL ){ returnError(); }
				sanitize($_GET['dep']);
				$output = Admin::listeUtilisateurs($_GET['dep'], 'administrateurs');
				break;

			case 'modifAdministrateur':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				sanitize($_GET['dep']);
				sanitize($_GET['ancienId']);
				sanitize($_GET['nouveauId']);
				sanitize($_GET['nouveauName']);
				$output = Admin::modifUtilisateur(
					$_GET['dep'],
					'administrateurs',
					$_GET['ancienId'], 
					$_GET['nouveauId'],
					$_GET['nouveauName']
				);
				break;

			case 'supAdministrateur':
				if($user->getStatut() < ADMINISTRATEUR ){ returnError(); }
				sanitize($_GET['dep']);
				sanitize($_GET['id']);
				$output = Admin::supUtilisateur(
					$_GET['dep'], 
					'administrateurs',
					$_GET['id']
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

			case 'getAllConfig':
				if($user->getStatut() < SUPERADMINISTRATEUR ){ returnError(); }
				$output = ($Config->getAllConfig)();
				break;

			case 'setConfig':
				if($user->getStatut() < SUPERADMINISTRATEUR ){ returnError(); }
				($Config->setConfig)($_GET['key'], $_GET['value']);
				$output = [
					'resultat' => 'ok'
				];
				break;

		/************************/
		/* Gestion des photos	*/
		/************************/
			case 'setStudentPic':
				if($user->getStatut() < ETUDIANT){ returnError(); }
				if(!move_uploaded_file($_FILES['image']['tmp_name'], "$path/data/studentsPic/".$user->getId().'.jpg')){
					$output = [
						'result' => "Not ok"
					];
				}else{
					$output = [
						'result' => "OK"
					];
				}
				break;

			case 'getStudentPic':
				if ($user->getStatut() > ETUDIANT && isset($_GET['nip'])) {
					sanitize($_GET['nip']);
					$url = "$path/data/studentsPic/" . $_GET['nip'] . ".jpg";
				} else {
					$url = "$path/data/studentsPic/" . $user->getId() . '.jpg';
				}

				if(!file_exists($url)){ // Image par défaut si elle n'existe pas
					if(method_exists('Config', 'customPic') == true){
						sanitize($_GET['nip']);
                        Config::customPic($_GET['nip']);
                        return;
                    } else {
						header('Content-type:image/svg+xml');
						echo '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#0b0b0b" style="background: #fafafa" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
						return;	
					}
					
				} else {
					header('Content-type:image/jpeg');
					echo file_get_contents($url);
					return;
				}
				break;

			case 'deleteStudentPic':
				unlink("$path/data/studentsPic/" . $user->getId() . '.jpg');
				$output = [
					'result' => "OK"
				];
				break;

		/************************/
		/* Analytics interne	*/
		/************************/
			case 'getAnalyticsData':
				$output = Analytics::getData();
				break;
		}	
		
/*************************************************/

		if($output != ''){
			if(method_exists('Config', 'customOutput') == true){
				$output = Config::customOutput($output);
			}
			echo json_encode($output/*, JSON_PRETTY_PRINT*/);
		}else{
			returnError('Mauvaise requête.');
		}
	}

	function sanitize($data, $regex = '/\.\.|\\|\//'){
		/* Nettoyage des entrées */
		if(preg_match($regex, $data)){
			returnError('Données envoyées au serveur non valides - try to hack ?!');
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