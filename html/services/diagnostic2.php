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
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
	/********************/
	/* CAS */
	/********************/
		include_once "$path/config/cas_config.php";

		echo "<div><span>💭</span><div>L'authentification au CAS renvoie :<br><br>";

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
			echo '<b>'.phpCAS::getUser().'</b>';

		}else{
			echo "<b>*** Vous n'êtes pas authentifié ***</b> => <a href=/services/doAuth.php?href=https://".$_SERVER['HTTP_HOST'].">Authentification</a><br><br>";
		}

		echo '<br><br>Pour plus de tests sur le CAS, allez sur cette page <a href="/code_test/testCAS.php?-no-sw">Test CAS</a><br><br>';

		include_once "$path/includes/default_config.php";

		echo " Est-ce bien ";
		if ($Config->CAS_return_type == 'nip') {
			echo '<b>un numéro d\'étudiant ou quelque chose de proche ?</b>.';
		} else {
			echo '<b>une autre valeur que le numéro d\'étudiant</b> ?<br>Dans ce cas, il vous faudra certainement configurer le LDAP.';
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
		
		/* Test liaison Scodoc */
		$ch = curl_init($Config->scodoc_url);
		curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
		curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT,1);	// Scodoc devrait répondre en moins d'une seconde
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
		curl_close($ch);
		
		if ($httpcode == 200) {
			echo '<div><span>✔️</span> La communication entre le serveur passerelle et le serveur Scodoc est fonctionnelle.</div>';
		} else {
			echo '<div class=wrong><span>❌</span> La communication entre le serveur passerelle et le serveur Scodoc est fonctionnelle, le code retourné est <b>' . $httpcode . '</b><br></div>';
			die();
		}
		
		echo '<div><span>💭</span> L\'authentification a Scodoc nécessite un compte avec les authorisations "Secr" sur l\'ensemble des départements : vérifiez que ce compte soit bien créé dans Scodoc.</div>';
		if($Config->scodoc_login != 'LOGIN_SCODOC' && $Config->scodoc_psw != 'MDP_SCODOC') {
			echo '<div><span>✔️</span> Vous avez configuré un login et mot de passe pour vous authentifier à Scodoc.</div>';
		} else {
			echo '<div class=wrong><span>❌</span>Veuillez configurer le login et le mot de passe pour vous authentifier à Scodoc.</div>';
			die();
		}

		/*Auth à Scodoc*/
		include_once "$path/includes/scodoc.class.php";
		try{
			$Scodoc = new Scodoc();
		} catch(Exception $e) {
			echo '<div class=wrong><span>❌</span> Il semblerait que l\'authentification auprès de Scodoc ait échoué. Vérifiez que le compte que vous avez créé a bien un accès "Scre" à l\'ensemble des départements.</div>';
			die();
		}

		echo '<div><span>✔️</span> L\'authentification auprès de Scodoc a réussi.</div>';

		/* Récupération de données Scodoc */
		echo '<div><span>💭</span> Essai de récupération de données scodoc, vous devriez voir apparaître la liste des départements:</div>';

		echo '<div><b><pre>' . json_encode($Scodoc->getDepartmentsList(), JSON_PRETTY_PRINT) . '<pre></b></div>';

		/*******************/
		echo '<div><span>💭</span> Une option Scodoc permet de choisir de diffuser ou non les relevés pour chaque département.</div>';

	?>

	<h2>En option : LDAP</h2>
	<?php
	/********************/
	/* Lien avec Scodoc */
	/********************/
		echo "<div>Tests en cours de rédactions ...</div>";
	?>
</body>
</html>