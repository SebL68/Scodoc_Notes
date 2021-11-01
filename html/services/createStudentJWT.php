<?php
/***************************************/
/* Service de création de tocken JWT 
	pour des TP étudiants			   */
/* https://github.com/firebase/php-jwt */
/***************************************/

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include "$path/includes/auth.php";

	$authData = (object) authData();

	use \Firebase\JWT\JWT;

	include $path . '/includes/JWT/JWT.php';
	include $path . '/includes/JWT/key.php';

	$payload = [
		'session' => $authData->session, // mail de la personne destinataire du jeton
		'statut' => $authData->statut, 
		'exp' => time() + (26*7*24*60*60) // Valide pour ce semestre (6 mois de plus par rapport à maintenant)
	];
	echo 'Votre jeton d\'accès est : <br>' . JWT::encode($payload, Config::$JWT_key);
?>