<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Diagnostic</title>
	<style>
		body{
			background: #111;
			color: #FFF;
			font-family: arial;
			margin: 0;
		}
		h1{margin-left: 64px;}
		h2{
			background: #09c;
			padding: 8px 64px;
		}
		body>div{
			margin-left: 64px;
			display: flex;
			align-items: baseline;
			gap: 8px;
			max-width: 1080px;

		}
		body>div>span{
			width: 24px;
		}
		.wrong{
			margin-top: 8px;
			margin-bottom: 8px;
		}
		.stop{
			font-weight: bold;
			margin-top: 32px;
		}
		a{
			padding: 4px 8px;
			background: #909;
			color: #FFF;
			border-radius: 4px;
			text-decoration: none;
		}
		b{
			color: #909;
		}
		.spaceUnder{
			margin-bottom: 16px;
		}
	</style>
</head>
<body>
	<h1>Diagnostique de la passerelle</h1>
	<h2>Configuration du serveur</h2>
	<?php
		$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
		
	/******************/
	/* Version de PHP */
	/******************/
		if (version_compare(PHP_VERSION, '7.1.0') >= 0) {
			echo '<div><span>✔️</span> La version de PHP est compatible avec la passerelle.</div>';
		} else {
			echo '<div><span>❌</span> Veuillez mettre à jour votre version de PHP, le minimum requis est 7.1.0</div>';
		}

	/******************/
	/* Extensions PHP */
	/******************/	
		if(extension_loaded('CURL')){
			echo '<div><span>✔️</span> L\'extention CURL est bien chargée.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> Veuillez installer l\'extention CURL.</div>';
		}
		if(extension_loaded('OpenSSL')){
			echo '<div><span>✔️</span> L\'extention OpenSSL est bien chargée.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> Veuillez installer l\'extention OpenSSL.</div>';
		}
		if(extension_loaded('dom')){
			echo '<div><span>✔️</span> L\'extention DOM est bien chargée.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> Veuillez installer l\'extention DOM.</div>';
		}
		if(extension_loaded('ldap')){
			echo '<div><span>✔️</span> L\'extention LDAP est bien chargée.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> [OPTIONNEL] L\'extention LDAP n\'est pas installée.<br>Cette extention est nécessaire si vous avez besoin des fonctionnalitées liées au LDAP.<br>Le LDAP peut s\'avérer nécessaire si le CAS renvoie autre chose que le numéro d\'étudiant et pour automatiser la distinction entre les étudiants et les enseignants.</div>';
		}
		if($_SERVER['HTTPS']){
			echo '<div><span>✔️</span> Votre serveur à bien le SSL / TLS de configuré.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> Votre serveur n\'a pas le SSL / TLS de configurer, dans certains cas, ça peut poser des problèmes, comme par exemple pour l\'utilisation du CAS.</div>';
		}

	/**************************/
	/* Configuration du vhost */
	/**************************/
		if(file_exists("$path/includes/default_config.php")){
			include_once "$path/includes/default_config.php";
			echo '<div><span>✔️</span> La racine du site est bien configurée.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> La racine du site n\'est pas configurée correctement : elle doit pointer vers le répertoire html.<br> Veuillez configurer le fichier httpd-vhosts.conf (si vous utilisez Apache).</div>';

			die('<div class=stop>Arrêt des tests ...<br>Suite après la configuration du serveur.</div>');
		}		
	?>

	<h2>CAS</h2>
	<?php
	/********************/
	/* CAS */
	/********************/
		include_once "$path/config/cas_config.php";

		echo "<div><span>☑️</span> Vérifiez que c'est bien le CAS de votre université <a href=https://$cas_host>$cas_host</a></div>";
		echo "<div class=spaceUnder><span></span> ==> Si ce n'est pas le cas, changez la configuration de cas_config.php</div>";
		
		echo "<div><span>☑️</span> Vérifiez que votre serveur à bien l'authorisation de se connecter au CAS : <a href=/services/doAuth.php?href=https://".$_SERVER['HTTP_HOST'].">Authentifiation</a></div>";
		echo "<div class=spaceUnder><span></span> ==> Si ce n'est pas le cas, demandez l'authorisation à votre service informatique.</div>";

		echo "<div><span>☑️</span><div>La passerelle attend du CAS ";
		if ($Config->CAS_return_type == 'nip') {
			echo '<b>un numéro d\'étudiant</b>.';
		} else {
			echo '<b>une autre valeur que le numéro d\'étudiant</b>. Dans ce cas, il vous faudra certainement configurer le LDAP.';
		}
		echo '</div></div>';
		echo "<div class=spaceUnder><span></span> ==> Si ce n'est pas le cas, changez la configuration dans config.php</div>";

		if ($Config->CAS_return_type == 'nip') {
			echo "<div><span>☑️</span> Vérifiez que le numéro d'étudiant donné par le CAS correspond bien au numéro qui est dans Scodoc.</div>";
			echo "<div class=spaceUnder><span></span> ==> Si ce n'est pas le cas, changez la fonction nipModifier() dans config.php</div>";
		}

		if ($cas_server_ca_cert_path != '') {
			echo '<div><span>✔️</span> Vous avez configuré un certificat pour le CAS.</div>';
		} else {
			echo '<div><span>🔞</span> Vous n\'avez pas configuré le certificat pour le CAS, ce n\'est pas obligatoire, mais fortement recommandé pour améliorer la sécurité du site.</div>';
		}
	?>
	<h2>Lien avec Scodoc</h2>
	<?php
	/********************/
	/* Lien avec Scodoc */
	/********************/
		include_once "$path/includes/scodoc.class.php";
		/*error_reporting(E_ALL);
		ini_set('display_errors', '1');*/

		/*$Scodoc = new Scodoc();
		echo $Scodoc->getToken();
		echo $Scodoc->Ask_Scodoc('list_depts');*/
		echo "Tests en cours de rédactions ...";
	?>

	<h2>En option : LDAP</h2>
	<?php
	/********************/
	/* Lien avec Scodoc */
	/********************/
		echo "Tests en cours de rédactions ...";
	?>
</body>
</html>
