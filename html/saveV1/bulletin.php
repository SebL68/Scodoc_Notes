<?php
	ob_start("ob_gzhandler");
	session_start();
/**************************/
/* Gestion du CAS */
/**************************/
	if(!isset($_SESSION['id'])){
		require_once '../CAS/include/CAS.php';
		require_once '../CAS/config/cas_uha.php';

		// Initialize phpCAS
		phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
			
		// force CAS authentication
		phpCAS::setNoCasServerValidation() ;
		//phpCAS::setCasServerCACert($cas_server_ca_cert_path);
		phpCAS::forceAuthentication(); 
		
		$_SESSION['id'] = phpCAS::getUser();
		$id = $_SESSION['id'];
	}elseif($_SESSION['id'] !== ''){
		$id = $_SESSION['id'];
	}
	
	if(!isset($_SESSION['enseignant'])){
		header("Location: /");
		die();
	}
	if($_SESSION['enseignant'] == true){
		$id = $_GET['etudiant'];
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

/**************************/
/* Configurations */
/**************************/	
	$dep = $_GET['departement'];
	
	$login = "acces_notes_$dep";//__ac_name

	$mdp = 'IUT_chouette';// __ac_password
	$log_str = "&__ac_name=$login&__ac_password=$mdp";
	
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
		header("Location: probleme.php?id=$id&dep=$dep&nip=false&probleme=pasDansLDAP");
		die();
	}

/**************************/
/* Demandes d'info sur l'étudiant à Scodoc */
/*************************/
	$data_semestre = CURL("https://iutmscodoc9.uha.fr/ScoDoc/$dep/Scolarite/Notes/etud_info?format=json$nip$log_str");
	if($data_semestre != ''){
		$formsemestre_id = '&formsemestre_id='.json_decode($data_semestre)->insemestre[0]->formsemestre_id;
	}else{
		//header('Location: probleme.php?dep='.$dep);
		header("Location: probleme.php?id=$id&dep=$dep&nip=$nip&probleme=pasDansScodocPourCeDep");
		die();
	}
	
/**************************/
/* Envoi du bulletin */
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

