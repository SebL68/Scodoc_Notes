<?php 

$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
include_once "$path/includes/default_config.php";

class Scodoc{
	private $ch; // Connexion CURL

	/***********************************************************/
	/* Initialisation de la connexion et récupération du token */
	/***********************************************************/
	public function __construct(){
		global $Config;
		$this->ch = curl_init();
		//$Config->scodoc_url = 'http://192.168.43.67:5000/ScoDoc';
		/* Configuration pour récupérer le token */ 
		$options = array(
			CURLOPT_FORBID_REUSE => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_POST => true,
			CURLOPT_URL => $Config->scodoc_url.'/api/tokens',
			CURLOPT_USERPWD => $Config->scodoc_login . ':' . $Config->scodoc_psw,
			CURLOPT_REFERER => $_SERVER['SERVER_NAME'] . '/?passerelle=' . $Config->passerelle_version
		);
		curl_setopt_array($this->ch, $options);
		$token = json_decode(curl_exec($this->ch), false)->token;

		/* Token récupéré, changement de la configuration pour les autres requêtes */
		$headers = array(
			"Authorization: Bearer $token"
		);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->ch, CURLOPT_USERPWD, NULL);
		curl_setopt($this->ch, CURLOPT_POST, false);
	}

	/************************/
	/* Accès à l'API Scodoc */
	/************************/
	private function Ask_Scodoc($url_query, $options = []){
		global $Config;
		$data = http_build_query($options);

		curl_setopt($this->ch, CURLOPT_URL, $Config->scodoc_url . "/api/$url_query?$data");
		return curl_exec($this->ch);
	}

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
		$json = json_decode($this->Ask_Scodoc('etudiants/courants'));
		forEach($json as $value){
			$output[] = [
				$value->code_nip,
				$value->nom . ' ' . ucfirst(mb_strtolower($value->prenom))
			];
		}
		return $output;
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
		$data = json_decode($this->Ask_Scodoc('departements'));
	
		if($data != ''){
			$output = [];
			forEach($data as $value){
				$output[] = $value->acronym;
			}
			
			return $output;
		}else{
			returnError(
				"Impossible de récupérer la liste des départements, vérifiez que Scodoc fonctionne."
			);
		}	
	}

	/*******************************/
	/* getStudentSemesters()
	Liste les identifiants semestres qu'un étudiant a suivi

	Entrée :
		'21800202' 	// numéro étudiant

	Sortie :
		[
			{
				formsemestre_id: 319,	// code semestre Scodoc
				semestre_id: 3, 		// numéro du semestre
				date_debut: "26/08/2021",
				date_fin: "17/01/2022"
			}
		]

	*******************************/
	public function getStudentSemesters($nip){
		$data = json_decode($this->Ask_Scodoc("etudiant/nip/$nip/formsemestres"));

		if($data != ''){
			$output = [];
			forEach($data as $value){

				$output[] = [
					'titre' => $value->formation->acronyme,
					'formsemestre_id' => $value->formsemestre_id,
					'semestre_id' => $value->semestre_id,
					'annee_scolaire' => str_replace(' - ', '/', $value->annee_scolaire),
					'date_fin' => $value->date_fin
				];
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
			'semestre' : '833', 		// Semestre
			'nip' : '21800202', 		// numéro étudiant
			[format: json ou pdf]		// format de retour
		]

	Sortie :
		// JSON avec les données du relevé de notes.
		// ou PDF du relevé

	*******************************/
	public function getReportCards($semestre, $nip, $format = 'json'){
		global $Config;
		$output = json_decode($this->Ask_Scodoc("etudiant/nip/$nip/formsemestre/$semestre/bulletin?format=$format"));

		////////// TODO gérer l'export de la version PDF

		if(isset($output->rang) || $output->type == 'BUT'){	// Version BUT ou autres versions
			if($output->publie === false){
				$output->message = $Config->message_non_publication_releve;
			}
			Analytics::add('relevé');
			return $output;
		}else{
			returnError(
				"Relevé non disponible pour ce semestre, veuillez contacter votre responsable en lui précisant : vérifier si l'export des notes du semestre est autorisé dans Scodoc."
			);
		}
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
		$json = json_decode($this->Ask_Scodoc('departement/' . $dep . '/formsemestres_courants'));

		$output = [];
		foreach($json as $value){
			$output[] = [
				'titre_court' => $value->titre_formation,
				'titre_long' => $value->titre,
				'num' => $value->semestre_id,
				'id' => $value->formsemestre_id
			];
		}
		return $output;
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
		$dataSEM = $this->getDepartmentSemesters($dep);
		//var_dump($dataSEM);die();
		$output = [];
		foreach($dataSEM as $value){
			$value = (object) $value;
			$data_students = (object) $this->getStudentsInSemester($value->id);
			$output[] = [
				'titre' => $value->titre_court . ' - semestre ' . $value->num,
				'semestre_id' => $value->id,
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
	public function getStudentDepartment($nip){
		$json = json_decode($this->Ask_Scodoc('etudiant/nip/' . $nip));
		return $json->dept_acronym;
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
		$json = json_decode(
			$this->Ask_Scodoc("formsemestre/$sem/etudiants")
		);

		$groupes = [];
		$output_json = [];
		foreach($json as $value){
			$groupe = end($value->groups)->group_name;
			if(!in_array($groupe, $groupes)){
				$groupes[] = $groupe;
			}

			$output_json[] = [
				'nom' => $value->nom,
				'prenom' => $value->prenom,
				'groupe' => $groupe,
				'nip' => $value->code_nip
				//'email' => Annuaire::getStudentIdCASFromNumber($value->nip)
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
		$json = json_decode(
			$this->Ask_Scodoc("formsemestre/$sem/programme")
		);

		$output_json = [];

		if(count($json->ressources) > 0){
			$output_json["modules"] = $this->getModulesData($json->ressources);
		} else {
			$output_json["modules"] = $this->getModulesData($json->modules);
		}
		if(count($json->saes) > 0){
			$output_json["saes"] = $this->getModulesData($json->saes);
		}

		return $output_json;
	}

	private function getModulesData($data){
		$output = [];
		foreach($data as $value){
			$output[] = [
				'titre' => $value->module->titre,
				'code' => $value->module->code
			];
		}
		return $output;
	}

}
