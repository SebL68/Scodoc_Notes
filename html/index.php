<?php 
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/default_config.php";
?>
<!DOCTYPE html>
<html lang=fr>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width">
		<title>Relevé de notes</title>
		<link rel="manifest" href="manifest.json">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<meta name="theme-color" content="#0084b0">
		<link rel="apple-touch-icon" href="images/icons/192x192.png">
		<style>
			<?php include $_SERVER['DOCUMENT_ROOT']."/assets/styles/global.css"?>
/**********************/
/* Gestion de semestres */
/**********************/
			.studentPic{
				float: left;
				border-radius: 8px;
				width: 52px;
    				margin-right: 16px;
				height: auto;
				object-fit: contain;
			}
			.semestres{
				display: flex;
				flex-wrap: wrap;
			}
			.semestres>label{
				cursor: pointer;
			}
			.semestres input{
				display: none;
			}
			.semestres>label>div{
				background: var(--fond-clair);
				padding: 8px 16px;
				margin: 8px;
				font-size: 18px;
				text-align: right;
				border-radius: 8px;
				box-shadow: var(--box-shadow);
			}
			.semestres>label>div:hover{
				outline: 2px solid var(--gris);
			}
			.semestres>label>div>div:nth-child(2){
				font-weight: bold;
				color: var(--primaire);
			}
			.semestres input:checked+div{
				background: var(--secondaire);
				color: var(--secondaire-contenu);
			}
			.semestres input:checked+div>div:nth-child(2){
				color: var(--secondaire-contenu);
			}

			form{
				text-align: right;
			}
			form>button{
				border: none;
				background: var(--primaire);
				padding: 8px 32px;
				color: var(--primaire-contenu);
				border-radius: 8px;
				cursor: pointer;
			}

			.depMessage{
				display: none;
				background: var(--fond-clair);
				border: 1px solid #CCC;
				margin: 16px 0;
				padding: 8px 32px;
				position: relative;
			}
			.depMessage::before{
				content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 24 24' fill='%23FFFFFF' stroke='%230099cc' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z'%3E%3C/path%3E%3Cpolyline points='22,6 12,13 2,6'%3E%3C/polyline%3E%3C/svg%3E");
				position: absolute;
				top: -8px;
				left: -16px;
				transform: rotate(30deg);
				animation: lettre 2s infinite;
			}
			@keyframes lettre {
				5%, 15% { transform: rotate(0deg); }
				10%, 20% { transform: rotate(60deg); }
				25% { transform: rotate(30deg); }

			}
			.releve{
				margin-bottom: 22px;
			}
/**********************/
/* Zone absences */
/**********************/
			h2{
				background: var(--primaire);
			}
			.absences{
				display: none;
			}
			.absences>div{
				display: grid;
				grid-template-columns: repeat(5, auto);
				gap: 2px;
				padding: 4px;
				overflow: auto;
			}
			.absences>div>div{
				background: var(--fond-clair);
				box-shadow: var(--box-shadow);
				padding: 4px 8px;
				border-radius: 4px;
			}
			.absences>div>.entete{
				background:var(--secondaire);
				color: var(--secondaire-contenu);
			}
			.absences>div>.enseignant{
				text-transform: capitalize;
			}
			.absences>div>.absent{background: #ec7068; color: #FFF;}
			.absences>div>.retard{background: #f3a027; color: #FFF;}
			.absences>div>.justifie{background: var(--secondaire) !important; color: var(--secondaire-contenu);}

			.absences>.toutesAbsences>.absent:before{content:"Absent"}
			.absences>.toutesAbsences>.retard:before{content:"Retard"}
			.absences>.toutesAbsences>.justifie:before{content:"Justifiée"}

			.absences>.totauxAbsences{
				grid-template-columns: repeat(3, auto);
				margin-top: 16px;
			}
			.totauxAbsences>div:nth-child(1){
				background: var(--primaire);
			}
			.depotJustif {
				display: inline-block;
				padding: 8px 16px;
				border-radius: 8px;
				background: #c09;
				text-decoration: none;
				color: #FFF;
			}

/**********************/
/* Mode personnels    */
/**********************/
			.etudiantHide{
				display: none;
			}
			.personnel .eval{
				cursor: initial;
			}
			.personnel .etudiantHide{
				display: block;
				margin: 20px auto 20px auto;
			}
			.etudiantHide>input{
				border: 1px solid #ef5350;
				padding: 20px;
				border-radius: 20px;
				font-size: 18px;
				display: inline-block;
				margin: 10px;
			}
/***************/
/* Histogramme */
/***************/
			.histogramme {
				position: fixed;
				z-index: 1000;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				max-width: 100%;
				background: rgba(0,0,0,0.8);
				display: flex;
				font-size: 12px;
				overflow: auto;
			}
			.histogramme>div::before {
				content: "Nombre d'étudiants";
				color: #000;
				transform: rotate(-90deg);
				position: absolute;
				bottom: 77px;
				left: -44px;
			}
			.histogramme>div::after {
				content: "Notes";
				color: #000;
				position: absolute;
				bottom: 4px;
				left: 16px;
			}
			.histogramme>div{
				position: relative;
				background: #eee;
				padding: 8px 16px 32px 16px;;
				border-radius: 8px;
				display: flex;
				gap: 2px;
				height: 200px;
				margin: auto;
			}
			.histo_max {
				background: #fff;
				border-radius: 4px;
				position: relative;
				width: 16px;
				display: flex;
				align-items: flex-end;
			}
			.histo_visu {
				padding: 2px 1px;
				border-radius: 4px;
				flex: 1;
				background: var(--secondaire);
				text-align: center;
			}
			.histo_value {
				color: #FFF;
			}
			.histo_index {
				position: absolute;
				top: 100%;
				color: #000;
			}
			.histogramme .focus{
				background: var(--accent);
			}
/*****************/
/* Appréciations */
/*****************/
			.appreciations {
				max-width: 1000px;
				background: var(--fond-clair);
				border: 1px solid var(--gris-estompe);
				border-radius: 16px;
				padding: 16px 32px;
				color: var(--contenu);
			}
		</style>
		<meta name=description content="Relevé de notes - <?php echo $Config->nom_IUT; ?>">
	</head>
	<body>
		<?php 
			if (file_exists($path.'/config/hook.php')) {  include $path.'/config/hook.php';}
		?>
		<?php 
			$h1 = 'Relevé de notes';
			include $_SERVER['DOCUMENT_ROOT']."/assets/header.php";
		?>
		<main>
			<a href="avatar.php" aria-label="Changer la photo">
				<img alt="Photo de profil" class=studentPic src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" width=350 height=450>
			</a>
			<p>
				Bonjour <span class=nom></span>.
			</p>
			<p>
				<i>
					Ce relevé de notes est provisoire, il est fourni à titre informatif et n'a aucune valeur officielle.<br>
					La moyenne affichée correspond à la moyenne coefficientée des modules qui ont des notes.
				</i>
			</p>
			<div class=etudiantHide>
				Vous êtes un personnel de l'IUT , <input required list=etudiants name=etudiant placeholder="Choisissez un étudiant" onchange="loadSemesters(this);this.blur()">
				<datalist id=etudiants></datalist>
			</div>
			<div class=semestres></div>
			<div class="depMessage">
				<b>Message de votre département</b>
				<div></div>
			</div>

			<div class=releve></div>
			<hr>
			<div class="absences">
				<h2>Rapport d'absences</h2>
				<p><i class=message_rapport_absences>
				Les causes de l’absence doivent être notifiées par écrit à l'aide d'un justificatif dans les 48 heures à compter du début de l’absence au secrétariat du département. Voir règlement intérieur pour les motifs légitimes d'absence.
				</i></p>
				<a class=depotJustif href="absences/justifications.php">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/></svg>
					Dépôt d'un justificatif d'absences
				</a>
				<div class=toutesAbsences></div>
				<h3>Totaux</h3>
				<i>Chaque département peut décider d'un malus en fonction des absences injustifiées.
				</i>
				<div class=totauxAbsences></div>
			</div>

			<hr>
			<small>Ce site utilise deux cookies permettant l'authentification au service et une analyse statistique anonymisée des connexions ne nécessitant pas de consentement selon les règles du RGPD.</small><br>
			<small>Application réalisée par Sébastien Lehmann, enseignant MMI à l'IUT de Mulhouse - <a href="maj.php?-no-sw">version <?php echo $Config->passerelle_version; ?></a> - <a href="https://github.com/SebL68/Scodoc_Notes">code source</a></small>
		</main>

		<div class=auth>
			Authentification en cours ...
		</div>

		<script>
			/**************************/
			/* Service Worker pour le message "Installer l'application" et pour le fonctionnement hors ligne PWA
			/**************************/		
			if('serviceWorker' in navigator){
				navigator.serviceWorker.register('sw.js');
			}
		</script>
		<script src="assets/js/theme.js"></script>
		<script src="assets/js/releve-dut.js"></script>
		<script src="assets/js/releve-but.js"></script>
		<script>
/**************************/
/* Début
/**************************/
			let nip = "";
			let statut = "";
			let studentDep = "";
			document.querySelector("#notes")?.classList.add("navActif");
			<?php
				include "$path/includes/clientIO.php";
			?>
			checkStatut();
/*********************************************/
/* Vérifie l'identité de la personne et son statut
/*********************************************/			
			async function checkStatut(){
				let data = await fetchData("dataPremièreConnexion");

				nip = data.auth.session;
				statut = data.auth.statut;

				document.querySelector(".studentPic").src = "services/data.php?q=getStudentPic";
				let auth = document.querySelector(".auth");
				auth.style.opacity = "0";
				auth.style.pointerEvents = "none";

				if(data.auth.statut >= PERSONNEL){
					loadStudents(data.etudiants);
					let etudiant = (window.location.search.match(/ask_student=([a-zA-Z0-9._@-]+)/)?.[1] || "");
					if(etudiant){
						let input = document.querySelector("input");
						input.value = etudiant;
						loadSemesters(input);
					}
				} else {
					feedSemesters(data.semestres);
					showReportCards(data, data.semestres[data.semestres.length-1].formsemestre_id, data.auth.session);
					feedAbsences(data);
				}
				if(!config.etudiant_modif_photo) {
					document.querySelector("main>a").href = "#";
				}
				if(data.envoiDonneesVersion) {
					let url = "https://notes.iutmulhouse.uha.fr/services/getOthersData.php?";
					url += "name=" + location.host;
					url += "&passerelle_version=" + config.passerelle_version;
					url += "&acces_enseignants=" + config.acces_enseignants;
					url += "&module_absences=" + config.module_absences;
					url += "&data_absences_scodoc=" + config.data_absences_scodoc;
					url += "&autoriser_justificatifs=" + config.autoriser_justificatifs;

					fetch(url);
				}
			}
/*********************************************/
/* Fonction pour les personnels 
	Charge la liste d'étudiants pour en choisir un
/*********************************************/
			function loadStudents(data){
				let output = "";
				data.forEach(function(e){
					output += `<option value='${e[0]}'>${e[1]}</option>`;
				});
				
				document.querySelector("#etudiants").innerHTML = output;
			}
			
/*********************************************/
/* Charge les semestres d'un étudiant
	Paramètre étudiant pour un personnel qui en choisit un
/*********************************************/
			async function loadSemesters(input = ""){
				if(input){
					nip = input.value;
				}				
				let data = await fetchData("semestresEtudiant" + (input ? "&etudiant=" + nip : ""));
				feedSemesters(data, nip);
				document.querySelector(".semestres>label:nth-child(1)>div").click();
			}
			
			function feedSemesters(data, nip){
				let output = document.querySelector(".semestres");
				output.innerHTML = "";
				dep = data[data.length-1].titre;
				for(let i=data.length-1 ; i>=0 ; i--){
					let label = document.createElement("label");
					
					let input = document.createElement("input");
					input.type = "radio";
					input.name = "semestre";
					if(i==data.length-1){
						input.checked = true;
					}

					let vignette = document.createElement("div");
					vignette.innerHTML = `
						<div>${data[i].titre} - ${data[i].annee_scolaire}</div>
						<div>Semestre ${data[i].semestre_id}</div>
					`;
					vignette.dataset.semestre = data[i].formsemestre_id;
					vignette.addEventListener("click", getReportCards);

					label.appendChild(input);
					label.appendChild(vignette);
					output.appendChild(label);
				}

				if(statut >= 20){
					let url = window.location.origin + "/?ask_student=" + nip;
					let div = document.createElement("div");
					div.innerHTML = `<div style="width:100%; margin: 8px;">Lien pour accéder directement aux relevés : <a href=${url}>${url}</a></div>`;
					output.appendChild(div);
				}
			}

/*********************************************/
/* Récupère et affiche le relevé de notes
/*********************************************/
			async function getReportCards(){
				let semestre = this.dataset.semestre;
				let data = await fetchData("relevéEtudiant&semestre=" + semestre + ((nip && statut >= PERSONNEL) ? ("&etudiant=" + nip) : ""));

				showReportCards(data, semestre);
				feedAbsences(data);
			}	

			async function showReportCards(data, semestre){
				dep = data.relevé.etudiant.dept_acronym || data.relevé.etudiant.photo_url.split("/")[2];
				if(!data.relevé.date) { return; } // Les données ne sont pas publiées pour ce département (option passerelle).
				if(data.relevé.publie == false){
					document.querySelector(".releve").innerHTML = "<h2 style='background: #90c;'>" + data.relevé.message + "</h2>";
				}else if(data.relevé.type == "BUT"){
					let output = "";

					if(config.releve_PDF && (config.liste_dep_publi_PDF == "" || config.liste_dep_publi_PDF.split(",").includes(dep))) {
						output = `
						<form action="services/bulletin_PDF.php?type=BUT&sem_id=${semestre}&etudiant=${nip}" target="_blank" method="post">
							<button type="submit">Télécharger le relevé au format PDF</button>
						</form>`;

					}
					let footer = "";
					if (data.relevé.appreciation 
						&& data.relevé.appreciation.length>0 
						&& config.liste_dep_affichage_appreciations.split(",").includes(dep)){
						footer = `
						<div class="appreciations">
							<h3>Appréciations</h3>`;
						data.relevé.appreciation.forEach(app => {
							footer += `
							<p>${app.comment}</p>`;
						});
						footer += `
						</div>`;
					}
					document.querySelector(".releve").innerHTML = output + "<releve-but></releve-but>" + footer;

					let releve = document.querySelector("releve-but");
					releve.config = {
						showURL: false
					}
					releve.showData = data.relevé;
					releve.shadowRoot.children[0].classList.add("hide_abs");

					/* Styles différent de Scodoc */
					let styles = document.createElement('link');
					styles.setAttribute('rel', 'stylesheet');
					styles.setAttribute('href', 'assets/styles/releve-but-custom.css');
					releve.shadowRoot.appendChild(styles);
					<?php if(file_exists("$path/config/releve-but-local.css") == true){ ?>
					/* Styles locaux */
					styles = document.createElement('style');
					styles.innerText = `<?php include("$path/config/releve-but-local.css"); ?>`;
					releve.shadowRoot.appendChild(styles);
					<?php } ?>
					
					if(!document.body.classList.contains("personnel")){
						document.querySelector(".nom").innerText = data.relevé.etudiant.prenom.toLowerCase();
						releve.shadowRoot.querySelector(".studentPic").src = "services/data.php?q=getStudentPic";
					} else {
						releve.shadowRoot.querySelector(".studentPic").src = "services/data.php?q=getStudentPic&nip=" + nip;
					}
				} else {
					document.querySelector(".releve").innerHTML = "<releve-dut></releve-dut>";
					document.querySelector("releve-dut").showData = [data.relevé, semestre, nip];
					<?php if($Config->releve_PDF == false){ ?>
						document.querySelector("releve-dut").hidePDF = false;
					<?php } ?>
				}

				// Récupération et affichage du message département
				let message = await fetchData("getReportPageMessage&dep=" + dep);
				if(message.message) {
					let zoneMessage = document.querySelector(".depMessage");
					zoneMessage.style.display = "block";
					zoneMessage.querySelector("div").innerHTML = message.message;
				}
			}

/*********************************************/
/* Affichage des absences
/*********************************************/
			function feedAbsences(data){
				var totaux = {
					justifie: 0,
					absent: 0,
					retard: 0
				};
				let output = "";
				let multiJours = false;

				if(!config.afficher_absences) { return; }
				let depts = config.liste_dep_publi_absences;
				if  (! depts.split(",").includes(dep) && depts != '') { return; }

				document.querySelector(".message_rapport_absences").innerHTML = config.message_rapport_absences;
				document.querySelector(".absences").style.display = "block";

				if(config.autoriser_justificatifs && config.liste_dep_ok_justificatifs.split(",").includes(dep)) {
					document.querySelector(".depotJustif").href += "?nip=" + nip;
				} else {
					document.querySelector(".depotJustif").style.display = "none";
				}

				if(Object.entries(data.absences).length){
					Object.entries(data.absences).forEach(([date, listeAbsences])=>{
						listeAbsences.forEach(absence=>{
							if(absence.statut == "present"){
								return;
							}
							if(absence.justifie == true || absence.justifie == "true"){
								totaux.justifie += absence.fin - absence.debut;
							}else{
								if(absence.statut == "retard") {
									totaux[absence.statut] += 1;
								} else {
									totaux[absence.statut] += absence.fin - absence.debut;
								}
								
							}
							if(absence.dateFin && date != absence.dateFin){
								var outputDate = date.split("-").reverse().join("/") 
												+ " - " 
												+ absence.dateFin.split("-").reverse().join("/");
								multiJours = true;
							} else {
								var outputDate = date.split("-").reverse().join("/");
							}
							output = `
								<div>${outputDate}</div> 
								<div>${floatToHour(absence.debut)} - ${floatToHour(absence.fin)}</div>
								<div>${getMatiere(data, absence.matiereComplet)}</div>
								<div class=enseignant>${absence.enseignant.split('@')[0].split(".").join(" ")}</div>
								<div class="${(absence.justifie === true || absence.justifie === "true" ) ? "justifie" : absence.statut}"></div>
							` + output;
						})
					})
				} else {
					output = `
						<div>/</div> 
						<div>/</div>
						<div>/</div>
						<div>/</div>
						<div>/</div>
					`
				}
				
				document.querySelector(".absences>.toutesAbsences").innerHTML = `
					<div class=entete>Date</div> 
					<div class=entete>Heures</div>
					<div class=entete>Matière</div>
					<div class=entete>Enseignant</div>
					<div class=entete>Statut</div>
				` + output;

				/* Totaux */
				if(multiJours) {
					document.querySelector(".absences>.totauxAbsences").style.display = "none";
				} else {
					if(data.totauxAbsences) {	// totaux de Scodoc
						let txtType = {
							heure: "h",
							demi: " demi-journée(s)",
							journee: " journée(s)"
						}

						let totAbsent = data.totauxAbsences.absent.non_justifie[config.metrique_absences];
						let totJustifie = data.totauxAbsences.absent.justifie[config.metrique_absences];

						var txtJustifie = totJustifie + txtType[config.metrique_absences];
						var txtAbsent = totAbsent + txtType[config.metrique_absences];
					} else {	// Totaux calculés
						var txtJustifie = floatToHour(totaux.justifie);
						var txtAbsent = floatToHour(totaux.absent);
					}

					document.querySelector(".absences>.totauxAbsences").style.display = "grid";
					document.querySelector(".absences>.totauxAbsences").innerHTML = `
						<div class="entete justifie">Nombre justifiées</div>
						<div class="entete absent">Nombre injustifiées</div>
						<div class="entete retard">Nombre retards</div>

						<div>${txtJustifie}</div>
						<div>${txtAbsent}</div>
						<div>${totaux.retard}</div>
					`;
				}
				
			}

			function getMatiere(data, txt) {
				if(Number.isInteger(txt)) {
					let matiere = Object.entries({...data.relevé.ressources, ...data.relevé.saes}).find(e => {
						return e[1].id == txt;
					});
					if(!matiere) {
						return "-";
					}
					return matiere[0] + ' - ' + matiere[1].titre;
				} else {
					return txt || "-";
				}
			}

			function floatToHour(heure){
				return Math.floor(heure) + "h"+ ((heure%1*60 < 10)?"0"+Math.round(heure%1*60) : Math.round(heure%1*60))
			}
		</script>
	
		<?php 
			include "$path/config/analytics.php";
		?>

<!-- ----------------------------------------------------------------- -->
<!--               Fait avec beaucoup d'amour par                      -->
<!--	   Sébastien Lehmann et Denis Graef - enseignant MMI           -->
<!--																   -->
<!--         Merci à Alexandre Kieffer et Bruno Colicchio.             -->
<!-- ----------------------------------------------------------------- -->
	</body>
</html>
