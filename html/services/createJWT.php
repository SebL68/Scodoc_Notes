<?php
/***************************************/
/* Service de création de token JWT   /*
/* https://github.com/firebase/php-jwt */
/***************************************/

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

	include_once "$path/config/config.php";
	include_once "$path/includes/user.class.php";
	$user = new User();

	if(
		$user->getSessionName() != 'sebastien.lehmann@uha.fr' &&
		$user->getSessionName() != 'denis.graef@uha.fr'
	){ 
		die("Ce service n'est autorisé que pour Sébastien Lehmann, vous pouvez le contacter.");
	}

	use \Firebase\JWT\JWT;

	include $path . '/lib/JWT/JWT.php';

	$payload = [
		'session' => 'sebastien.lehmann@uha.fr', // mail de la personne destinataire du jeton
		'statut' => 'superadministrateur', // 'etudiant' | 'personnel' | 'administrateur' | 'superadministrateur' | INCONNU
		//'exp' => 1608498444 // (optionnel) timestamp d'expiration du token 
	];
	echo JWT::encode($payload, Config::$JWT_key);

/**********************************/
/* Compte demo étudiant :
	Compte_Demo.test@uha.fr
	
	eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzZXNzaW9uIjoiQ29tcHRlX0RlbW8udGVzdEB1aGEuZnIiLCJzdGF0dXQiOiJldHVkaWFudCJ9.kHuiNx8X2mWUjv1LAHVOdcLGCu2yQS_i6fxqZZICuEA
*/
?>