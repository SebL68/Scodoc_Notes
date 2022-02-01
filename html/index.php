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
			<?php include $_SERVER['DOCUMENT_ROOT']."/assets/header.css"?>
/**********************/
/* Gestion de semestres */
/**********************/
			.studentPic{
				float: left;
				border-radius: 8px;
				width: 52px;
    			margin-right: 16px;
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
			.semestres span{
				border: 1px solid #777;
				background: #FFF;
				padding: 10px;
				margin: 10px;
				display: block;
			}
			.semestres input:checked+span{
				background: #0C9;
				color: #FFF;
			}

/**********************/
/* Zone absences */
/**********************/
			h2{
				background: #09C;
			}
			.absences>div{
				display: grid;
				grid-template-columns: repeat(6, auto);
				gap: 2px;
				padding: 4px;
				overflow: auto;
			}
			.absences>div>div{
				background: #FFF;
				box-shadow: 0 2px 2px #888;
				padding: 4px 8px;
				border-radius: 4px;
			}
			.absences>div>.entete{
				background:#0c9;
				color: #FFF;
			}
			.absences>div>.enseignant{
				text-transform: capitalize;
			}
			.absences>div>.absent{background: #c09; color: #FFF;}
			.absences>div>.excuse{background: #0c9}

			.absences>.toutesAbsences>.absent:before{content:"Absent"}
			.absences>.toutesAbsences>.excuse:before{content:"Justifiée"}

			.absences>.totauxAbsences{
				grid-template-columns: repeat(3, auto);
				margin-top: 16px;
			}
			.totauxAbsences>div:nth-child(1){
				background: #09c;
			}

			.hideAbsences .absences{
				display: none;
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
		</style>
		<meta name=description content="Relevé de notes de l'IUT de Mulhouse">
	</head>
	<body class="<?php
		if($Config->afficher_absences == false){
			echo 'hideAbsences';
		}
	?>">
		<?php 
			$h1 = 'Relevé de notes';
			include $_SERVER['DOCUMENT_ROOT']."/assets/header.php";
		?>
		<main>
			<a href="avatar.php">
				<img class=studentPic src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">
			</a>
			<p>
				Bonjour <span class=prenom></span>.
			</p>
			<p>
				<i>
					Ce relevé de notes est provisoire, il est fourni à titre informatif et n'a aucune valeur officielle.<br>
					La moyenne affichée correspond à la moyenne coefficientée des modules qui ont des notes.
				</i>
			</p>
			<div class=etudiantHide>
				Vous êtes un personnel de l'IUT , <input required list=etudiants name=etudiant placeholder="Choisissez un étudiant" onchange="loadSemesters(this.value);this.blur()">
				<datalist id=etudiants></datalist>
			</div>
			<div class=semestres></div>
			<hr>
			<div class=wait></div>
			<div class=releve></div>
			<!--<div class=button onclick="ShowEmpty()">Montrer les évaluations sans note</div>-->
			<hr>

			<div class="absences">
				<h2>Rapport d'absences</h2>
				<p><i>
					Vous avez jusqu'à 48h après votre retour pour justifier une absence auprès du secrétariat.<br>
					Les créneaux horaires sont indicatifs et susceptibles de varier en fonction du département.
				</i></p>
				<div class=toutesAbsences></div>
				<h3>Totaux</h3>
				<i>Chaque département peut décider d'un malus en fonction des absences injustifiées.
				</i>
				<div class=totauxAbsences></div>
			</div>

			<hr>
			<small>Ce site utilise deux cookies permettant l'authentification au service et une analyse statistique anonymisée des connexions ne nécessitant pas de consentement selon les règles du RGPD.</small><br>
			<small>Application réalisée par Sébastien Lehmann, enseignant MMI - <a href="maj.php">voir les MAJ</a> - <a href="https://github.com/SebL68/Scodoc_Notes">code source</a>.</small>
		</main>

		<div class=auth>
			Authentification en cours ...
		</div>

		<script src="assets/js/releve-dut.js"></script>
		<script src="assets/js/releve-but.js"></script>
		<script>
/**************************/
/* Service Worker pour le message "Installer l'application" et pour le fonctionnement hors ligne PWA
/**************************/		
			if('serviceWorker' in navigator){
				navigator.serviceWorker.register('sw.js');
			}
/**************************/
/* Début
/**************************/
			checkStatut();
			document.querySelector("#notes")?.classList.add("navActif");
			<?php
				include "$path/includes/clientIO.php";
			?>
			let CAS_return_type = "<?php echo $Config->CAS_return_type;?>";
/*********************************************/
/* Vérifie l'identité de la personne et son statut
/*********************************************/			
			async function checkStatut(){
				let data = await fetchData("dataPremièreConnexion");
				document.querySelector(".studentPic").src = "services/data.php?q=getStudentPic";
				document.querySelector(".prenom").innerText = String(data.auth.session).match(/([a-z-]*)\./)?.[1] || "Mme, M.,";
				let auth = document.querySelector(".auth");
				auth.style.opacity = "0";
				auth.style.pointerEvents = "none";

				if(data.auth.statut >= PERSONNEL){
					document.querySelector("body").classList.add('personnel');
					if(data.auth.statut >= ADMINISTRATEUR){
						document.querySelector("#admin").style.display = "inherit";
					}
					loadStudents(data.etudiants);
					let etudiant = (window.location.search.match(/ask_student=([a-zA-Z0-9._@-]+)/)?.[1] || "");
					if(etudiant){
						loadSemesters(etudiant);
					}
				} else {
					document.querySelector("body").classList.add('etudiant');
					feedSemesters(data.semestres);
					showReportCards(data, data.semestres[0], data.auth.session);
					feedAbsences(data.absences);
				}
			}
/*********************************************/
/* Fonction pour les personnels 
	Charge la liste d'étudiants pour en choisir un
/*********************************************/
			async function loadStudents(data){
				let output = "";
				if(CAS_return_type == "nip"){
					data.forEach(function(e){
						output += `<option value='${e[0]}'>${e[1]}</option>`;
					});
				} else {
					data.forEach(function(e){
						output += `<option value='${e[1]}'>${e[1]}</option>`;
					});
				}
				
				document.querySelector("#etudiants").innerHTML = output;
			}
			
/*********************************************/
/* Charge les semestres d'un étudiant
	Paramètre étudiant pour un personnel qui en choisit un
/*********************************************/
			async function loadSemesters(etudiant = ""){
				let data = await fetchData("semestresEtudiant" + (etudiant ? "&etudiant=" + etudiant : ""));
				feedSemesters(data, etudiant);
				document.querySelector(".semestres>label:nth-child(1)>span").click();
			}
			
			function feedSemesters(data, etudiant = ""){
				let output = document.querySelector(".semestres");
				output.innerHTML = "";
				for(let i=0, n=data.length;i<n;i++){
					let label = document.createElement("label");
					
					let input = document.createElement("input");
					input.type = "radio";
					input.name = "semestre";
					if(i==0){
						input.checked = true;
					}

					let span = document.createElement("span");
					span.innerText = "Semestre " + (n-i);
					span.dataset.semestre = data[i];
					span.addEventListener("click", getReportCards);

					label.appendChild(input);
					label.appendChild(span);
					output.appendChild(label);
				}
				output.dataset.etudiant = etudiant;
			}

/*********************************************/
/* Récupère et affiche le relevé de notes
/*********************************************/
			async function getReportCards(){
				let semestre = this.dataset.semestre;
				let etudiant = this.parentElement.parentElement.dataset.etudiant;
				let data = await fetchData("relevéEtudiant&semestre=" + semestre + (etudiant ? "&etudiant=" + etudiant : ""));

				showReportCards(data, semestre, etudiant);
				feedAbsences(data.absences);
			}	

			function showReportCards(data, semestre, etudiant){
				if(data.relevé.publie == false){
					document.querySelector(".releve").innerHTML = "<h2 style='background: #90c;'>Le responsable de votre formation a décidé de ne pas publier le relevé de notes de ce semestre.</h2>";
				}else if(data.relevé.type == "BUT"){
					document.querySelector(".releve").innerHTML = "<releve-but></releve-but>";

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
						document.querySelector(".prenom").innerText = data.relevé.etudiant.prenom.toLowerCase();
						releve.shadowRoot.querySelector(".studentPic").src = "services/data.php?q=getStudentPic";
					} else {
						releve.shadowRoot.querySelector(".studentPic").src = "services/data.php?q=getStudentPic&email=" + etudiant;
					}
				} else {
					document.querySelector(".releve").innerHTML = "<releve-dut></releve-dut>";
					document.querySelector("releve-dut").showData = [data.relevé, semestre, etudiant];
					<?php if($Config->releve_PDF == false){ ?>
						document.querySelector("releve-dut").hidePDF = false;
					<?php } ?>
				}
			}

/*********************************************/
/* Affichage des absences
/*********************************************/
			function feedAbsences(data){
				var totaux = {};
				let output = "";

				if(Object.entries(data).length){
					Object.entries(data).forEach(([date, creneaux])=>{
						Object.entries(creneaux).forEach(([creneau, dataAbsence])=>{
							if(!totaux[dataAbsence.UE]){
								totaux[dataAbsence.UE] = {
									justifie: 0,
									injustifie: 0
								};
							}
							if(dataAbsence.statut == "absent"){
								totaux[dataAbsence.UE].injustifie += 1;
							}else{
								totaux[dataAbsence.UE].justifie += 1;
							}
							output = `
								<div>${date}</div> 
								<div>${creneau.replace(",", " - ")}</div>
								<div>${dataAbsence.matiereComplet}</div>
								<div class=enseignant>${dataAbsence.enseignant.split('@')[0].split(".").join(" ")}</div>
								<div>${dataAbsence.UE}</div>
								<div class="${dataAbsence.statut}"></div>
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
						<div>/</div>
					`
				}
				

				document.querySelector(".absences>.toutesAbsences").innerHTML = `
					<div class=entete>Date</div> 
					<div class=entete>Créneau</div>
					<div class=entete>Matière</div>
					<div class=entete>Enseignant</div>
					<div class=entete>UE</div>
					<div class=entete>Statut</div>
				` + output;

				/* Totaux */
				output = `
					<div class=entete>UE</div>
					<div class=entete>Nombre justifiées</div>
					<div class="entete absent">Nombre injustifiées</div>
				`;

				if(Object.entries(totaux).length){
					Object.entries(totaux).forEach(([UE, total])=>{
						output += `
							<div>${UE}</div>
							<div>${total.justifie}</div>
							<div>${total.injustifie}</div>
						`;
					})
				} else {
					output += `
						<div>/</div>
						<div>0</div>
						<div>0</div>
					`;
				}

				

				document.querySelector(".absences>.totauxAbsences").innerHTML = output;
			}

/**************************/
/* Pointage des évaluations et stockage en local de l'action
/**************************/
			function check_eval(obj){
				if(obj.dataset.note != "undefined" && obj.dataset.note != "NP"){
					obj.classList.toggle("checked");
					let ue = obj.parentElement;
					let security = 100;
					do{
						ue = ue.previousElementSibling;
						if(--security == 0)break;
					}while(ue.className != "ue");

					let id = `[data-id='${ue.dataset.id}']~[data-id='${obj.parentElement.dataset.id}']>[data-id='${obj.dataset.id}']`;

					if(localStorage.getItem(id) != obj.dataset.note){
						localStorage.setItem(id, obj.dataset.note);
					}else{
						localStorage.removeItem(id);
					}
				}
			}

/**************************/
/* Pointage des évaluation déjà pointées ultérieurement
/**************************/			
			function set_checked(){
				Object.keys(localStorage).forEach(function(e){
					try{
						var eval=document.querySelector(e);
						if(eval && eval.dataset.note == localStorage.getItem(e)){
							eval.classList.add("checked");
						}
					} catch(error){
						console.log("Problème de selector des eval : " + e);
						localStorage.removeItem(e);
					}				
					
				})

				let firstNotChecked = document.querySelector(".eval:not(.checked):not([data-note=undefined]):not([data-note=NP])");
				if(firstNotChecked){
					let y = firstNotChecked.parentElement.getBoundingClientRect().top + window.scrollY;

					window.scrollTo(0, y - 65); 
				}
			}
/**************************/
/* Ouvrir / fermer les UE
/**************************/
			function openClose(obj){
				while(obj.nextElementSibling && obj.nextElementSibling.classList.contains("module")){
					obj = obj.nextElementSibling;
					obj.classList.toggle("hide");
				}
			}
/**************************/
/* Afficher / masquer les évaluations sans notes
/**************************/
			function ShowEmpty(){
				document.querySelector("body").classList.toggle("ShowEmpty");
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
