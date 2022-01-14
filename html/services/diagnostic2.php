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
	<h1>Diagnostique de la passerelle - page 2</h1>
	<h2>CAS</h2>
	<?php
		$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	/********************/
	/* CAS */
	/********************/
		
		include_once "$path/config/cas_config.php";

		echo "<div><span>💭</span><div>Si vous êtes déjà authentifié au CAS, ce dernier renvoie : <br><br>";

		require_once $path . '/lib/CAS/CAS.php';
		require_once $path . '/config/cas_config.php';

		// Initialize phpCAS
		phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
		if($cas_server_ca_cert_path != '') {
			phpCAS::setCasServerCACert($cas_server_ca_cert_path);
		} else {
			phpCAS::setNoCasServerValidation();
		}

		if(phpCAS::isAuthenticated()){
			// Utilisateur authentifié
			echo phpCAS::getUser();

		}else{
			echo "*** Vous n'êtes pas authentifié *** => <a href=/services/doAuth.php?href=https://".$_SERVER['HTTP_HOST'].">Authentifiation</a><br><br>";
		}

		include_once "$path/includes/default_config.php";

		echo "Est-ce bien ";
		if ($Config->CAS_return_type == 'nip') {
			echo '<b>un numéro d\'étudiant ?</b>.';
		} else {
			echo '<b>une autre valeur que le numéro d\'étudiant</b> ? Dans ce cas, il vous faudra certainement configurer le LDAP.';
		}
		echo '</div></div>';
		echo "<div class=spaceUnder><span></span> ==> Si ce n'est pas le cas, changez la configuration dans config.php</div>";

		echo "<div><span>💭</span> Vérifiez que le numéro d'étudiant donné par le CAS ou transformé par le LDAP correspond bien au numéro qui est dans Scodoc.</div>";
		echo "<div class=spaceUnder><span></span> ==> Si la valeur retourné par le CAS ne correspond pas, changez la fonction nipModifier() dans config.php</div>";

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
