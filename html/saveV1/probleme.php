<?php
	ob_start("ob_gzhandler");
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width">
		<title>Relevé de notes</title>
		<!--<link rel="stylesheet" href="styles/style.css">-->
		<style>
			*{
				box-sizing: border-box;
			}
			body{
				margin:0;
				font-family:arial;
				background: #FAFAFA;
			}
			header{
				padding:10px;
				background:#09C;
				display: flex;
				justify-content: space-between;
				color:#FFF;
				box-shadow: 0 2px 2px #888;
			}
			header>a{
				color: #FFF;
				text-decoration: none;
				padding: 10px 0 10px 0;
			}
			h1{
				margin:0;
			}
			main{
				padding:0 10px;
			}
		</style>
	</head>
	<body>
		<header>

			<h1>
				Relevé de notes
			</h1>
			<a href=logout.php>Déconnexion</a>
		</header>
		<main>
			<h2>
				😭 Votre compte n'est pas encore enregistré dans ce département ou un problème est survenu lors de la récupération des données, réessayez ultérieurement ou contactez le responsable des notes.
			</h2>

			<?php
				if($_SESSION['enseignant'] == true){
			
					echo 'Le problème est :' . $_GET['probleme'];
			?>
				En fonction de ce problème, il se peut que :<br>
				<ul>
					<li>l'étudiant ne soit pas inscrit dans ce département,</li>
					<li>l'étudiant ne soit pas encore affecté dans Scodoc à un semestre valide,</li>
					<li>le numéro étudiant dans Scodoc ne soit pas valide, lors des exports APOGEE pour les inscriptions, certains numéro sont sous la forme xxxxxxxx.0, le ".0" n'est pas valide et doit être supprimé.</li>
				</ul>
			<?php
				}
			?>
		</main>

		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-126874346-1"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());
			gtag('config', '<GA_MEASUREMENT_ID>', { 'anonymize_ip': true });
			gtag('config', 'UA-126874346-1');
		</script>

	</body>
</html>