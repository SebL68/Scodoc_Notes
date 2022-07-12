<?php
/****************************/
/* Class User
	Créer une nouvel instance d'un utilisateur et initie son authentification.

	On a alors accès à son identifiant de session (typiquement son mail) et à son statut :
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

	require_once $Config->auth_class;	// Class Auth
	require_once $Config->service_annuaire_class;	// Class Service_Annuaire

	class User{
		private $id;
		private $idCAS;
		private $name;
		private $statut;
		private $path;

	/****************/
	/* Constructeur */
	/****************/
		public function __construct(){
			global $Config;
			$this->path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

			$header = apache_request_headers()['Authorization'] ?? "";
			preg_match('/Bearer\s((.*)\.(.*)\.(.*))/', $header, $token);

			if(($token[1] ?? '') != '' && $Config->JWT_key != ''){
				/* Accès par jeton */
				$this->tokenAuth($token[1]);
				$this->defineSession();

			} elseif(isset($_SESSION['statut']) && $_SESSION['statut'] != '') {
				/* Utilisateur déjà authentifié */
				$this->id = $_SESSION['id'];
				$this->idCAS = $_SESSION['idCAS'];
				$this->name = $_SESSION['name'];
				$this->statut = $_SESSION['statut'];

			} else {
				/* Procédure d'authentification */
				$infoCAS = Auth::defaultAuth();
				$this->idCAS = $infoCAS[0];
				$this->name = 
					($Config->nameFromIdCAS)($idCAS) ??
					$infoCAS[1]['cn'] ?? 
					$infoCAS[1]['displayName'] ?? 
					'Mme, M.';
				$this->statut = $this->defineStatut($this->idCAS);
var_dump($this->statut);die();
				if($this->statut < PERSONNEL){
					$this->id = Annuaire::getStudentNumberFromIdCAS($this->idCAS);
				} else {
					$this->id = $this->idCAS;
				}
				
				$this->defineSession();
			}
		}

		private function defineSession(){
			$_SESSION['id'] = $this->id;
			$_SESSION['idCAS'] = $this->idCAS;
			$_SESSION['name'] = $this->name;
			$_SESSION['statut'] = $this->statut;
		}

	/*************/
	/* Interface */
	/*************/
		public function getId(){
			return $this->id;
		}

		public function getName(){
			return $this->name;
		}

		public function getStatut(){
			return $this->statut;
		}

	/******************************/
	/* Authentification par jeton */
	/******************************/
		private function tokenAuth($token){
			global $Config;
			include_once $this->path . '/lib/JWT/JWT.php';
			include_once $this->path . '/includes/default_config.php';
			
			$decoded = JWT::decode($token, $Config->JWT_key, ['HS256']);

			$this->id = $decoded->id;
			$this->idCAS = $decoded->idCAS;
			$this->name = $decoded->name;

			switch($decoded->statut){
				case 'inconnu':
					$this->statut = INCONNU;
					break;
				case 'etudiant':
					$this->statut = ETUDIANT;
					break;
				case 'personnel':
					$this->statut = PERSONNEL;
					break;
				case 'administrateur':
					$this->statut = ADMINISTRATEUR;
					break;
				case 'superadministrateur':
					$this->statut = SUPERADMINISTRATEUR;
					break;
			}
		}

	/***********************************************/
	/* Définition du statut à partir de l'annuaire */
	/***********************************************/
		private function defineStatut($id){
			global $Config;
			if($Config->acces_enseignants == true){
				return Annuaire::statut($this->id);
			} else {
				return ETUDIANT;
			}
		}
	};