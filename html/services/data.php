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

	include_once "$path/includes/config.php";
	include_once "$path/includes/auth.php";

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
	Met les liste des utilisateurs à jour à partir du serveur LDAP
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=updateLists

	0	set setCron :
	Configure CRON pour la mise à jour automatique des listes d'utilisateurs à partir du serveur LDAP
			Exemple : https://notes.iutmulhouse.uha.fr/services/data.php?q=setCron
*******************************/
	if(isset($_GET['q'])){
		switch($_GET['q']){

			case 'donnéesAuthentification':
				$output = (array) $authData;
				break;

			case 'listeEtudiants':
				// Uniquement pour les personnels IUT.
				if($authData->statut < PERSONNEL){ returnError(); }
				include_once "$path/includes/LDAPData.php";
				$output = getAllLDAPStudents();							// includes/LDAPData.php
				break;

			case 'semestresDépartement':
				include_once "$path/includes/serverIO.php";
				$output = getDepartmentSemesters($_GET['dep']);			// includes/serverIO.php
				break;

			case 'listeEtudiantsSemestre':
				// Uniquement pour les personnels IUT.
				if($authData->statut < PERSONNEL){ returnError(); }
				include_once "$path/includes/serverIO.php";
				$output = getStudentsInSemester(						// includes/serverIO.php
					$_GET['dep'], 
					$_GET['semestre']
				); 
				if(isset($_GET['absences']) && $_GET['absences'] == 'true'){
					include_once "$path/includes/absencesIO.php";
					$output['absences'] = getAbsence(					// includes/absencesIO.php
						$_GET['dep'],
						$_GET['semestre']
					);
				}
				break;
			case 'listesEtudiantsDépartement':
				// Uniquement pour les personnels IUT.
				if($authData->statut < PERSONNEL){ returnError(); }
				include_once "$path/includes/serverIO.php";
				$output = getStudentsListsDepartement($_GET['dep']);	// includes/serverIO.php
				break;

			case 'semestresEtudiant':
				// Uniquement les personnels IUT peuvent demander le relevé d'une autre personne.
				if($authData->statut < PERSONNEL && isset($_GET['etudiant'])){ returnError(); }
				// Si c'est un personnel, on transmet l'étudiant par get, sinon on prend l'identifiant de la session.
				include_once "$path/includes/serverIO.php";
				$output = getStudentSemesters(							// includes/serverIO.php
					['id' => $_GET['etudiant'] ?? $authData->session]
				);	
				break;

			case 'relevéEtudiant':
				// Uniquement les personnels IUT peuvent demander le relevé d'une autre personne.
				if($authData->statut < PERSONNEL && isset($_GET['etudiant'])){ returnError(); } 
				// Si c'est un personnel, on transmet l'étudiant par get, sinon on prend l'identifiant de la session.
				include_once "$path/includes/serverIO.php";
				$output = getReportCards([								// includes/serverIO.php
					'semestre' => $_GET['semestre'], 
					'id' => $_GET['etudiant'] ?? $authData->session
				]);
				break;
			
			case 'UEEtModules':
				if($authData->statut < PERSONNEL ){ returnError(); }
				include_once "$path/includes/serverIO.php";
				$output = UEAndModules($_GET['dep'], $_GET['semestre']);// includes/serverIO.php
				break;

			case 'listeDépartements':
				include_once "$path/includes/serverIO.php";
				$output = getDepartmentsList();							// includes/serverIO.php
				break;
			
			case 'dataPremièreConnexion':
				if($authData->statut == ETUDIANT){
					if($authData->session == 'Compte_Demo.test@uha.fr'){
						include 'data_demo.php';
					} else {
						include_once "$path/includes/LDAPData.php";
						include_once "$path/includes/serverIO.php";
						$nip = getStudentNumberFromMail($authData->session);// includes/LDAPData.php
						$dep = getStudentDepartment($nip);				// includes/serverIO.php
						$semestres = getStudentSemesters([				// includes/serverIO.php
							'nip' => $nip, 
							'dep' => $dep
						]);
						$output = [
							'auth' => (array) $authData,
							'semestres' => $semestres,
							'relevé' => getReportCards([				// includes/serverIO.php
								'semestre' => $semestres[0],
								'nip' => $nip, 
								'dep' => $dep
							])
						];
					}
				}else if($authData->statut >= PERSONNEL){
					include_once "$path/includes/LDAPData.php";
					$output = [
						'auth' => (array) $authData,
						'etudiants' => getAllLDAPStudents()				// includes/LDAPData.php
					];
				}
				break;

		/*************************/
			case 'setAbsence':
				if($authData->statut < PERSONNEL ){ returnError(); }
				include_once "$path/includes/absencesIO.php";
				setAbsence(												// includes/absencesIO.php
					$authData->session,
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
				if($authData->statut < PERSONNEL ){ returnError(); }
				include_once "$path/includes/absencesIO.php";
				$output = getAbsence(									// includes/absencesIO.php
					$_GET['dep'],
					$_GET['semestre'],
					$_GET['etudiant'] ?? ''
				);
				break;

		/*************************/
			case 'listeVacataires':
				if($authData->statut < ADMINISTRATEUR ){ returnError(); }
				include_once "$path/includes/adminIO.php";
				$output = listeVacataires($_GET['dep']);				// includes/adminIO.php
				break;

			case 'modifVacataire':
				if($authData->statut < ADMINISTRATEUR ){ returnError(); }
				include_once "$path/includes/adminIO.php";
				$output = modifVacataire(								// includes/adminIO.php
					$_GET['dep'], 
					$_GET['ancienMail'], 
					$_GET['nouveauMail']
				);
				break;

			case 'supVacataire':
				if($authData->statut < ADMINISTRATEUR ){ returnError(); }
				include_once "$path/includes/adminIO.php";
				$output = supVacataire(									// includes/adminIO.php
					$_GET['dep'], 
					$_GET['email']
				);
				break;

		/*************************/
			case 'listeAdministrateurs':
				if($authData->statut < ADMINISTRATEUR ){ returnError(); }
				include_once "$path/includes/adminIO.php";
				$output = listeAdministrateurs($_GET['dep']);			// includes/adminIO.php
				break;

			case 'modifAdministrateur':
				if($authData->statut < ADMINISTRATEUR ){ returnError(); }
				include_once "$path/includes/adminIO.php";
				$output = modifAdministrateur(							// includes/adminIO.php
					$_GET['dep'], 
					$_GET['ancienMail'], 
					$_GET['nouveauMail']
				);
				break;

			case 'supAdministrateur':
				if($authData->statut < ADMINISTRATEUR ){ returnError(); }
				include_once "$path/includes/adminIO.php";
				$output = supAdministrateur(							// includes/adminIO.php
					$_GET['dep'], 
					$_GET['email']
				);
				break;

		/*************************/
			case 'updateLists':
				if($authData->statut < SUPERADMINISTRATEUR ){ returnError(); }
				$output = updateLists();								// includes/LDAPIO.php
				break;

			case 'setCron':
				if($authData->statut < SUPERADMINISTRATEUR ){ returnError(); }
				$output = setCron();									// includes/LDAPIO.php
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