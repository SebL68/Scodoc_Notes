<?php
/****************************/
/* Class User
	Créer une nouvel instance d'un utilisateur et initie son authentification.

	On a alors accès à son identifiant de session (typiquement son mail) et à son statut (voir config/config.php) :
		'INCONNU'             => 0,
		'ETUDIANT'            => 10,
		'PERSONNEL'           => 20,
		'ADMINISTRATEUR'      => 30,
		'SUPERADMINISTRATEUR' => 40

	Methodes publiques :
		- [String] 	User->getSessionName()
		- [int] 	User->getStatut()
	

/****************************/
	if(!isset($_SESSION)){ session_start(); }
	use \Firebase\JWT\JWT;

	require Config::$auth_class;	// Class Auth

	class User{
		private $session;
		private $statut;
		private $path;

	/****************/
	/* Constructeur */
	/****************/
		public function __construct(){
			$this->path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

			if(isset($_POST['token']) && Config::$JWT_key != ''){
				/* Accès par jeton */
				$this->tokenAuth();

			} elseif(isset($_SESSION['statut']) && $_SESSION['statut'] != '') {
				/* Utilisateur déjà authentifié */
				$this->session = $_SESSION['id'];
				$this->statut = $_SESSION['statut'];

			} else {
				/* Procédure d'authentification */
				$this->session = Auth::defaultAuth();
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
	

	/***********************************************/
	/* Définition du statut à partir de l'annuaire */
	/***********************************************/
		private function defineStatut(){
			include_once $this->path.'/includes/annuaire.class.php';
			$_SESSION['statut'] = Annuaire::statut($this->session);
			$this->statut = $_SESSION['statut'];
		}
	};
?>