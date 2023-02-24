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
		<?php include $_SERVER['DOCUMENT_ROOT']."/assets/styles/global.css"?>
		summary {
			margin: 20px 0 0 0;
			padding: 20px;
			background: #0C9;
			color: #FFF;
			border-radius: 10px;
			cursor: pointer;
			font-size: 1.5em;
			font-weight: bold;
		}
		label:has(input[type=checkbox]) {
			background: #FFF;
			border: 1px solid #aaa;
			display: block;
			align-items: flex-start;
			gap: 8px;
			padding: 4px 16px;
			border-radius: 4px;
			margin-bottom: 4px;
			cursor: pointer;
			position: relative;
		}
		label:has(input[type=checkbox]):hover {
			border: 1px solid #c09;
			outline: 1px solid #c09;
		}
		label>b{
			flex: none;
		}
		.done::before {
			content: "✔️";
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			border-radius: 4px;
			background: rgba(0,0,0,0.6);
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 32px;
		}
		.problem::before {
			content: "❌";
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

		<div class="contenu">
			<!--<details>
				<summary>Configuration du CAS</summary>
				<div></div>
			</details>
			<details>
				<summary>Lien avec Scodoc</summary>
				<div></div>
			</details>
			<details>
				<summary>Configuration LDAP</summary>
				<div>
					<a href="#" onClick="exeCmd('updateLists')">UpdateLists</a>
					<a href="#" onClick="exeCmd('setUpdateLists')">setUpdateLists</a>
				</div>
			</details>
			<details>
				<summary>Fonctionnalités de la passerelle</summary>
				<div></div>
			</details>
			<details>
				<summary>Configuration de l'onglet "Comptes"</summary>
				<div></div>
			</details>
			<details>
				<summary>Affichage</summary>
				<div></div>
			</details>
			<details>
				<summary>Absences</summary>
				<div></div>
			</details>
			<details>
				<summary>Autre</summary>
				<div></div>
			</details>-->

			<p><b>Ce menu n'est accessible qu'aux super-administrateurs et permet de changer la configuration de la passerelle.</b></p>
			<label>
				<input type="checkbox" name="releve_PDF">
				<b>Relevé PDF</b>
				<p>Permettre aux étudiants de télécharger un relevé de notes intermédiaire en PDF.</p>	
				<p>Ca leur permet d'avoir un historique de leurs notes et également d'avoir à disposition un relevé intermédiaire pour les poursuites d'études.</p>
			</label>
			<label>
				<input type="checkbox" name="acces_enseignants">
				<b>Acces enseignant</b>
				<p>
					Permettre aux enseignants de :
					<ul>
						<li>voir les notes de n'importe quels étudiants,</li>
						<li>obtenir des documents bien pratiques,</li>
						<li>activer le mode absences.</li>
					</ul>
				</p>
				
				<p>Nécessite de compléter les listes dans "Comptes" ou d'activer le LDAP.</p>
			</label>
			<label>
				<input type="checkbox" name="module_absences">
				<b>Module absences</b>
				<p>Activer le module de saisi des absences</p>
				<p>Ce module est spécifique à la passerelle et n'est pas connecté avec Scodoc pour le moment.</p>
			</label>
			<label>
				<input type="checkbox" name="afficher_absences">
				<b>Afficher absences</b>
				<p>Activer la zone de visualisation des absences sous le relevé de notes.</p>
			</label>
		</div>

		<div class=wait></div>

	</main>

	<div class=auth>
		<!-- Site en maintenance -->
		Authentification en cours ...
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
	<script>
		checkStatut();
		getConfig();
		<?php
			include "$path/includes/clientIO.php";
		?>
		document.querySelector("#config").classList.add("navActif");
		/***************************************************/
		/* Vérifie l'identité de la personne et son statut */
		/***************************************************/
		async function checkStatut() {
			let data = await fetchData("donnéesAuthentification");
			
			let auth = document.querySelector(".auth");
			auth.style.opacity = "0";
			auth.style.pointerEvents = "none";
			
			if (config.statut < SUPERADMINISTRATEUR) {
				document.querySelector(".contenu").innerHTML = "Ce contenu est uniquement accessible pour des super administrateurs. ";
				return;
			}
		}

		async function getConfig() {
			let data = await fetchData("getAllConfig");

			document.querySelectorAll("[type=checkbox]").forEach(input=>{
				input.checked = data[input.name];
				input.addEventListener("input", setConfig)
			})

			//console.log(data.fct_nameFromIdCAS);
		}

		async function setConfig(){
			let data = await fetchData(`setConfig&key=${this.name}&value=${this.checked}`);
			if(data.resultat == "ok") {
				this.classList.add("done");
				setTimeout(() => {
					this.classList.remove("done")	
				}, 600);
			} else {
				this.classList.add("done", "problem");
				displayError("Une erreur est survenue - mais je ne sais pas quoi.");
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