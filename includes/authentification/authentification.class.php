<?php

// https://www.pierre-giraud.com/php-mysql-apprendre-coder-cours/introduction-programmation-orientee-objet/

	interface Authentification{
		public function getSessionName();
		public function getStatut();
	}

	require 'CAS_Authentification.class.php';

	class Auth extends CAS_authentification implements Authentification{	
	/* 
		private function defaultAuth(){
			// Si besoin, votre propre implémentation en surchargeant la méthode
		}
	*/
	}
?>