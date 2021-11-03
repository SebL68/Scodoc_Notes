<?php
/****************************/
/* Class CAS_authentification
	Strucutre parente de la classe Auth, permettant de mettre en place le fonctionnement de base de l'authentification

	==> Voir la class Auth

/****************************/
	if(!isset($_SESSION)){ session_start(); }
	use \Firebase\JWT\JWT;

	class CAS_authentification implements Authentification{
		
		private $session;
		private $statut;
		private $path;

	/****************/
	/* Constructeur */
	/****************/
		public function __construct(){
			$this->path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

			
			if(isset($_POST['token'])){
				/* Accès par jeton */
				$this->tokenAuth();

			} elseif(isset($_SESSION['id']) && $_SESSION['id'] != '') {
				/* Utilisateur déjà authentifié */
				$this->session = $_SESSION['id'];
				$this->statut = $_SESSION['statut'];

			} else {
				/* Procédure d'authentification */
				$this->defaultAuth();
				$this->defineStatut();
			}
		}

	/*************/
	/* Interface */
	/*************/
		public function getSessionName(){
			return $this->session;
		}

		public function getStatut(){
			return $this->statut;
		}

	/******************************/
	/* Authentification par jeton */
	/******************************/
		private function tokenAuth(){
			include_once $this->path . '/lib/JWT/JWT.php';
			include_once $this->path . '/config/config.php';

			// Message d'erreur si le serveur est mal configuré.
			if(Config::$JWT_key == ""){
				exit(
					json_encode(
						array(
							'erreur' => 'ADMIN : veuillez définir une clé pour les jetons JWT.'
						)
					)
				);
			}
			
			$decoded = JWT::decode($_POST['token'], Config::$JWT_key, ['HS256']);
			$_SESSION['id'] = $decoded->session;

			switch($decoded->statut){
				case 'inconnu':
					$_SESSION['statut'] = INCONNU;
					break;
				case 'etudiant':
					$_SESSION['statut'] = ETUDIANT;
					break;
				case 'personnel':
					$_SESSION['statut'] = PERSONNEL;
					break;
				case 'administrateur':
					$_SESSION['statut'] = ADMINISTRATEUR;
					break;
				case 'superadministrateur':
					$_SESSION['statut'] = SUPERADMINISTRATEUR;
					break;
			}

			$this->session = $_SESSION['id'];
			$this->statut = $_SESSION['statut'];
		}

	/*******************************/
	/* Authentification par le CAS */
	/*******************************/
		private function defaultAuth(){
			require_once $this->path . '/lib/CAS/CAS.php';
			require_once $this->path . '/config/cas_config.php';

			// Initialize phpCAS
			phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);	
			phpCAS::setCasServerCACert($cas_server_ca_cert_path);

			if(phpCAS::isAuthenticated()){
				// Utilisateur authentifié
				$_SESSION['id'] = phpCAS::getUser();
				$this->session = $_SESSION['id'];

			}else{
				// Utilisateur non authentifié, redirection vers une page pour s'authentifier au CAS.
				exit(
					json_encode(
						[
							'redirect' => '/services/doAuth.php'
						]
					)
				);
			}
		}
	/****************************************************************/
	/* Processus d'authentification sur une page à part pour le CAS */
	/****************************************************************/
	// Contenu de la page html/services/doAuth.php
	// Cette page permet de mettre en place le cookie d'authentification
		public static function doAuth(){
			$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

			require_once $path . '/lib/CAS/CAS.php';
			require_once $path . '/config/cas_config.php';

			// Initialize phpCAS
			phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
				
			// force CAS authentication
			//phpCAS::setNoCasServerValidation() ;
			phpCAS::setCasServerCACert($cas_server_ca_cert_path);
			phpCAS::forceAuthentication(); 

			$_SESSION['id'] = phpCAS::getUser();

			header('Location: '. $_GET['href']);
		}

	/**********************/
	/* Deconnexion du CAS */
	/**********************/
	// Contenu de la page html/logout.php
	// Permet de supprimer l'authentification de l'utilisateur
		public static function logout(){
			$_SESSION = array();
			$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
			require_once $path . '/lib/CAS/CAS.php';
			require_once $path . '/config/cas_config.php';

			phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
			phpCAS::logoutWithRedirectService('https://notes.iutmulhouse.uha.fr/');
		}
	

	/************************************/
	/* Définition du statut par le LDAP */
	/************************************/
		private function defineStatut(){
			include_once "$path/includes/LDAPData.php";
			$_SESSION['statut'] = statut($this->$session);
			$this->statut = $_SESSION['statut'];
		}
	};
?>