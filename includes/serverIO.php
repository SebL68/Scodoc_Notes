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
		$patch - optionnel : [bool] retourne le résultat tel quel, sans modification - defaut : true

		Retour : [string] du résultat
****************************/
	function Ask_Scodoc($url_query, $dep = '', $options = [], $patch = true){
	
		include 'loginScodoc.php';

		$data = http_build_query(array_merge($acces, $options));
		
		if($dep != ''){
			$dep = '/'.$dep;
		}

		if(!$patch){
			return CURL("$scodoc_url/ScoDoc$dep$url_query?$data");
		} else {
			return
/* Patch pour résouvre le fichier scodoc non valide formsemestre_list */
				str_replace (
					[ // https://www.utf8-chartable.de/unicode-utf8-table.pl?start=128&number=128&utf8=string-literal&unicodeinhtml=hex
						'\\xc3\\xa9',
						'\\xc3\\xb4',
						'\\xc3\\xa0',
						'\\xc3\\xa8',
						'\\xc3\\xaa',
						'\\xc3\\xa7',
						'\\xc3\\xb9'
					],
					[
						'é',
						'ô',
						'à',
						'è',
						'ê',
						'ç',
						'ù'
					],
					preg_replace(
						'~"etapes": \[.*\], ~U',
						'',
						fixJSON(
/********* Fin du patch *************/
							html_entity_decode(
								CURL("https://iutmscodoc9.uha.fr/ScoDoc$dep$url_query?$data")
							)
						)
					)
				)
			;	
		}
	}

/**********************/
/* Pour résoudre le problème du JSON non valide avec des '...' */
/**********************/
	function fixJSON($json) {
		$regex = <<<'REGEX'
~
	"[^"\\]*(?:\\.|[^"\\]*)*"
	(*SKIP)(*F)
	| '([^'\\]*(?:\\.|[^'\\]*)*)'
~x
REGEX;
	
		return preg_replace_callback($regex, function($matches) {
			return '"' . preg_replace('~\\\\.(*SKIP)(*F)|"~', '\\"', $matches[1]) . '"';
		}, $json);
	}
?>