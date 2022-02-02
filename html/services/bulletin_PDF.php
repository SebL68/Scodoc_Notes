<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/default_config.php";
	include_once "$path/includes/annuaire.class.php";		// Class Annuaire
	include_once "$path/includes/serverIO.php";
	include_once "$path/includes/user.class.php";
	$user = new User();

	if($user->getStatut() >= PERSONNEL){ 
		$nip = $_GET['etudiant'];
	} else {
		$nip = Annuaire::getStudentNumberFromIdCAS($user->getSessionName());
	}
	$dep = getStudentDepartment($nip);

/**************************/
/* Envoi du relevé au format PDF */
/**************************/
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

