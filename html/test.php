<?php 
	require '../includes/authentification/authentification.class.php';

	$auth = new Auth();
	
	echo $auth->getSessionName() . " " . $auth->getStatut();

?>