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

			$Config->scodoc_url = 'http://192.168.43.67:5000/ScoDoc';
			/* Configuration pour récupérer le token */ 
			$options = array(
				//CURLOPT_HTTPHEADER => array('Expect:'),
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
		public function Ask_Scodoc($url_query, $options = []){
			global $Config;
			$data = http_build_query($options);
			curl_setopt($this->ch, CURLOPT_URL, $Config->scodoc_url . "/api/$url_query?$data");

			// Pour tester d'envoyer des données en POST :
			//$payload = '[{"date_debut": "2022-11-11T08:00","date_fin": "2022-11-11T10:00","etat": "absent"}]';
			//$payload = '[{"etat":"valide","date_debut":"2023-07-26T07:59","date_fin":"2023-07-26T10:01"}]';
			//$payload = '[8714,8722]';
			//curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $payload );
			//////////////////////////////////////////////
			return curl_exec($this->ch);
		}
	}

	
	$Scodoc = new Scodoc();
	
	//echo $Scodoc->Ask_Scodoc('departements');
	//echo $Scodoc->Ask_Scodoc('departement/MMI/etudiants');
	//echo $Scodoc->Ask_Scodoc('departements/MMI/etudiants/list/349');
	//echo $Scodoc->Ask_Scodoc('formsemestre/319/programme');		// Pour UEetModules : bug pour les DUT	// MANQUE sur passerelle et HS sur Scodoc 349 ou 419
	//echo $Scodoc->Ask_Scodoc('formsemestre/349/etudiants');
	//echo $Scodoc->Ask_Scodoc('departement/MMI/formsemestres_courants');			

	//echo $Scodoc->Ask_Scodoc('etudiants/courants');
	//echo $Scodoc->Ask_Scodoc('etudiant/nip/21902367');
	//echo $Scodoc->Ask_Scodoc('etudiant/nip/22003752/formsemestres');

	//echo $Scodoc->Ask_Scodoc('etudiant/nip/22003752/formsemestre/419/bulletin');
	//echo $Scodoc->Ask_Scodoc('etudiant/nip/22003752/formsemestre/349/bulletin/long/pdf/nosig');


	/*Test API nouvelles absences - Scodoc 9.6+ */
	//echo $Scodoc->Ask_Scodoc('assiduite/38657');
	//echo $Scodoc->Ask_Scodoc('assiduites/nip/22203129');
	//echo $Scodoc->Ask_Scodoc('assiduites/etudid/5167/count');
	//echo $Scodoc->Ask_Scodoc('assiduites/formsemestre/477');
	//echo $Scodoc->Ask_Scodoc('assiduite/5167/create');	// Données en POST

	//echo $Scodoc->Ask_Scodoc('justificatif/nip/22202215/create');	// Données en POST
	//echo $Scodoc->Ask_Scodoc('justificatif/delete');	// Données en POST

?>