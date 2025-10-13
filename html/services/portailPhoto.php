<?php

/*************************************************************/
/* Outil pour faire le lien entre le portail photo et Scodoc */
/*************************************************************/
// Pour le moment ce n'est que utilisable à l'UHA.

$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
include_once "$path/includes/default_config.php";
include_once "$path/includes/annuaire.class.php";

$nip = $_GET['nip'];

/* Vérifier que c'est bien le serveur Scodoc en utilisant l'IP */
if($_SERVER['REMOTE_ADDR'] != $Config->ipScodoc) {
	http_response_code(403);
	die('Non autorisé');
}

// Transformer le NIP en idCAS (pour Mulhouse)
$id = Annuaire::getStudentIdCASFromNumber($nip);

if($id == NULL) {
	http_response_code(404);
	die('Not found');
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, str_replace('$id', $id, $Config->urlPhoto));

$options = array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_SSL_VERIFYHOST => false
);
curl_setopt_array($ch, $options);

$output = curl_exec($ch);

if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 404) {
	http_response_code(404);
	die('Not found');
}

header("Content-Type: image/jpeg");
echo $output;