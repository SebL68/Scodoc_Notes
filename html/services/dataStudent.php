<?php
	ob_start("ob_gzhandler");
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Credentials: true');
	header('Content-type:application/json');

/* Debug */
	/*error_reporting(E_ALL);
	ini_set('display_errors', '1');*/

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/auth.php";
	include_once "$path/includes/LDAPData.php";
	include_once "$path/includes/serverIO.php"; // Fonctions de communication vers le serveur Scodoc

	$authData = (object) authData();

/* Utilisateur qui n'est pas dans la composante : n'est pas autorisé. */
	if($authData->statut == 'none'){ returnError("Ce site est réservé aux étudiants et personnels de l'IUT."); }

	if(isset($_GET['q'])){
		switch($_GET['q']){

			case 'donnéesAuthentification':
				$output = (array) $authData;
				break;

			case 'messageErreur':
				returnError('Ceci est un message d\'erreur test.');
				break;

		}	
		if($output){
			echo json_encode($output/*, JSON_PRETTY_PRINT*/);
		}else{
			returnError('Mauvaise requête.');
		}
	}

	function returnError($msg = "Vous n'êtes pas un personnel habilité pour accéder à cette ressource."){
		exit(
			json_encode(
				array(
					'erreur' => $msg
				)
			)
		);
	}

?>