<?php 
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/config/config.php";

	class Scodoc{
		private $ch; // Connexion CURL

		/***********************************************************/
		/* Initialisation de la connexion et récupération du token */
		/***********************************************************/
		public function __construct(){
			$this->ch = curl_init();
			curl_setopt($this->ch, CURLOPT_FAILONERROR, true);  
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); 
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false); // Serveur Scodoc non accéssible depuis le net, donc vérification impossible
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->ch, CURLOPT_POST, true);

			curl_setopt($this->ch, CURLOPT_URL, Config::$scodoc_url.'/api/tokens');
			curl_setopt($this->ch, CURLOPT_USERPWD, Config::$scodoc_login2 . ':' . Config::$scodoc_psw);

			$token = json_decode(curl_exec($this->ch))->token;

			curl_setopt($this->ch, CURLOPT_USERPWD, NULL);
			curl_setopt($this->ch, CURLOPT_POST, false);
			$headers = array(
				"Authorization: Bearer $token"
			);

			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		}

		/************************/
		/* Accès à l'API Scodoc */
		/************************/
		public function Ask_Scodoc($url_query, $options = []){
			$data = http_build_query($options);

			curl_setopt($this->ch, CURLOPT_URL, Config::$scodoc_url . "/api/$url_query?$data");
			return curl_exec($this->ch);
		}
	}

	

	$Scodoc = new Scodoc();
	var_dump($Scodoc->Ask_Scodoc('list_depts'));

?>