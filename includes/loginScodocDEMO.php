<?php
/****************************/
/* Logins pour accéder à Scodoc
	Il faut créer un compte par département qui fini par la nom du département
	Exemple : login_MMI
	
	Pour des raisons de sécurité, il est recommandé que ce compte ait le statut 'secrétariat' l'accès sera alors uniquement en lecture 
*/

	$acces = [
		'__ac_name' => "un_compte_de_votre_choix_$dep", 
		'__ac_password' => 'un_super_mot_de_passe_de_votre_choix'
	];

	$scodoc_url = 'https://scodoc.uha.fr';
?>