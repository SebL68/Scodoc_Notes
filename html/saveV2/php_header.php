<?php
	ob_start("ob_gzhandler");
	session_start();

/* Accès à l'API depuis d'autres sites */
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Credentials: true');

/* Debug */
	/*error_reporting(E_ALL);
	ini_set('display_errors', '1');*/
	
	if(isset($_SESSION['id'])){
		$id = $_SESSION['id'];
	} else {
		die('"Fin de session"');
	}
	$sco_url = 'https://iutmscodoc9.uha.fr/ScoDoc/';

	if($_SESSION['enseignant'] === true){
		$id = $_GET['etu'];
	}

/**************************/
/* Fonctions d'aide */
/**************************/
function CURL($url){
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
	curl_setopt($ch, CURLOPT_FAILONERROR, true);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   

	$output = curl_exec($ch);
	curl_close($ch);
	return $output;    
}
?>