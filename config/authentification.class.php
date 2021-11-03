<?php

// https://www.pierre-giraud.com/php-mysql-apprendre-coder-cours/introduction-programmation-orientee-objet/

/****************************/
/* Class Auth
*	Classe permettant d'instancier le processus d'authentification
*
*	Si la personne n'est pas authentifiée, un JSON de redirection est renvoyé :
*		{
*			'redirect' => '/services/doAuth.php' // URL vers la page pour s'authentifier
*		}
*
*	Méthodes publiques :
*
*		Auth->getSessionName() : renvoie le mail de la personne => string 'jean.dupond@uha.fr'
*
*		Auth->getStatut() : renvoie le statut de la personne => int ETUDIANT | PERSONNEL | ADMINISTRATEUR | INCONNU

/****************************/

	interface Authentification{
		public function getSessionName();
		public function getStatut();
	}
	
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	require $path.'/includes/authentification.class.php';

	// Si besoin, votre propre implémentation en surchargeant ces méthodes
	class Auth extends CAS_authentification implements Authentification{	
	/* 
		// Cette méthode définit l'authentification par défaut
		private function defaultAuth(){		

			 Fonctionnement attendu : 
			* Si la personne est authentifiée, ajouter l'authentifiant à $this->session;
			* Si la personne n'est pas authentifiée, rediriger vers une page prévue pour l'authentification :

				exit(
					json_encode(
						[
							'redirect' => '/services/doAuth.php'
						]
					)
				);
			 
	 }
		
	// Contenu de la page html/services/doAuth.php
	// Cette page permet de mettre en place le cookie d'authentification
		public static function doAuth(){
			
		}

	// Contenu de la page html/logout.php
	// Permet de supprimer l'authentification de l'utilisateur
		public static function logout(){

		}	
	*/
	}
?>