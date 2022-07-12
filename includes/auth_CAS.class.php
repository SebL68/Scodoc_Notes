<?php

	/**********************/
	/** class Auth
	 * 		Cette class est spécifique à l'authentification par le CAS
	 * 		Il est possible d'en créer une nouvelle pour un autre moyen d'authentification
	 * 		Il faut alors configurer le fichier utiliser dans config/config.php
	 * 
	 * 		Les méthodes attentues sont :
	 * 			- Auth::defaultAuth()
	 * 			- Auth::doAuth()
	 * 			- Auth::logout()
	*/

	class Auth {
	/*******************************/
	/* Authentification par le CAS */
	/*******************************/
		public static function defaultAuth(){
			$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

			require_once $path . '/lib/CAS/CAS.php';
			require_once $path . '/config/cas_config.php';

			// Initialize phpCAS
			phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
			if($cas_server_ca_cert_path != '') {
				phpCAS::setCasServerCACert($cas_server_ca_cert_path);
			} else {
				phpCAS::setNoCasServerValidation();
			}

			if(phpCAS::isAuthenticated()){
				// Utilisateur authentifié
				return [
					phpCAS::getUser(),
					phpCAS::getAttributes()
				];

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
				
			if($cas_server_ca_cert_path != '') {
				phpCAS::setCasServerCACert($cas_server_ca_cert_path);
			} else {
				phpCAS::setNoCasServerValidation();
			}
			phpCAS::forceAuthentication(); 

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
			phpCAS::logoutWithRedirectService('');
		}
	}