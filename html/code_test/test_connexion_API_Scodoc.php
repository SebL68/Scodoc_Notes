<?php 
	header('Content-type:application/json');

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/default_config.php";

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	class Scodoc{
		private $ch; // Connexion CURL

		/***********************************************************/
		/* Initialisation de la connexion et récupération du token */
		/***********************************************************/
		public function __construct(){
			global $Config;
			$this->ch = curl_init();

			/*$fp = fopen(dirname(__FILE__).'/errorlog.txt', 'w');
			curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
			curl_setopt($this->ch, CURLOPT_STDERR, $fp);*/

			//$Config->scodoc_url = 'http://192.168.1.49:5000/ScoDoc';
			/* Configuration pour récupérer le token */ 
			$options = array(
				//CURLOPT_HTTPHEADER => array('Expect:'),
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
		public function Ask_Scodoc($url_query, $options = []){
			global $Config;
			$data = http_build_query($options);
			curl_setopt($this->ch, CURLOPT_URL, $Config->scodoc_url . "/api/$url_query?$data");
		//	var_dump(curl_exec($this->ch));
			return curl_exec($this->ch);
		}
	}

	
	$Scodoc = new Scodoc();
	
	//echo $Scodoc->Ask_Scodoc('departements');							// ok
	//echo $Scodoc->Ask_Scodoc('departements/MMI/etudiants/list');		// ok
	//echo $Scodoc->Ask_Scodoc('departements/MMI/etudiants/list/349');	// ok
	//echo $Scodoc->Ask_Scodoc('formsemestre/349/programme');			// 404
	//echo $Scodoc->Ask_Scodoc('/departement/MMI/formsemestres_courants');			
	//echo $Scodoc->Ask_Scodoc('departements/MMI/semestres_courants');	// manque titre court, exemple : BUT MMI

	echo $Scodoc->Ask_Scodoc('etudiants/courant');					// ok
	//echo $Scodoc->Ask_Scodoc('etudiant/nip/22003752');				// ok
	//echo $Scodoc->Ask_Scodoc('etudiant/nip/22003752/formsemestres');	// manque titre court, exemple : BUT MMI

	//echo $Scodoc->Ask_Scodoc('etudiant/nip/22003752/formsemestre/349/bulletin');	// 404 - changé ?
	//echo $Scodoc->Ask_Scodoc('formsemestre/418/liste_etudiants');		// Supprimé je crois ?



	// Ajouter titre sur les semestres pour le choix.
?>