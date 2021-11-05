<?php 
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/config/config.php";

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	function CURL($url){
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($ch, CURLOPT_FAILONERROR, true);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__.'/cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__.'/cookie.txt');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Serveur Scodoc non accéssible depuis le net, donc vérification impossible
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_USERPWD, Config::$scodoc_login2 . ':' . Config::$scodoc_psw); 

		$output = curl_exec($ch);
		if ($output === false) {
			throw new Exception(curl_error($ch), curl_errno($ch));
		}
		curl_close($ch);
		return $output;    
	}

	var_dump(CURL('https://iutmscodoc9.uha.fr/ScoDoc/api/tokens'));

?>