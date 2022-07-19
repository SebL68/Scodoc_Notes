<?php 

$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
include_once "$path/includes/default_config.php";
include_once "$path/includes/serverIO.php";

class Scodoc{
	private $ch; // Connexion CURL

	/***********************************************************/
	/* Initialisation de la connexion et récupération du token */
	/***********************************************************/
	public function __construct(){
		global $Config;
		$this->ch = curl_init();
		//$Config->scodoc_url = 'http://192.168.1.49:5000/ScoDoc';
		/* Configuration pour récupérer le token */ 
		$options = array(
			//CURLOPT_FORBID_REUSE => true,
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
		$json = json_decode($this->Ask_Scodoc('etudiants/courant'));
		forEach($json as $value){
			$output[] = [
				$value->nip,
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
					'titre_court' => $value->titre_formation,
					'formsemestre_id' => $value->formsemestre_id,
					'semestre_id' => $value->semestre_id,
					'date_debut' => $value->date_debut,
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
			$data_students = (object) getStudentsInSemester($dep, $value->id);
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
	public function getStudentsInSemester($dep, $sem){
		$json = json_decode(
			Ask_Scodoc(
				'/Scolarite/groups_view',
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
				'email' => Annuaire::getStudentIdCASFromNumber($value->code_nip)
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

	private function findTP($json){
		// Recherche du groupe TP dans la key Pxxxx
		//$output = [];
		foreach($json as $key => $value){
			if(is_numeric($key)){
				return $json->$key;
				//$output[] = $json->$key;
			}
		};
		//return $output;
	}

	/*******************************/
	/* UEAndModules()
	Liste les UE et modules d'un département + semestre

	Entrées : 
		$dep : [string] département - exemple : MMI
		$sem : [string] code semestre Scodoc - exemple : 871

	Sortie :
		[
			{
				UE: "UE1 nom de l'UE",
				modules: [
					{
						"titre": "nom du module 1",
						"code": "W511" // Code scodoc du module
					},
					etc.
				]
			},
			etc.
		]

	*******************************/
	public function UEAndModules($dep, $sem){
		$json = json_decode(Ask_Scodoc(
			'/Scolarite/Notes/formsemestre_description',
			$dep,
			[
				'formsemestre_id' => $sem,
				'format' => 'json'
			]
		));

		array_pop($json); // Supprime le récapitulatif de toutes les UE
		$output_json = [];

		/* 
		Listes des UE et des Modules les uns après les autres 
		Données dispo :
			Code: 'W511',				// null si c'est une UE
			Coef.: '0.5',				// null si c'est une UE
			Inscrits: '12',				// null si c'est une UE
			Module: 'Ecriture numérique',
			Responsable: 'Graef D.',	// null si c'est une UE
			UE: 'UE1 Culture Com &amp; Entreprise',
		*/
		foreach($json as $value){
			if($value->Module != 'Bonus'){
				
				if($value->Responsable == NULL){
					$output_json[] = [
						'UE' => $value->UE,
						'modules' => []
					];
				}else{
					$output_json[count($output_json)-1]['modules'][] = [
						'titre' => $value->Module,
						'code' => $value->Code
					];
						
				}
			}
		}

		return $output_json;
	}

}
