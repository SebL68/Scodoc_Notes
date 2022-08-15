<?php
/******************************************/
/* serverIO.php
	Fonctions de communication vers le serveur Scodoc
*******************************************/
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/default_config.php";
	include_once "$path/includes/annuaire.class.php";		// Class Annuaire
	
/**************************/
/* Configuration du CURL  */
/**************************/
	function CURL($url){
		global $Config;
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($ch, CURLOPT_FAILONERROR, true);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__.'/cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__.'/cookie.txt');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Serveur Scodoc non accéssible depuis le net, donc vérification impossible
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_REFERER, $_SERVER['SERVER_NAME'] . '/?passerelle=' . $Config->passerelle_version);

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
	function Ask_Scodoc($url_query, $dep = '', $options = []){
		global $Config;
		$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

		$login = [
			'__ac_name' => $Config->scodoc_login,
			'__ac_password' => $Config->scodoc_psw
		];
		
		$data = http_build_query(array_merge($login, $options));
		
		if($dep != ''){
			$dep = '/'.$dep;
		}

		return CURL($Config->scodoc_url . "$dep$url_query?$data");
	}

/*******************************/
/* UEAndModules()
Liste les UE et modules d'un département + semestre

Entrées : 
	$dep : [string] département - exemple : MMI
	$sem : [string] code semestre Scodoc - exemple : 871

Sortie :
	[
		{
			UE: "UE1 nom de l'UE",
			modules: [
				{
					"titre": "nom du module 1",
					"code": "W511" // Code scodoc du module
				},
				etc.
			]
		},
		etc.
	]

*******************************/
function UEAndModules($dep, $sem){
	$json = json_decode(Ask_Scodoc(
		'/Scolarite/Notes/formsemestre_description',
		$dep,
		[
			'formsemestre_id' => $sem,
			'format' => 'json'
		]
	));

	array_pop($json); // Supprime le récapitulatif de toutes les UE
	$output_json = [];

	/* 
	Listes des UE et des Modules les uns après les autres 
	Données dispo :
		Code: 'W511',				// null si c'est une UE
		Coef.: '0.5',				// null si c'est une UE
		Inscrits: '12',				// null si c'est une UE
		Module: 'Ecriture numérique',
		Responsable: 'Graef D.',	// null si c'est une UE
		UE: 'UE1 Culture Com &amp; Entreprise',
	*/
	foreach($json as $value){
		if($value->Module != 'Bonus'){
			
			if($value->Responsable == NULL){
				$output_json[] = [
					'UE' => $value->UE,
					'modules' => []
				];
			}else{
				$output_json[count($output_json)-1]['modules'][] = [
					'titre' => $value->Module,
					'code' => $value->Code
				];
					
			}
		}
	}

	return $output_json;
}
