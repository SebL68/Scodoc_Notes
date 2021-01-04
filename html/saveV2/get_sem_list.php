<?php
	include_once 'php_header.php';

/**************************/
/* Recherche de l'numéro de l'étudiant scodoc à partir du nom */
/*************************/
$handle = fopen('../etudiants/export_etu_iutmulhouse.txt', 'r');
while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
	if( strcasecmp($data[21], $id) == 0){	
		$nip="&code_nip=$data[2]";
		$nip[10] = '2';
		break;	
	}
}
if(!isset($nip)){
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
/* Demandes d'info sur l'étudiant à Scodoc */
/*************************/
$data_semestre = CURL("$sco_url$dep/Scolarite/Notes/etud_info?format=json$nip$log_str");

if($data_semestre != ''){
	$formsemestre_id = '&formsemestre_id='.json_decode($data_semestre)->insemestre[0]->formsemestre_id;

	$json = json_decode($data_semestre);
	$sem_output = array();

	for($i=0 ; $i<count($json->insemestre) ; $i++){
		array_push($sem_output, $json->insemestre[$i]->formsemestre_id);
	}
}else{
	//header('Location: probleme.php?dep='.$dep);

	die('"probleme recup semestre"');
}
echo(json_encode($sem_output));
?>