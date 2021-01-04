<?php
include_once 'php_header.php';
$formsemestre_id = '&formsemestre_id='.$_GET["sem_id"];

/**************************/
/* Recherche de l'id étudiant scodoc à partir du nom */
/*************************/
$handle = fopen("../etudiants/export_etu_iutmulhouse.txt", "r");
while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
	if( strcasecmp($data[21], $id) == 0){	
		$nip="&code_nip=$data[2]";
		$nip[10] = '2';
		break;	
	}
}
if(!isset($nip)){
	//header('Location: probleme.php?recup');
	
	die('"problème nip"');
}

/**************************/
/* Récupération du département - gestion du login */
/**************************/

$dep = CURL( $sco_url."get_etud_dept?$nip");

$login = "acces_notes_$dep";//__ac_name
$mdp = 'IUT_chouette';// __ac_password

$log_str = "&__ac_name=$login&__ac_password=$mdp";


/**************************/
/* Envoi du bulletin */
/**************************/
	$result = CURL("$sco_url$dep/Scolarite/Notes/Notes/formsemestre_bulletinetud?$formsemestre_id$nip$log_str&format=json&version=long");

	echo(str_replace('&apos;', '\'', $result));

?>