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
		global $path;

		$this->tokenPath = "$path/includes/token.txt";
		$this->ch = curl_init();
		$Config->scodoc_url = 'http://192.168.43.67:5000/ScoDoc';

		$options = array(
			CURLOPT_FORBID_REUSE => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_REFERER => $_SERVER['SERVER_NAME'] . '/?passerelle=' . $Config->passerelle_version
		);
		curl_setopt_array($this->ch, $options);

		if(!file_exists($this->tokenPath)) {
			$this->getScodocToken();
		} else {
			$tokenFile = fopen($this->tokenPath, "r");
			$token = fread($tokenFile, 1000);
			fclose($tokenFile);
			$headers = array(
				"Authorization: Bearer $token"
			);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($this->ch, CURLOPT_USERPWD, NULL);
		}
	}

	private function getScodocToken() {
		global $Config;

		$options = array(
			CURLOPT_POST => true,
			CURLOPT_URL => $Config->scodoc_url.'/api/tokens',
			CURLOPT_USERPWD => $Config->scodoc_login . ':' . $Config->scodoc_psw,
		);
		$headers = array();
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt_array($this->ch, $options);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, NULL);

		$token = json_decode(curl_exec($this->ch), false)->token;

		$tokenFile = fopen($this->tokenPath, "w");
		fwrite($tokenFile, $token);
		fclose($tokenFile);

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
	private function Ask_Scodoc($url_query, $options = [], $POSTData = null){
		global $Config;
		global $path;
		$data = http_build_query($options);
	
	/* Speed test début */
		if($Config->analyse_temps_requetes){
			$time_start = microtime(true);
		}
	/********************/

		curl_setopt($this->ch, CURLOPT_URL, $Config->scodoc_url . "/api/$url_query?$data");
		if($POSTData != null) {
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $POSTData);
		}

		$response = curl_exec($this->ch);

		if(@json_decode($response)->message == 'Non autorise (logic)') {
			$this->getScodocToken();

			curl_setopt($this->ch, CURLOPT_URL, $Config->scodoc_url . "/api/$url_query?$data");
			if($POSTData != null) {
				curl_setopt($this->ch, CURLOPT_POSTFIELDS, $POSTData);
			}

			$response = curl_exec($this->ch);
		}

	/* Speed test fin */
		if($Config->analyse_temps_requetes){
			$time_end = microtime(true);
			$time = $time_end - $time_start;

			// Enregistrement du temps dans un fichier
			$data = [date('Y-m-d H:i:s'), $url_query, $time];
			$file = $path . '/data/analytics/temps_requetes.csv';
			$fp = fopen($file, 'a');
			fputcsv($fp, $data, ';');
			fclose($fp);
		}
	/******************/

		return $response;
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
		$output = [];
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
				$output[] = [
					'code' => $value->acronym,
					'nom' => $value->dept_name
				];
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
				titre: "BUT MMI",
				formsemestre_id: 319,	// code semestre Scodoc
				semestre_id: 3, 		// numéro du semestre
				annee_scolaire: 2022/2023
			}
		]

	*******************************/
	public function getStudentSemesters($nip){
		$data = json_decode($this->Ask_Scodoc('formsemestres/query', ['nip' => $nip]));

		if($data != ''){
			$output = [];

			function ordre($a, $b){
				$a = $a->annee_scolaire . $a->semestre_id;
				$b = $b->annee_scolaire . $b->semestre_id;
				return ($a<$b)?-1:1;
			}
			uasort($data, 'ordre');

			forEach($data as $value){

				$output[] = [
					'titre' => $value->formation->acronyme,
					'formsemestre_id' => $value->formsemestre_id,
					'semestre_id' => $value->semestre_id,
					'annee_scolaire' => $value->annee_scolaire . '/' . ($value->annee_scolaire+1)
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
	public function getReportCards($semestre, $nip, $format = ''){
		global $Config;

		if($format == 'pdf'){
			$output = $this->Ask_Scodoc("etudiant/nip/$nip/formsemestre/$semestre/bulletin/long/$format/nosig");
			Analytics::add('relevéPDF');
			return $output;
		}

		$output = json_decode($this->Ask_Scodoc("etudiant/nip/$nip/formsemestre/$semestre/bulletin"));
		if(isset($output->publie)){	// Détecte si c'est une réponse normale
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
		
		$output = [];
		foreach($dataSEM as $value){
			$value = (object) $value;
			$data_students = (object) $this->getStudentsInSemester($value->id);

			$output[] = [
				'titre' => $value->titre_long . ' - semestre ' . $value->num,
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
			$this->Ask_Scodoc("formsemestre/$sem/etudiants/long/query", ['etat' => "I"])
		);

		$groupes = [];
		$output_json = [];
		foreach($json as $value){
			$studentGroups = [];
			foreach($value->groups as $group){
				$partition = $group->partition_name;
				$studentGroups[] = $group->group_name;
				$groupe = $group->group_name;
				$groupes[$partition][] = $groupe;
			}

			$output_json[] = [
				'nom' => $value->nom,
				'prenom' => $value->prenom,
				'groupes' => $studentGroups,
				'nip' => $value->code_nip,
				'idcas' => Annuaire::getStudentIdCASFromNumber($value->code_nip),
				'date_naissance' => $value->date_naissance,
				'boursier' => $value->boursier
				// 'num_ine' => $value->code_ine

			];
		}
		foreach($groupes as &$partition){
			$partition = array_unique($partition);
			sort($partition);
		}

		if(count($groupes) == 0) {
			$groupes = ['TP' => ['Groupe 1']];
		}
		
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
				'code' => $value->module->code,
				'id' => $value->id
			];
		}
		return $output;
	}

/***********************/
/* API absences Scodoc */
/***********************/

	/*******************************/
	/* createAbsence()
	Créé une nouvelle absence
	*******************************/
	public function createAbsence($nip, $data){
		return json_decode( $this->Ask_Scodoc("assiduite/nip/$nip/create", [], $data) );
	}

	/*******************************/
	/* modifAbsence()
	Modifie une absence
	*******************************/
	public function modifAbsence($id, $data){
		return json_decode( $this->Ask_Scodoc("assiduite/$id/edit", [], $data) );
	}

	/*******************************/
	/* deleteAbsence()
	Supprime une absence
	*******************************/
	public function deleteAbsence($data){
		return json_decode( $this->Ask_Scodoc("assiduite/delete", [], $data) );
	}

	/*******************************/
	/* getStudentAbsences()
	Liste les absences d'un étudiant durant un semestre
	*******************************/
	public function getStudentAbsences($sem, $nip){
		return json_decode(
			$this->Ask_Scodoc(
				"assiduites/nip/$nip", 
				['formsemestre_id' => $sem])
		);
	}

	/*******************************/
	/* getSemesterAbsences()
	Liste les absences de tous les étudiants d'un semestre
	*******************************/
	public function getSemesterAbsences($sem){
		return json_decode( $this->Ask_Scodoc("assiduites/formsemestre/$sem", ['with_justifs' => true]) );
	}

	/*******************************/
	/* setJustif()
	Ajout d'une justification d'absence
	*******************************/
	public function setJustif($nip, $debut, $fin){
		return json_decode( 
			$this->Ask_Scodoc(
				"justificatif/nip/$nip/create", 
				[], 
				json_encode(
					[
						[
							'etat' => 'valide',
							'date_debut' => $debut,
							'date_fin' => $fin
						]
					]
				)
			) 
		);
	}

	/*******************************/
	/* unsetJustif()
	Supprime un jusitifcatif
	*******************************/
	public function unsetJustif($id){
		return json_decode( $this->Ask_Scodoc('justificatif/delete', [], $id) );
	}
}