<?php
  $path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
  include_once "$path/includes/default_config.php";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Administration</title>
	<style>
		<?php include $_SERVER['DOCUMENT_ROOT']."/assets/header.css" ?>

		/*********************/
		/* Affichage message */
		/*********************/
		.message {
			position: fixed;
			bottom: 100%;
			left: 50%;
			z-index: 101;
			padding: 20px;
			border-radius: 0 0 10px 10px;
			background: #ec7068;
			color: #FFF;
			font-size: 24px;
			animation: message 3s;
			transform: translate(-50%, 0);
		}

		@keyframes message {
			20%,
			80% {
				transform: translate(-50%, 100%)
			}
		}
	</style>
	<meta name=description content="Gestion des administrateurs de l'<?php echo $Config->nom_IUT; ?>">
</head>

<body>
	<?php 
    	$h1 = 'Configuration';
    	include $_SERVER['DOCUMENT_ROOT']."/assets/header.php";
  	?>
	<main>
		<p>
			Bonjour <span class=nom></span>.
		</p>

		<div class=wait></div>

	</main>

	<div class=auth>
		<!-- Site en maintenance -->
		Authentification en cours ...
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
	<script>
		var utilisateur;        // Stockage de l'identifiant de l'utilisateur
		var statut;             // Stockage du statut de l'utilisateur
		var selectDep;          // Stockage du département sélectionné
		checkStatut();
		<?php
			include "$path/includes/clientIO.php";
		?>
		document.querySelector("#admin").classList.add("navActif");
		/***************************************************/
		/* Vérifie l'identité de la personne et son statut */
		/***************************************************/
		async function checkStatut() {
			let data = await fetchData("donnéesAuthentification");
			document.querySelector(".nom").innerText = data.name;
			utilisateur = data.session;
			let auth = document.querySelector(".auth");
			auth.style.opacity = "0";
			auth.style.pointerEvents = "none";

			if (statut >= SUPERADMINISTRATEUR) {    // Ajout des fonctionnalités pour SuperAdministrateur
				document.querySelector("nav").innerHTML += `
					<a class="nav" href="#" onClick="exeCmd('updateLists')">UpdateLists</a>
					<a class="nav" href="#" onClick="exeCmd('setUpdateLists')">setUpdateLists</a>
				`;
			} else {
				document.querySelector(".contenu").innerHTML = "Ce contenu est uniquement accessible pour des super administrateurs. ";
			}
		}

		/***************************************************/
		/* Exécution de commandes pour SuperAdministrateur */
		/***************************************************/
		async function exeCmd(commande) {
			let result = await fetchData(commande);
			console.log(result);
		}


		/**************************/
		/* Affichage d'un message */
		/**************************/
		function message(msg) {
			var div = document.createElement("div");
			div.className = "message";
			div.innerHTML = msg;
			document.querySelector("body").appendChild(div);
			setTimeout(() => {
				div.remove();
			}, 3000);
		}
	</script>
	<?php
 		include "$path/config/analytics.php";
  	?>
</body>
</html>