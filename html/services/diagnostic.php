<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Diagnostic</title>
	<style>
		<?php include $_SERVER['DOCUMENT_ROOT']."/assets/styles/global.css"?>

		body{
			background: var(--fond-inverse);
			color: var(--contenu-inverse);
			font-family: arial;
			margin: 0;
		}
		h1{margin-left: 64px;}
		h2{
			background: var(--primaire);
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
	<script>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/js/theme.js" ?>
	</script>
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
			echo '<div><span>✔️</span> L\'extension CURL est bien chargée.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> Veuillez installer l\'extension php-curl.</div>';
		}
		if(extension_loaded('OpenSSL')){
			echo '<div><span>✔️</span> L\'extension OpenSSL est bien chargée.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> Veuillez installer l\'extension OpenSSL.</div>';
		}
		if(extension_loaded('dom')){
			echo '<div><span>✔️</span> L\'extension DOM est bien chargée.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> Veuillez installer l\'extension DOM php-xml.</div>';
		}
		if(extension_loaded('ldap')){
			echo '<div><span>✔️</span> L\'extension LDAP est bien chargée.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> [OPTIONNEL] L\'extension LDAP n\'est pas installée.<br>Cette extension est nécessaire si vous avez besoin des fonctionnalitées liées au LDAP.<br>Le LDAP peut s\'avérer nécessaire si le CAS renvoie autre chose que le numéro d\'étudiant et pour automatiser la distinction entre les étudiants et les enseignants.</div>';
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
		if(file_exists("$path/config/cas_config.php")){
			include_once "$path/config/cas_config.php";

			echo "<div><span>💭</span> Vérifiez que c'est bien le CAS de votre université <a href=https://$cas_host>$cas_host</a></div>";
			echo "<div class=spaceUnder><span></span> ==> Si ce n'est pas le cas, changez la configuration de cas_config.php</div>";
			
			echo "<div><span>💭</span> Vérifiez que votre serveur à bien l'autorisation de se connecter au CAS : <a href=/services/doAuth.php?href=https://".$_SERVER['HTTP_HOST'].">Authentifiation</a></div>";
			echo "<div class=spaceUnder><span></span> ==> Si ce n'est pas le cas, demandez l'autorisation à votre service informatique.</div>";
			echo "<a href=diagnostic2.php?-no-sw>La suite sur la deuxième page.</a>";
		} else {
			echo "Le CAS ne peut pas être utilisé sans une bonne configuration de la racine.";
		}
	?>
</body>
</html>
