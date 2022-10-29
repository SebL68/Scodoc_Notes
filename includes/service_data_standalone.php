<?php 

$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
include_once "$path/includes/default_config.php";

class Scodoc{

	/*******************************/
	/* getAllStudents()
		Liste de l'ensemble des étudiants courants

		Entrée :
			/

		Sortie :
			[
				["nip 1", "Nom Prénom 1"],
				["nip 2", "Nom Prénom 2"],
				etc.
			]
	*/
	/*******************************/
	public function getAllStudents(){

	}
	/*******************************/
	/* getDepartmentsList()
	Liste les départements de Scodoc

	Entrées : 
		/

	Sortie :
		[
			"MMI",
			"GEII",
			etc.
		]

	*******************************/
	public function getDepartmentsList(){

	}

	/*******************************/
	/* getStudentSemesters()
	Liste les identifiants semestres qu'un étudiant a suivi

	Entrée :
		'21800202' 	// numéro étudiant

	Sortie :
		[
			{
				titre: "BUT MMI",
				formsemestre_id: 319,	// code semestre Scodoc
				semestre_id: 3, 		// numéro du semestre
				annee_scolaire: 2022/2023
			}
		]

	*******************************/
	public function getStudentSemesters($nip){
		
	}

	/*******************************/
	/* getReportCards()
	Renvoie les notes d'un étudiants pour un semestre choisi

	Entrée :
			'semestre' : '833', 		// Semestre
			'nip' : '21800202', 		// numéro étudiant
			[format: json ou pdf]		// format de retour
		]

	Sortie :
		// JSON avec les données du relevé de notes.
		// ou PDF du relevé

	*******************************/
	public function getReportCards($semestre, $nip, $format = ''){

	}

	/*******************************/
	/* getDepartmentSemesters()
		Liste des semestres actif d'un département

		Entrée :
			$dep : [string] département - exemple : 'MMI'

		Sortie :
			[
				{
					'titre_court' => 'BUT MMI',
					'titre_long' => 'BUT Métiers du Multimédia et de l\'Internet',
					'num' => 2,			// Numéro du semestre
					'id' => 384			// Code semestre Scodoc
				},
				etc.
			]

	*******************************/
	public function getDepartmentSemesters($dep){

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
				'semestre_id': '132',
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
	public function getStudentsListsDepartement($dep){

	}

	/*******************************/
	/* getStudentDepartment()
	Récupère le département d'un étudiant à partir de son numéro d'étudiant

	Entrée :
		$nip : [string] numéro d'étudiant - exemple : "21600306"

	Sortie :
		"département" - exemple : "MMI"
	*/
	public function getStudentDepartment($nip){

	}

	/*******************************/
	/* getStudentsInSemester()
	Liste de tous les étudiants dans un semestre

	Entrées : 
		$dep : [string] département - exemple : MMI
		$sem : [string] code semestre Scodoc - exemple : 171

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
	public function getStudentsInSemester($sem){

	}

	/*******************************/
	/* modules()
	Liste les modules d'un département + semestre

	Entrées : 
		$sem : [string] code semestre Scodoc - exemple : 871

	Sortie :
		{
			modules: [
				{
					"titre": "nom du module 1",
					"code": "R101" 
				},
				etc.
			],
			[OPTIONNEL] saes: [
				{
					"titre": "nom du module 1",
					"code": "R101" 
				},
				etc.
			],
		},


	*******************************/
	public function modules($sem){

	}

}
