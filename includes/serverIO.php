<?php
/******************************************/
/* serverIO.php
	Fonctions de communication vers le serveur Scodoc

	Fonction à utiliser en priorité :
		Ask_Scodoc()
*******************************************/

	if(!isset($_SESSION)){ session_start(); }
	
/**************************/
/* Configuration du CURL  */
/**************************/
	function CURL($url){
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($ch, CURLOPT_FAILONERROR, true);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Serveur Scodoc non accéssible depuis le net, donc vérification impossible
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$output = curl_exec($ch);
		curl_close($ch);
		return $output;    
	}

/**************************/
/* Ask_Scodoc() :

	Entrées :
		$url_query : [string] url de question à Scodoc - exemple : "Scolarite/Notes/etud_info"
		$dep - optionnel : [string] département, exemple : MMI. Si fonction url_query globale (sans département), laisser vide.
		$options - optionnel : tableau associatif des options à transmettre - exemple :
			[
				'formsemestre_id' => 'SEM8871',
				'format' => 'json'
			]

		Retour : [string] du résultat
****************************/
	function Ask_Scodoc($url_query, $dep = '', $options = [], $patch = true){
	
		include 'loginScodoc.php';

		$data = http_build_query(array_merge($acces, $options));
		
		if($dep != ''){
			$dep = '/'.$dep;
		}

		return CURL("$scodoc_url/ScoDoc$dep$url_query?$data");

	}