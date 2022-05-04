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
	
			/* Configuration pour récupérer le token */ 
			$options = array(
				CURLOPT_HTTPHEADER => array('Expect:'),
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
			return curl_exec($this->ch);
		}
	}

	
	$Scodoc = new Scodoc();
	
	//echo $Scodoc->Ask_Scodoc('departements');	// ok
	//echo $Scodoc->Ask_Scodoc('departements/MMI/etudiants/liste');	// ok
	//echo $Scodoc->Ask_Scodoc('departements/MMI/etudiants/liste/418');
	//echo $Scodoc->Ask_Scodoc('formsemestre/419/programme');	// manque des ressources et saes (et aussi modules DUT) + lier les ressources et sae à toutes les UE
	//echo $Scodoc->Ask_Scodoc('departements/MMI/semestres_courants');	// titre court

	//echo $Scodoc->Ask_Scodoc('etudiants/courant');	// ok
	//echo $Scodoc->Ask_Scodoc('etudiant/nip/22003752');	// ok
	//echo $Scodoc->Ask_Scodoc('etudiant/nip/22003752/formsemestres');	// titre court 

	//echo $Scodoc->Ask_Scodoc('formsemestre/418/etudiant/nip/22002244/bulletin');	// ok
	//echo $Scodoc->Ask_Scodoc('formsemestre/418/liste_etudiants');	// A ajouter


?>