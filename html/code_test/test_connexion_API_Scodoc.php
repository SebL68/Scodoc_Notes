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


		
/*************************************************************************************/
/* A conserver : mise en cache de données et mise à jour si les données ont expirées */
/*************************************************************************************/
// Il faudrait ajouter un traitement asynchrone ou un autre thread pour sauvegarder les données pendant que les anciennes sont envoyées.
/*
		public function getAllStudents(){
			global $path;
			$file = $path . '/data/annuaires/autocompletion_students.json';

			if(!file_exists($file) || (filemtime($file) > (time() + 4 * 3600))){	// Nouveau fichier ou données anciennes : + de 4h
				$output = [];
				$json = json_decode($this->Ask_Scodoc('etudiants/courant'));
				forEach($json as $value){
					$output[] = [
						$value->code_nip,
						$value->nom . ' ' . ucfirst(strtolower($value->prenom))
					];
				}
				file_put_contents($file, json_encode($json));
				return $output;
			}

			// renvoie données
			return json_decode(file_get_contents($file));
		}
*************************************************************************************/
	}

	
	$Scodoc = new Scodoc();
	
	//echo $Scodoc->Ask_Scodoc('departements');	// ok
	//echo $Scodoc->Ask_Scodoc('departements/MMI/etudiants/liste');	// ok
	//echo $Scodoc->Ask_Scodoc('departements/MMI/etudiants/liste/418');	// changer pour /formsemestre/<int:formsemestre_id>/liste_etudiants
	//echo $Scodoc->Ask_Scodoc('departements/MMI/formsemestre/419/programme');	// Juste formsemestre sans "departements/MMI" + manque des ressources et saes (et aussi modules DUT) + lier les ressources et sae à toutes les UE
	//echo $Scodoc->Ask_Scodoc('departements/MMI/semestres_courants');	// simplifier data + titre court et num semestre

	//echo $Scodoc->Ask_Scodoc('etudiants/courant');	// ok
	//echo $Scodoc->Ask_Scodoc('etudiant/nip/22003752');	// ok
	//echo $Scodoc->Ask_Scodoc('etudiant/nip/22003752/formsemestres');	// simplifier data + titre court et num semestre

	//echo $Scodoc->Ask_Scodoc('formsemestre/418');
	//echo $Scodoc->Ask_Scodoc('formsemestre/418/departements/MMI/etudiant/nip/22002244/bulletin');	// Voir si on peut ne pas mettre le departement
?>