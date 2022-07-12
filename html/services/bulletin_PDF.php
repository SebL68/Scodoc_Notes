<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/default_config.php";
	include_once "$path/includes/annuaire.class.php";
	//include_once "$path/includes/scodoc.class.php";
	include_once "$path/includes/serverIO.php";
	include_once "$path/includes/user.class.php";
	$user = new User();

	if($user->getStatut() >= PERSONNEL){ 
		$nip = $_GET['etudiant'];
	} else {
		$nip = $user->getId();
	}

/*********************************/
/* Envoi du relevé au format PDF */
/*********************************/
	//$Scodoc = new Scodoc();

	///////// TODO il manque la route pour pouvoir récupérer le relevé version PDF
	/*$result = $Scodoc->getReportCards(
		$_GET["sem_id"],
		$nip,
		'pdf'
	);*/

	/////////////
	
die("Fonctionnalité momentanément désactivée");

	$result = Ask_Scodoc(
		'/Scolarite/Notes/formsemestre_bulletinetud',
		$dep,
		[ 
			'code_nip' => $nip,
			'formsemestre_id' => $_GET["sem_id"],
			'format' => 'pdf',
			'version' => 'long'
		],
		false
	);

	if($result != ''){
		header('Content-type:application/pdf');
		header('Content-Disposition:attachment;filename=bulletin.pdf');		
		echo $result;
	}
?>

