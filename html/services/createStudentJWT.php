<?php
/***************************************/
/* Service de création de tocken JWT 
	pour des TP étudiants			   */
/* https://github.com/firebase/php-jwt */
/***************************************/

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	
	include_once "$path/includes/default_config.php";
	include_once "$path/includes/user.class.php";
	$user = new User();

	use \Firebase\JWT\JWT;

	include $path . '/lib/JWT/JWT.php';
	include $path . '/lib/JWT/key.php';

	$payload = [
		'session' => $user->getSessionName(), // mail de la personne destinataire du jeton
		'statut' => 'etudiant', 
		'exp' => time() + (1*7*24*60*60) // Valide pour ce semestre (7 jours de plus par rapport à maintenant)
	];
	echo 'Votre jeton d\'accès est : <br>'; //. JWT::encode($payload, $Config->JWT_key);

	$root_url = (isset($_SERVER["https"]) ? "https://" : "http://" ). $_SERVER["HTTP_HOST"];
	echo $root_url."?token=".JWT::encode($payload, $Config->JWT_key);
?>