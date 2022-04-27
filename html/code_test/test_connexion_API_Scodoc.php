<?php 
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

			/******************************************************/
			/* Uniquement pour accéder à un serveur Scodoc de dev *
				curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Expect:'));
				$Config->scodoc_url = 'http://192.168.1.49:5000/ScoDoc';
			/******************************************************/

			/* Configuration pour récupérer le token */ 
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); 
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
			//curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($this->ch, CURLOPT_POST, true);

			curl_setopt($this->ch, CURLOPT_URL, $Config->scodoc_url.'/api/tokens');
			curl_setopt($this->ch, CURLOPT_USERPWD, $Config->scodoc_login . ':' . $Config->scodoc_psw);

			$token = json_decode(curl_exec($this->ch))->token;
			
			if(curl_exec($this->ch) === false) {
				throw new Exception(curl_error($this->ch), curl_errno($this->ch));
			}
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
	echo $Scodoc->Ask_Scodoc('list_depts');
	//echo $Scodoc->Ask_Scodoc('etudiants/courant');

?>