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
			margin: 20px 0 8px 0;
			padding: 20px;
			background: var(--secondaire);
			color: var(--secondaire-contenu);
			border-radius: 10px;
			cursor: pointer;
			font-size: 1.5em;
			font-weight: bold;
		}
		label {
			background: var(--fond-clair);
			border: 1px solid var(--gris-estompe);
			display: block;
			align-items: flex-start;
			gap: 8px;
			padding: 16px 16px 0 16px;
			border-radius: 4px;
			margin-bottom: 4px;
			cursor: pointer;
			position: relative;
		}
		label:has(input[type=checkbox]):hover, 
		label:hover>input[type=text],
		label:hover>input[type=number] {
			border: 1px solid var(--accent);
			outline: 1px solid var(--accent);
		}
		label>b{
			flex: none;
		}
		input[type=text],
		input[type=number] {
			display: block;
			margin: 8px 0;
			padding: 4px 8px;
			border-radius: 4px;
			font-size: 16px;
			border: 1px solid var(--gris);
		}
		input[type=text] {
			width: 100%;
		}
		label:has(.done)::before {
			content: "✔️";
			position: absolute;
			top: 0;
			right: 0;
			padding: 4px 4px 6px;
			border-radius: 4px;
			background: rgba(0,0,0,0.6);
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 24px;
		}
		label:has(.problem)::before {
			content: "❌";
		}
		img{
			display: block;
			margin: 0 auto 8px auto;
			border: 2px dashed;
		}
		h3{
			color: var(--primaire);
		}
	</style>
	<meta name=description content="Gestion des administrateurs - <?php echo $Config->nom_IUT; ?>">
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
			<!--
			<details>
				<summary>Fonctionnalités de la passerelle</summary>
				<div></div>
			</details>
			-->
			<p><b>Ce menu n'est accessible qu'aux super-administrateurs et permet de changer les options de la passerelle.</b></p>

		<!-- ------- -->
		<!-- Serveur -->
		<!-- ------- -->
			<details>
				<summary>Serveur</summary>
				<div>
					<label>
						<b>🖊️ Nom de l'établissement</b>
						<input type="text" name="nom_IUT">
						
						<p>Utiliser dans les balises meta description. <br>Par exemple : IUT de Mulhouse</p>
					</label>

					<label>
						<input type="checkbox" name="analystics_interne">
						<b>Sauvegarde des données de connexion</b>
						<p>Système interne à la passerelle pour l'analyse du trafic compatible RGPD, les données seront visible dans ce <a href="/services/analytics.php">tableau de bord</a>. 
						</p>
						<p><b>⚠️ Exploitation des données et création des graphiques non implémentés pour le moment - avis aux amateurs.</b></p>
					</label>
					
					<label>
						<input type="checkbox" name="analyse_temps_requetes">
						<b>Statistiques du temps de réponse de Scodoc.</b>
						<p>Les données sont dans /data/analytics/temps_requetes.csv</a>.</p>
					</label>
				</div>
			</details>

		<!-- --------------- -->
		<!-- Relevé de notes -->
		<!-- --------------- -->
			<details>
				<summary>Relevé de notes</summary>
				<div>
					<label>
						<input type="checkbox" name="releve_PDF">
						<b>Relevé PDF</b>
						<p>Permettre aux étudiants de télécharger un relevé de notes intermédiaire en PDF.</p>	
						<p>Ca leur permet d'avoir un historique de leurs notes et également d'avoir à disposition un relevé intermédiaire pour les poursuites d'études.</p>
					</label>
					<label>
						<b>🖊️ Message non publication relevé</b>
						<input type="text" name="message_non_publication_releve">
					</label>
				</div>
			</details>

		<!-- ----------- -->
		<!-- Enseignants -->
		<!-- ----------- -->
			<details>
				<summary>Enseignants</summary>
				<div>
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
						
						<p>💡 Nécessite de compléter les listes dans "Comptes" ou d'activer le LDAP.</p>
					</label>
				</div>
			</details>
			
		<!-- ------- -->
		<!-- Comptes -->
		<!-- ------- -->
			<details>
				<summary>Onglet Comptes</summary>
				<div>
					<img src="/images/comptes.png" alt="Compte">

					<h3>Zone Nom Prénom</h3>
					<label>
						<b>🖊️ Expression régulière de vérification du nom</b>
						<input type="text" name="nameReg">
						
						<p>Permet de forcer une manière de nommer.<br>Par défaut on accepte tout : ^.+$</p>
					</label>
					<label>
						<b>🖊️ Placeholder pour le nom</b>
						<input type="text" name="namePlaceHolder">
						
						<p>Par exemple : "Nom Prénom de l'utilisateur"</p>
					</label>
					<label>
						<b>🖊️ Bulle info d'aide pour le nom</b>
						<input type="text" name="nameInfo">
						
						<p>Par exemple : "Indiquez le nom"</p>
					</label>

					<h3>Zone identifiant CAS</h3>
					<label>
						<b>🖊️ Expression régulière de vérification de l'identifiant CAS</b>
						<input type="text" name="idReg">
						
						<p>Par défaut on accepte tout : ^.+$</p>
					</label>
					<label>
						<b>🖊️ Placeholder pour l'identifiant</b>
						<input type="text" name="idPlaceHolder">
						
						<p>Par exemple : "Identifiant CAS" ou "Adresse mail"</p>
					</label>
					<label>
						<b>🖊️ Bulle info d'aide pour l'identifiant</b>
						<input type="text" name="idInfo">
						
						<p>Par exemple : "Entrez l'identifiant CAS"</p>
					</label>
				</div>
			</details>
		<!-- -------- -->
		<!-- Absences -->
		<!-- -------- -->
			<details>
				<summary>Absences</summary>
				<div>
					<label>
						<input type="checkbox" name="module_absences">
						<b>Module absences</b>
						<p>Activer le module de saisi des absences</p>
						<p>Ce module est spécifique à la passerelle et n'est pas connecté avec Scodoc pour le moment.</p>
						<p>💡 Nécessite le mode enseignant.</p>
					</label>
					<label>
						<input type="checkbox" name="afficher_absences">
						<b>Afficher absences</b>
						<p>Activer la zone de visualisation des absences sous le relevé de notes.</p>
					</label>

					<label>
						<b>🖊️ Heure de début des absences</b>
						<input type="number" min=0 max=24 required name="absence_heureDebut">
						<p>Pour une demi heure, utiliser 0.5, par exemple : 8h30 -> 8.5</p>
					</label>
					<label>
						<b>🖊️ Heure de fin des absences</b>
						<input type="number" min=0 max=24 required name="absence_heureFin">
						<p>Pour une demi heure, utiliser 0.5, par exemple : 17h30 -> 17.5</p>
					</label>
					<label>
						<b>🖊️ Échelonnement des créneaux</b>
						<input type="number" min=0 max=24 required name="absence_pas">
						<p>Durée minimale pour déplacer un creneau, par exemple pour 30 minutes : 0.5.</p>
					</label>
					<label>
						<b>🖊️ Durée par défaut d'un créneau</b>
						<input type="number" min=0 max=24 required name="absence_dureeSeance">
					</label>
				</div>
			</details>

		</div>

		<div class=wait></div>

	</main>

	<div class=auth>
		<!-- Site en maintenance -->
		Authentification en cours ...
	</div>
	
	<script src="../assets/js/theme.js"></script>
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

			document.querySelectorAll("[type=text], [type=number]").forEach(input=>{
				input.value = data[input.name];
				input.addEventListener("input", setConfig)
			})
		}

		async function setConfig(){
			let value;
			if(this.type == "checkbox") {
				value = this.checked;
			} else {
				value = this.value;
			}

			let data = await fetchData(`setConfig&key=${this.name}&value=${value}`);

			if(data.resultat == "ok") {
				this.classList.add("done");
				setTimeout(() => {
					this.classList.remove("done")	
				}, 600);
			} else {
				this.classList.add("done", "problem");
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
