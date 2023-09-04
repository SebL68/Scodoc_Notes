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
		label p{
			margin-top: 0;
		}
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
		label:hover>input[type=number],
		label:hover>select {
			border: 1px solid var(--accent);
			outline: 1px solid var(--accent);
		}
		label>b{
			flex: none;
			margin-bottom: 16px;
			display: inline-block;
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
		select {
			display: block;
			margin: -12px 0 8px;
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
		.outils>div{
			background: #fff;
			padding: 16px;
			border: 1px solid #ccc;
			border-radius: 4px;
			margin-bottom: 4px;
		}
		.contenu a {
			display: block;
		}
		.contenu a:before{
			content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%230b0b0b' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3'/%3E%3C/svg%3E");;
			width: 16px;
			margin-right: 4px;
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
		<!-- Outils  -->
		<!-- ------- -->
			<details class=outils>
				<summary>Outils</summary>
				<div><a href="/services/diagnostic.php">Diagnostic de la passerelle</a></div>
				<div>
					<a href="/services/analytics.php">Analyse du trafic</a>
					<p>Si l'option est activée dans la section "Serveur".</p>
				</div>
				<div>
					<a href="/services/createJWT.php">Création de jetons JWT d'accès</a>
					<p> Nécessite la configuration d'une clé JWT dans /config/config.php - Nécessite de modifier le fichier html/services/createJWT.php "à la main".</p>
				</div>
				<div>
					<a href="/services/nettoyagePhotos.php">Nettoyage photos</a>
					<p>Supprime automatiquement les photos étudiants de la passerelle pour les étudiants qui n'ont pas été inscrits dans Scodoc depuis plus d'un an.</p>
				</div>
			</details>

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
						<p>
							Système interne à la passerelle pour l'analyse du trafic compatible RGPD, les données sont visibles dans ce <a href="/services/analytics.php">tableau de bord</a>. 
						</p>
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
					<label>
						<input type="checkbox" name="etudiant_modif_photo">
						<b>Autoriser les étudiants à modifier leur photo</b>
						<p>Cette modification ne se fait que sur la passerelle et n'est pas liée avec la photo Scodoc.</p>	
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
						<b>Accès enseignant</b>
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

					<label>
						<input type="checkbox" name="cloisonner_enseignants">
						<b>Cloisonner les enseignants</b>
						<p>
							Chaque enseignant n'a accès qu'aux départements dans lesquels il intervient (voir onglet Comptes).
						</p>
					</label>

					<label>
						<input type="checkbox" name="doc_afficher_nip">
						<b>"Onglet Documents -> Données étudiants" : afficher le numéro d'étudiant</b>
					</label>
					<label>
						<input type="checkbox" name="doc_afficher_id">
						<b>"Onglet Documents -> Données étudiants" : afficher l'identifiant étudiant</b>
					</label>
					<label>
						<input type="checkbox" name="doc_afficher_date_naissance">
						<b>"Onglet Documents -> Données étudiants" : afficher la date de naissance</b>
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
						<b>Saisie des absences</b>
						<p>Activer le module de saisie des absences depuis la passerelle.</p>
						<p>💡 Nécessite le mode enseignant.</p>
					</label>
					<label>
						<input type="checkbox" name="afficher_absences">
						<b>Affichage des absences aux étudiants</b>
						<p>Activer la zone de visualisation des absences sous le relevé de notes. Les données affichées proviennent de la passerelle ou de Scodoc en fonction de l'option suivante.</p>
					</label>
					<label>
						<input type="checkbox" name="data_absences_scodoc">
						<b>Sauvegarde des données absences dans Scodoc</b>
						<p>Par défaut, les absences sont stockées sur la passerelle.</p>
						<p>L'utilisation de cette option nécessite d'avoir au moins la version 9.6 de Scodoc.</p>
						<p>Si les données sont stockées dans Scodoc et que la saisie est activée depuis la passerelle, il faut ajouter la permission ScoAbsChange au rôle LecteurAPI sur le serveur Scodoc :
<pre>
<code>
	# En tant qu'utilisateur "scodoc" :
	cd /opt/scodoc
	source venv/bin/activate
	flask edit-role LecteurAPI -a ScoAbsChange

	# Si vous souhaitez retirer cette permission
	flask edit-role LecteurAPI -r ScoAbsChange
</code>
</pre>
						</p>
						<p>⚠️⚠️⚠️ Attention : même si l'affichage aux étudiants fonctionne, la passerelle ne gère pas les saisies, les justifications et les statistiques des absences ajoutées directement dans Scodoc qui durent plusieurs jours.</p>
						<p><a target=_blank href=../services/messages.php#absencesMultiJours>Plus d'informations</a></p>
					</label>
					<label>
						<b>Métrique des absences (!!! Ne fonctionne pas pour le moment !!!)</b><br>
						<select name="metrique_absences">
							<option value="heure">La passerelle calcule les heures</option>
							<option value="demi">Récupération des demi-journées dans Scodoc</option>
							<option value="journee">Récupération des journées dans Scodoc</option>
						</select>
						<p>Permet de choisir le type de métrique pour l'affichage des totaux absences aux étudiants.</p>
					</label>
					<label>
						<input type="checkbox" name="autoriser_justificatifs">
						<b>Dépôt de justificatifs</b>
						<p>Choisir si les étudiants peuvent déposer des justificatifs d'absences qui seront importés dans Scodoc.</p>
						<p>💡 Nécessite l'activation de l'affichage des absences et de la sauvegarde des données dans Scodoc.</p>
						<p>Il est nécessaire de changer la permission ScoJustifView pour les utilisateurs qui ont le droit de voir les justificatifs : </p>
<pre>
<code>
	# En tant qu'utilisateur "scodoc" :
	cd /opt/scodoc
	source venv/bin/activate

	# Pour ajouter les droits de visualisation aux "Admin" et aux "Secr" :
	flask edit-role Admin -a ScoJustifView
	flask edit-role Secr -a ScoJustifView
</code>
</pre>
					</label>
					<label>
						<b>🖊️ Liste des départements autorisant le dépot de justificatifs</b>
						<input type="text" name="liste_dep_ok_jusiticatifs">
						<p>Sous la forme : MMI,GEII,GLT,GEA</p>
						<p>⚠️ Attention : l'acronyme du département doit être celui des débuts dans Scodoc. Par exemple, si le département GLT a été renommé en MLT. Il faut alors utiliser GLT.</p>
					</label>

					<label>
						<b>🖊️ Message au début du rapport d'absences, après le relevé de notes</b>
						<input type="text" name="message_rapport_absences">
						<p>A destination des étudiants pour leur donner des indications supplémentaires.</p>
					</label>

					<label>
						<b>🖊️ Message à ajouter dans la page justificatifs</b>
						<input type="text" name="message_justificatifs">
						<p>A destination des étudiants pour leur donner des indications supplémentaires.</p>
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

			document.querySelectorAll("[type=text], [type=number], select").forEach(input=>{
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
			}, 6000);
		}
	</script>
	<?php
 		include "$path/config/analytics.php";
  	?>
</body>
</html>
