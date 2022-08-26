<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/default_config.php";
	include_once "$path/includes/annuaire.class.php";
	include_once "$path/includes/scodoc.class.php";
	include_once "$path/includes/user.class.php";
	require_once "$path/includes/analytics.class.php";
	$user = new User();

	if(!$Config->releve_PDF){
		die('Cette opération n\'est au autorisée, malotru !');
	}

	if($user->getStatut() >= PERSONNEL){ 
		$nip = $_GET['etudiant'];
	} else {
		$nip = $user->getId();
	}

/************************/
/* Relevé au format PDF */
/************************/
	$Scodoc = new Scodoc();

	$result = $Scodoc->getReportCards(
		$_GET["sem_id"],
		$nip,
		'pdf'
	);


	if($result != ''){
		header('Content-type:application/pdf');
		header('Content-Disposition:attachment;filename=bulletin.pdf');		
		echo $result;
	}
?>

