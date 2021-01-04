<?php
	include_once 'php_header.php';	
	$formsemestre_id = 'formsemestre_id='.$_GET["sem_id"];


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
	
	die('problème nip');
}

/**************************/
/* Récupération du département - gestion du login */
/**************************/

$dep = CURL( $sco_url."get_etud_dept?$nip");

$login = "acces_notes_$dep";//__ac_name
$mdp = 'IUT_chouette';// __ac_password

$log_str = "&__ac_name=$login&__ac_password=$mdp";
	
/**************************/
/* Envoi du relevé au format PDF */
/**************************/
	$result = CURL("https://iutmscodoc9.uha.fr/ScoDoc/$dep/Scolarite/Notes/Notes/formsemestre_bulletinetud?$formsemestre_id$nip$log_str&format=pdf&version=long");

	if($result != ''){
		header('Content-type:application/pdf');
		header('Content-Disposition:attachment;filename=bulletin.pdf');		
		echo $result;
	}else{
		// header('Location: probleme.php?recup');
		header("Location: probleme.php?id=$id&dep=$dep&nip=$nip&probleme=pasDeReleveARecup"); // Pas inscrit ?
		die();
	}
?>

