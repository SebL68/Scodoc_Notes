<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/default_config.php";
	include_once "$path/includes/annuaire.class.php";
	require_once "$path/includes/".$Config->service_data_class;		// Class service_data - typiquement Scodoc
	include_once "$path/includes/user.class.php";
	require_once "$path/includes/analytics.class.php";
	$user = new User();

	if(!$Config->releve_PDF){
		die('Cette opération n\'est au autorisée, malotru !');
	}

	if($user->getStatut() >= PERSONNEL){ 
		sanitize($_GET['etudiant']);
		$nip = $_GET['etudiant'];
	} else {
		$nip = $user->getId();
	}

	function sanitize($data){
		/* Nettoyage des entrées */
		if(preg_match('/\.\.|\\|\//', $data)){
			returnError('Données envoyées au serveur non valides - try to hack ?!');
		}
	}

/************************/
/* Relevé au format PDF */
/************************/
	sanitize($_GET["sem_id"]);
	sanitize($_GET["type"]);
	$Scodoc = new Scodoc();

	if($Config->liste_dep_publi_PDF != '') {
		$dep = $Scodoc->getStudentDepartment($nip);
		if(!in_array($dep, explode(",", $Config->liste_dep_publi_PDF))) {
			die('Votre département n\'autorise pas de récupérer le relevé au format PDF, vil gredin !');
		}
	}

	$result = $Scodoc->getReportCards(
		$_GET["sem_id"],
		$nip,
		'pdf',
		$_GET["type"]
	);


	if($result != ''){
		header('Content-type:application/pdf');
		header('Content-Disposition:attachment;filename=bulletin.pdf');		
		echo $result;
	}