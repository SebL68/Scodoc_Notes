<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/config/config.php";
	include_once "$path/includes/LDAPData.php";
	include_once "$path/includes/serverIO.php";
	include_once "$path/includes/user.class.php";
	$user = new User();

	if($user->getStatut() >= PERSONNEL){ 
		$id = $_GET['etudiant'];
	} else {
		$id = $user->getSessionName();
	}

/**************************/
/* Recherche de l'id étudiant scodoc à partir du nom */
/*************************/
	$nip = getStudentNumberFromMail($id);

/**************************/
/* Récupération du département */
/**************************/
	$dep = Ask_Scodoc(
		'/get_etud_dept',
		'',
		[ 'code_nip' => $nip ]
	);

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

