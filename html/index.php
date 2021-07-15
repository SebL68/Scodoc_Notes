<?php 
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
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
			<?php include "$path/html/assets/header.css"?>
/**********************/
/* Gestion de semestres */
/**********************/

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
/* Zone relevé de notes */
/**********************/
			.releve button, .button{
				border-radius: 5px;
				border: none;
				margin: 10px;
				padding: 10px;
				display: table;
				box-shadow: 0 0 4px #000;
				background: #FFF;
				color: #000;
				text-decoration: none;
				cursor: pointer;
			}
			.total{
				font-size: 24px;
				margin: 10px;
				text-align: center;
			}
			.total>span{
				font-size: 18px;
				opacity: 0.6;
			}
			.ue, .module{
				border-radius: 10px;
				padding: 10px 20px;
				margin: 10px;
				box-shadow: 0 3px 6px #999;
			}
			.ue{
				background: #09C;
				color: #FFF;
				display: flex;
				justify-content: space-between;
				gap: 10px;
				cursor: pointer;
			}
			.ue>div:nth-child(1){
				max-width: 50%;
				display: -webkit-box;
  				-webkit-line-clamp: 3;
				-webkit-box-orient: vertical;  
				overflow: hidden;
			}
			.ue>div:nth-child(2){
				font-weight: bold;
				text-align: right;
			}
			.module{
				background: #FFF;
				margin-left: 30px;
			}
			.module>div, .eval{
				display: flex;
				justify-content: space-between;
				gap: 15px;
			}
			.module>div>div:nth-child(2){
				text-align: right;
			}
			.module>div>div>span{
				opacity: 0.6;
			}
			.coef{
				font-style: italic;
				opacity: 0.6;
				display: block;
			}
			.eval .coef{
				display:inline-block;
				width: 65px;
				text-align: left;
			}
			.eval{
				padding: 5px;
				background: #87efd5;
				margin-top: 2px;
				border-radius: 5px;
				cursor:pointer;
			}
			.eval:nth-child(odd){
				background: #a4d3e2;
			}
			.eval>div:nth-child(2){
				flex-shrink:0;
			}

			.checked, [data-note=undefined], [data-note=NP]{
				background: #D0D0D0;
				
			}
			[data-note=undefined], [data-note=NP]{
				cursor: initial;
			}
			.checked:nth-child(odd), [data-note=undefined]:nth-child(odd), [data-note=NP]:nth-child(odd){
				background: #F0F0F0;
			}
			.hide{
				display: none;
			}
			body:not(.ShowEmpty) [data-note=undefined], body:not(.ShowEmpty) [data-note=NP], body:not(.ShowEmpty) [data-note=EXC]{
				display: none !important;
			}
			.ShowEmpty .button{
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

/**********************/
/* Mode personnel UHA */
/**********************/
			.etudiant{
				display: none;
			}
			.personnel .eval{
				cursor: initial;
			}
			.personnel .etudiant{
				display: block;
				margin: 20px auto 20px auto;
			}
			.etudiant>input{
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
	<body>
		<?php 
			$h1 = 'Relevé de notes';
			include "$path/html/assets/header.php";
		?>
		<main>
			<p>
				Bonjour <span class=prenom></span>.
			</p>
			<p>
				<i>
					Ce relevé de notes est provisoire, il est fourni à titre informatif et n'a aucune valeur officielle.<br>
					La moyenne affichée correspond à la moyenne coefficientée des modules qui ont des notes.
				</i>
			</p>
			<div class=etudiant>
				Vous êtes un personnel de l'IUT , <input required list=etudiants name=etudiant placeholder="Choisissez un étudiant" onchange="loadSemesters(this.value);this.blur()">
				<datalist id=etudiants></datalist>
			</div>
			<div class=semestres></div>
			<hr>
			<div class=wait></div>
			<div class=releve></div>
			<div class=button onclick="ShowEmpty()">Montrer les évaluations sans note</div>
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
			<small>Application réalisée par Sébastien Lehmann, enseignant MMI - <a href="maj.php">voir les MAJ</a>.</small>
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
/**************************/
/* Début
/**************************/
			checkStatut();
			document.querySelector("#notes").classList.add("navActif");
			<?php
				include "$path/includes/clientIO.php";
			?>
/*********************************************/
/* Vérifie l'identité de la personne et son statut
/*********************************************/			
			async function checkStatut(){
				let data = await fetchData("dataPremièreConnexion");
				document.querySelector(".prenom").innerText = data.auth.session.split(".")[0];
				let auth = document.querySelector(".auth");
				auth.style.opacity = "0";
				auth.style.pointerEvents = "none";

				if(data.auth.statut >= PERSONNEL){
					document.querySelector("body").classList.add('personnel');
					loadStudents(data.etudiants);
				} else {
					feedSemesters(data.semestres);
					feedReportCards(data.relevé, data.semestres[0], data.auth.session);
					feedAbsences(data.absences);
				}
			}
/*********************************************/
/* Fonction pour les personnels 
	Charge la liste d'étudiants pour en choisir un
/*********************************************/
			async function loadStudents(data){
				//let data = await fetchData("listeEtudiants");
				let output = "";
				data.forEach(function(e){
					output += `<option value='${e}'>${e}</option>`;
				});
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
				feedReportCards(data.relevé, semestre, etudiant);
				feedAbsences(data.absences);
			}

			function feedReportCards(data, semestre, etudiant){
				document.querySelector(".releve").innerHTML = `
					<form action=services/bulletin_PDF.php?sem_id=${semestre}&etudiant=${etudiant} target=_blank method=post>
						<button type=submit>Télécharger le relevé au format PDF</button>
					</form>
				`;

				let decision = data.situation?.split(". ") || [];
				if(decision[1]){
					decision = "<b>"+decision[1] + ". " + decision[2]+"</b><br>";
				}else{
					decision = "";
				}
				document.querySelector(".releve").innerHTML += `
					<div class="total">
						${decision}
						Moyenne semestre : ${data.note.value} <br>
						Rang : ${data.rang.value || "Attente"} / ${data.rang.ninscrits} <br>
						<span>Classe : ${data.note.moy} - Max : ${data.note.max} - Min : ${data.note.min}</span>
					</div>
					${ue(data.ue)}`;
//				if(!document.querySelector("body").classList.contains("personnel")){
				if(document.querySelector("body").classList.contains(ETUDIANT)){	// A valider !!!
					set_checked();
				}

				//feedAbsences(data.absences);
			}

/**************************/
/* Création des bloques UE
/**************************/
			function ue(ue){
				let output = "";
				ue.forEach(e=>{
					output += `
						<div class=ue data-id="${e.acronyme}" onclick="openClose(this)">
							<div>${e.acronyme} - ${e.titre}</div>
							<div>
								Moyenne&nbsp;:&nbsp;${e.note.value}<br>Rang&nbsp;:&nbsp;${e.rang}
							</div>
						</div>
						${module(e.module)}`;
				})
				return output;
			}

/**************************/
/* Création des bloques modules
/**************************/
			function module(module){
				let output = "";
				module.forEach(e=>{
					output += `
						<div class=module data-id="${e.titre}">
							<div>
								<div>${e.titre}<span class=coef>Coef ${e.coefficient}</span></div>
								<div>
									Moyenne&nbsp;:&nbsp;${e.note.value} - Rang&nbsp;:&nbsp;${e.rang.value}<br>
									<span>
										Classe&nbsp;:&nbsp;${e.note.moy} - Max&nbsp;:&nbsp;${e.note.max} - Min&nbsp;:&nbsp;${e.note.min}
									</span>
								</div>
							</div>
							

							${evaluation(e.evaluation)}
						</div>`;
				})
				return output;
			}

/**************************/
/* Création des evaluations
/**************************/
			function evaluation(evaluation){
				let output = "";
				evaluation.forEach(e=>{
					output += `
						<div class=eval onclick="check_eval(this)" data-id="${e.description}" data-note=${e.note}>
							<div>${e.description}</div>
							<div>${e.note}&nbsp;<span class=coef>Coef&nbsp;${e.coefficient}</span></div>
						</div>`;
				})
				return output;
			}

/*********************************************/
/* Affichage des absences
/*********************************************/
			function feedAbsences(data){
				var totaux = {};
				let output = "";
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

				document.querySelector(".absences>.toutesAbsences").innerHTML = `
					<div class=entete>Date</div> 
					<div class=entete>Créneaux</div>
					<div class=entete>Matière</div>
					<div class=entete>Enseignant</div>
					<div class=entete>UE</div>
					<div class=entete>Statut</div>
				` + output;

				/* Totaux */
				output = `
					<div class=entete>UE</div> 
					<div class=entete>Nombre justifiée</div>
					<div class="entete absent">Nombre injusitifée</div>
				`;

				Object.entries(totaux).forEach(([UE, total])=>{
					output += `
						<div>${UE}</div>
						<div>${total.justifie}</div>
						<div>${total.injustifie}</div>
					`;
				})

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
					let eval=document.querySelector(e);
					if(eval && eval.dataset.note == localStorage.getItem(e)){
						eval.classList.add("checked");
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
			include "$path/includes/analytics.php";
		?>

<!-- ----------------------------------------------------------------- -->
<!--               Fait avec beaucoup d'amour par                      -->
<!--	   Sébastien Lehmann et Denis Graef - enseignant MMI           -->
<!--																   -->
<!--         Merci à Alexandre Kieffer et Bruno Colicchio.             -->
<!-- ----------------------------------------------------------------- -->
	</body>
</html>