<?php
	if(!isset($_SESSION)){ session_start(); }
	
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/LDAPData.php";

	use \Firebase\JWT\JWT;

/*****************************/
/* authData()
	Fonction principale d'authentification, soit par un jeton JWT soit par le CAS

	Entrée :
		/
	
	Sortie :
		{
			'redirect' => '/services/doAuth.php' // URL vers la page pour s'authentifier
		}
	ou
		{
			'session' => 'jean.dupond@uha.fr', // mail de la personne identifiée
			'statut' => ETUDIANT | PERSONNEL | ADMINISTRATEUR | INCONNU
		}

******************************/
	function authData(){
		global $path;
		if(isset($_POST['token'])){
	/*******************************************************/
	/* Est-ce qu'un jeton JWT est utilisé pour avoir accès */
	/*******************************************************/
			include $path . '/includes/JWT/JWT.php';
			include $path . '/includes/JWT/key.php';

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
			$_SESSION['statut'] = $decoded->statut;

		}else{
	/****************************************************/
	/* Vérification auprès du CAS de l'authentifiaction */
	/****************************************************/
			require_once $path . '/CAS/include/CAS.php';
			require_once $path . '/CAS/config/cas_uha.php';
	
			// Initialize phpCAS
			phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
			phpCAS::setCasServerCACert($cas_server_ca_cert_path);
	
			if(!isset($_SESSION['id']) || $_SESSION['id'] == ''){
				if(phpCAS::isAuthenticated()){
	
					/* Utilisateur authentifié */
					$_SESSION['id'] = phpCAS::getUser();
	
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
		}
		return [
			'session' => $_SESSION['id'],
			'statut' => statut($_SESSION['id'])
		];
	}
?>