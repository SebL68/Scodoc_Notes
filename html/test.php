<?php 
	require '../config/authentification.class.php';

	$auth = new Auth();
	
	echo $auth->getSessionName() . " " . $auth->getStatut();
?>