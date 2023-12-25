<?php
/***************************************/
/* Service de création de tocken JWT 
	pour des TP étudiants			   */
/* https://github.com/firebase/php-jwt */
/***************************************/

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	
	include_once "$path/includes/default_config.php";

	if($Config->JWT_key == ''){
		die("Le service de jetons JWT n'est pas activés.");
	}

	include_once "$path/includes/user.class.php";
	$user = new User();

	use \Firebase\JWT\JWT;

	include $path . '/lib/JWT/JWT.php';

	/**************************************************/
	/* Configuration de la durée de validité du jeton */
	/**************************************************/
	// Par défaut une semaine.
	$nb_jours = 7;
	$nb_heures = 0;
	
	/*************************************************/
	
	$duree_de_validite = 
		$nb_jours * 86400 +
		$nb_heures * 3600;

	$payload = [
		'id' => $user->getId(),
		'idCAS' => '',
		'name' => $user->getName(),
		'statut' => 'etudiant', 
		'exp' => time() + $duree_de_validite
	];

	$root_url = (isset($_SERVER["https"]) ? "https://" : "http://" ). $_SERVER["HTTP_HOST"];
	$token_url = $root_url."?token=".JWT::encode($payload, $Config->JWT_key);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Jeton d'accès</title>
	<style>
		<?php include $_SERVER['DOCUMENT_ROOT']."/assets/styles/global.css"?>
		.jeton {
			background: #fff;
			color: #000;
			border: 1px solid #aaa;
			padding: 4px 16px;
			width: 400px;
			text-decoration: none;
			display: inline-block;
			word-break: break-all;
		}
		button {
			font-size: 16px;
			border: none;
			box-shadow: var(--box-shadow);
			padding: 4px 16px;
			background: var(--accent);
			color: #FFF;
			cursor: pointer;
		}
		button:hover {
			box-shadow: 0 2px 2px #444;
		}
		button:active {
			box-shadow: 0 0px 0px #444;
			transform: translateY(2px)
		}
	</style>
</head>
<body>
	<header>
		<h1>Jeton d'accès</h1>
	</header>
	
	<main>
		<p>
			Cette page vous permet de récupérer un lien contenant un jeton d'accès.<br>
			Ce jeton est valide pendant <b><?php echo "$nb_jours jour(s) et $nb_heures heure(s)"; ?></b>.
		</p>
		<p>
			<b>Attention</b>, ce jeton est personnel et donne accès à votre compte sans avoir besoin de se connecter.
		</p>

		<div class=jeton>
			<?php echo $token_url; ?>
		</div>
		<button>Copier</button>
	</main>
	<script>
		document.querySelector("button").addEventListener("click", function(){
			navigator.clipboard.writeText(
				document.querySelector(".jeton").innerText
			);
			this.innerText = "Copié !";
		})
	</script>
</body>
</html>