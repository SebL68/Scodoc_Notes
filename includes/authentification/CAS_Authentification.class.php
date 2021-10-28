<?php

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
			include_once $this->path . '/includes/JWT/JWT.php';
			include $this->path . 'includes/config.php';

			// Message d'erreur si le serveur est mal configuré.
			if($key == ""){
				exit(
					json_encode(
						array(
							'erreur' => 'ADMIN : veuillez définir une clé pour les jetons JWT.'
						)
					)
				);
			}
			
			$decoded = JWT::decode($_POST['token'], $key, ['HS256']);
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
			require_once $this->path . '/CAS/include/CAS.php';
			require_once $this->path . '/CAS/config/cas_uha.php';
	
			// Initialize phpCAS
			phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
			phpCAS::setCasServerCACert($cas_server_ca_cert_path);

			if(phpCAS::isAuthenticated()){
				/* Utilisateur authentifié */
				$_SESSION['id'] = phpCAS::getUser();
				$this->session = $_SESSION['id'];

			}else{
				/* Utilisateur non authentifié, redirection vers une page pour s'authentifier au CAS. */
				exit(
					json_encode(
						[
							'redirect' => '/services/doAuth.php'
						]
					)
				);
			}
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