<?php 
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	require_once "$path/includes/default_config.php";
	require_once "$path/includes/analytics.class.php";
	Analytics::add('gestionAbsences');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justif. absences</title>
    <style>
        <?php include $_SERVER['DOCUMENT_ROOT']."/assets/styles/global.css"?>
        header{
            position: sticky;
            left:0;
            top:0;
        }      
        main{
            text-align: center;
        }
        @media screen and (max-width: 1120px){
            html, body{
                overflow: auto;
                height: 100vh;
            }
            main{
                max-width: initial;
            }
        }
        .contenu{
            opacity: 0.5;
            pointer-events: none;
            user-select: none;
			position: relative;
        }
        .ready{
            opacity: initial;
            pointer-events: initial;
        }

        .capitalize{
            text-transform: capitalize;
        }
/**********************/
/*   Zones de choix   */
/**********************/
        .zone{
            background: var(--fond-clair);
            padding: 8px;
            margin-bottom: 8px;
            border-radius: 4px;
            border: 1px solid #CCC;
        }
		select{
			font-size: 21px;
			padding: 10px;
			margin: 5px auto;
			background: var(--primaire);
			color: var(--primaire-contenu);
			border: none;
			border-radius: 10px;
            max-width: 100%;
            display: table;
            box-shadow: var(--box-shadow);
		}
        .highlight{
            animation: pioupiou 0.4s infinite ease-in alternate;
        }
        @keyframes pioupiou{
            0%{
                box-shadow: 0 0 4px 0px orange;
            }
            100%{
                box-shadow: 0 0 4px 2px orange;
            }
        }

/*******************************/
/* Listes étudiants */
/*******************************/
		.contenu>button{
			border: 1px solid #CCC;
			border-radius: 4px;
			padding: 8px 16px;
			background: var(--fond-clair);
			cursor: pointer;
		}
		.contenu>button:hover{
			background: var(--secondaire);
			color: var(--secondaire-contenu);
		}
        .flex{
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .groupes{
			width: fit-content;
			margin: auto;
			margin-bottom: 10px;
        }
        .groupe{
            cursor: pointer;
            display: flex;
			flex-wrap: wrap;
            align-items: center;
            gap: 4px;
            padding: 10px;
            margin: 2px;
            background: var(--primaire);
            color: var(--primaire-contenu);
            border-radius: 8px;
        }
		.partition {
			display: flex;
			align-items: center;
			justify-content: center;
		}

		@supports (grid-template-columns: subgrid) {
			.groupes {
				display: grid;
				grid-template-columns: auto auto;
			}
			.partition {
				display: grid;
				grid-template-columns: subgrid;
				grid-column: 1 / -1;
			}
		}

		.partition>b{
			margin-right: 16px;
			text-align: right;
		}
		.partition>div{
			display: flex;
			flex-wrap: wrap;
		}

        @media screen and (max-width: 1120px){
            .flex{
                flex-direction: column-reverse;
            }
            .groupes{
				width: calc(100vw - 28px);
				margin: 0;
				margin-bottom: 10px;
            }
        }
        .selected{
            opacity: 0.5;
        }
        .hide{
            display: none !important;
        }

/*****************************/
/* Zone absences */
/*****************************/
        .date{
            position: sticky;
            top: 0;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 20px;
            background: var(--secondaire);
            color: var(--secondaire-contenu);
            border-radius: 10px;
            border: none;
        }
        .date>svg{
            cursor: pointer;
        }
        #actualDate{
            padding: 4px 0;
        }
		.etudiants{
            counter-reset: cpt;
			display: flex;
			justify-content: center;
			flex-direction: column;
			gap: 1px;
        }
		.semaine{
			position: sticky;
			top: 40px;
			z-index: 1;
			margin-top: 4px;
		}
		.etudiants>.semaine>div{
			cursor: initial !important;
			transition-delay: .035s;
			border-color: var(--primaire);
            width: initial;
		}
		.semaine>div:nth-child(1){
			grid-column: 2;
		}
		.etudiants>div:hover:not(.semaine)>div:nth-child(1), .showDay{
			background: var(--accent) !important;
			color: var(--accent-contenu);
			transition-delay: 0s !important;
			border-color: var(--accent) !important;
		}
		.etudiants>div{
			display: grid;
			grid-template-columns: 300px repeat(6, 144px);
			gap: 5px;
		}
		.etudiants>div>div{
			border-radius: 10px;
            border: 1px solid var(--gris-estompe);
            background: var(--fond-clair); 
            cursor: pointer;
		}
		.etudiants>div>.dayStudent{
			position: relative;
			overflow: hidden;
			cursor: initial;
		}
		.etudiants>div>.dayStudent:hover{
			border: 1px solid var(--gris-estompe);
		}
		.etudiants>div>.dayStudent>div{
			position: absolute;
			top: 0;
			bottom: 0;
			border-radius: 10px;
			border: 1px solid var(--fond-clair);
		}

		.etudiants>div>.dayStudent>div:not([data-statut=present]){
			cursor: pointer;
		}
		.etudiants>div>.dayStudent>div:not([data-statut=present]):hover{
			border: 2px solid var(--secondaire);
		}

        .etudiants .btnAbsences{
            position: relative;
            text-align: left;
            padding: 10px 20px;
			border-color: var(--primaire);
            width: initial;
            justify-self: initial;
        }
        .btnAbsences>div:nth-child(1){
            display: flex;
            gap:5px;
			overflow: hidden;
        }
        .btnAbsences>div:nth-child(1)::before{
            counter-increment: cpt;
            content: counter(cpt) " ";
            display: inline-block;
        }
        .btnAbsences>div:nth-child(1)>:last-child{
            margin-left: auto;
        }

		.btnAbsences>img{
			position: absolute;
			bottom: 100%;
			right: 0;
			pointer-events:none;
			background: var(--fond-clair);
			border-radius: 16px;
			border: 1px solid var(--accent);
			display: none;
		}
		.btnAbsences:hover{
			z-index: 1;
		}
		.btnAbsences:hover>img{
			display: block;
		}

        @media screen and (max-width: 1120px){
            .zone{
                position: sticky;
                left: 0;
            }
            .date{
                position: sticky;
                left: 10px;
                width: calc(100vw - 28px);
            }
            .etudiants .btnAbsences{
                padding: 8px 16px;
                position: sticky;
                left: 0;
            }
            .btnAbsences>div:nth-child(1)::before{
                display: none;
            }
        }
		[data-statut=present]{
            background: #00bcd4;
        }
        [data-statut=absent]{
            background: #ec7068;
        }
		[data-statut=retard]{
            background: #f3a027;
        }
		[data-justifie=true]{
            background: var(--secondaire);
        }

		.waitResponse{
			pointer-events: none;
			filter: brightness(50%);
		}
    </style>
    <meta name=description content="Gestion des absences - <?php echo $Config->nom_IUT; ?>">
</head>
<body>
    <?php 
        $h1 = 'Stats / Justif';
        include $_SERVER['DOCUMENT_ROOT']."/assets/header.php";
    ?>
    <main>
		<p>
			Bonjour <span class=nom></span>.
		</p>

		<div class="zone">
			<select id=departement class=highlight onchange="clearStorage(['semestre', 'matiere']);selectDepartment(this.value)">
				<option value="" disabled selected hidden>Choisir un département</option>
				<?php
					require_once "$path/includes/".$Config->service_data_class;		// Class service_data - typiquement Scodoc
					$Scodoc = new Scodoc();
					$listDepartement = $Scodoc->getDepartmentsList();
					foreach($listDepartement as $departement){
						echo '<option value=' . $departement['code'] . '>' . $departement['nom'] . '</option>';
					}
				?>
			</select>

			<select id=semestre onchange="selectSemester(this.value)" disabled>
				<option value="" disabled selected hidden>Choisir un semestre</option>
			</select>
		</div>

        <div class=contenu></div>
        
    </main>

    <div class=auth>
        <!-- Site en maintenance -->
        Authentification en cours ...
    </div>

	<script>
		/**************************/
		/* Service Worker pour le message "Installer l'application" et pour le fonctionnement hors ligne PWA
		/**************************/		
		if('serviceWorker' in navigator){
			navigator.serviceWorker.register('../sw.js');
		}
	</script>
	<script src="../assets/js/theme.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
    <script>
		<?php
            include "$path/includes/clientIO.php";
		?>  
        document.querySelector("#gestion").classList.add("navActif");

/*********************************************/
/* Vérifie l'identité de la personne et son statut
/*********************************************/		
        var session = "";
        var statutSession = "";
        async function checkStatut(){
            let data = await fetchData("donnéesAuthentification");
            session = data.session;
            let auth = document.querySelector(".auth");
            auth.style.opacity = "0";
            auth.style.pointerEvents = "none";
            statutSession = data.statut;

            if(data.statut >= PERSONNEL){
                /* Gestion du storage remettre le même état au retour */
                let departement = localStorage.getItem("departement");
                if(departement){
                    document.querySelector("#departement").value = departement;
                    selectDepartment(departement);
                }

			} else {
				document.querySelector(".contenu").innerHTML = "Ce contenu est uniquement accessible pour des personnels de l'IUT. ";
			}
        }
/*********************************************/
/* Récupère et traite les listes d'étudiants du département */
/*********************************************/		
        var departement = "";
        var semestre = "";
		var modules;
        var dataEtudiants;
        var depAdmins = [];

        async function selectDepartment(dep){
            departement = dep;
			let data = await fetchData("semestresDépartement&dep="+departement);
			
			let select = document.querySelector("#semestre");
			select.innerHTML = `<option value="" disabled selected hidden>Choisir un semestre</option>`;
			data.forEach(function(semestre){
				let option = document.createElement("option");
				option.value = semestre.id;
				option.innerText = `${semestre.titre_long} - Semestre ${semestre.num}`;
				select.appendChild(option);
            });
            document.querySelector("#departement").classList.remove("highlight");
            document.querySelector(".contenu").classList.remove("ready");
            select.disabled = false;
            select.classList.add("highlight");

            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('departement', departement);

            let semestre = localStorage.getItem("semestre");
            if(semestre){
                document.querySelector("#semestre").value = semestre;
				if(document.querySelector("#semestre").value){
					selectSemester(semestre);
				} else {
					document.querySelector("#semestre").value = "";
				}
            }
            depAdmins = await fetchData("listeAdministrateurs&dep=" + departement);
		}
		
		async function selectSemester(sem){
            semestre = sem;

            document.querySelector("#semestre").classList.remove("highlight");
            document.querySelector(".contenu").classList.add("ready");

            getStudentsListes();
            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('semestre', semestre);

			modules = await fetchData(`modules&semestre=${semestre}`);
		}

        async function getStudentsListes(){
            dataEtudiants = await fetchData(`listeEtudiantsSemestre&dep=${departement}&semestre=${semestre}&absences=true`);
            document.querySelector(".contenu").innerHTML = createSemester(dataEtudiants);

            changeDate(0);
        }

        function clearStorage(keys){
            keys.forEach(function(key){
                localStorage.removeItem(key);
            });
        }

        function createSemester(liste){
			var output = (statutSession >= ADMINISTRATEUR)?`
				<button onclick="createSemesterReport({boursiers:false})">Rapport d'absences</button>
				<button onclick="createSemesterReport({boursiers:true})">Rapport d'absences boursiers</button>
			`:"";

			if(config.data_absences_scodoc) {
				output += "<p>Attention, Scodoc et la passerelle ne gèrent pas les justifications de la même manière :<br><a target=_blank href=../services/messages.php#absencesMultiJours>Plus d'informations</a></p>";
			}

			var groupesOutput = "";
			let arrGroupes = Object.entries(liste.groupes);
            if(arrGroupes[0].length > 1){
                arrGroupes.forEach(([partition, groupes])=>{
					groupesOutput += `
					<div class=partition>
						<b>${partition}</b>
						<div>
							${createGroupes(groupes)}
						</div>
					</div>`;
                })
            }
            output += `
				<div class=flex>
					<div>
						<div class=groupes>${groupesOutput}</div>
						<div class=date>

							<svg onclick=changeDate(-1) xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>

							<div id=actualDate></div>

							<svg onclick=changeDate(1) xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>

						</div>
						<div class=etudiants>${createStudents(liste.etudiants)}</div>
					</div>
				</div>
            `;

            return output;
        }

		function createGroupes(groupesArray){
			let groupes = "";
			groupesArray.forEach(groupe=>{
				groupes += `<div class=groupe data-groupe="${groupe}" onclick="hideGroupe(this)">${groupe}</div>`;
			})
			return groupes;
		}

        function createStudents(etudiants){
			let output = `
				<div class=semaine>
					<div>Lundi</div>
					<div>Mardi</div>
					<div>Mercredi</div>
					<div>Jeudi</div>
					<div>Vendredi</div>
					<div>Samedi</div>
				</div>
			`;

			let calFrame = `
				<div class=dayStudent data-day=0 onmouseenter="showDay(this)" onmouseleave="stopShowDay(this)"></div>
				<div class=dayStudent data-day=1 onmouseenter="showDay(this)" onmouseleave="stopShowDay(this)"></div>
				<div class=dayStudent data-day=2 onmouseenter="showDay(this)" onmouseleave="stopShowDay(this)"></div>
				<div class=dayStudent data-day=3 onmouseenter="showDay(this)" onmouseleave="stopShowDay(this)"></div>
				<div class=dayStudent data-day=4 onmouseenter="showDay(this)" onmouseleave="stopShowDay(this)"></div>
				<div class=dayStudent data-day=5 onmouseenter="showDay(this)" onmouseleave="stopShowDay(this)"></div>
			`;

			etudiants.forEach(etudiant=>{
				let groupes = etudiant.groupes.join(" / ") || "Groupe1";
				output += `
					<div>
						<div class="btnAbsences" 
							data-nom="${etudiant.nom}" 
							data-prenom="${etudiant.prenom}" 
							data-groupe="${groupes}"
							data-nip="${etudiant.nip}"
                            title="${groupes} - Télécharger le rapport d'absence de l'étudiant"
                            onclick="createStudentReport(this)">
								<img src="../services/data.php?q=getStudentPic&nip=${etudiant.nip}" alt="etudiant" width="250" height="350">
								<div>
									<b>${etudiant.nom}</b>
									<span>${etudiant.prenom}</span>
								</div>
						</div>
						${calFrame}
					</div>
				`;
			})

			return output;
		}

		function showDay(obj){
			document.querySelector(".semaine").children[obj.dataset.day].classList.add("showDay");
		}
		function stopShowDay(obj){
			document.querySelector(".semaine").children[obj.dataset.day].classList.remove("showDay");
		}

		function hideGroupe(obj){
			let nbSelected = obj.parentElement.parentElement.querySelectorAll(".selected").length;
			let nbBtn = obj.parentElement.parentElement.querySelectorAll(".groupe").length;
			
			if(nbSelected == 0){
				Array.from(obj.parentElement.parentElement.querySelectorAll(".groupe")).forEach(e=>{
					e.classList.toggle("selected");
				})
			}
			obj.classList.toggle("selected");

			nbSelected = obj.parentElement.parentElement.querySelectorAll(".selected").length;
			if(nbSelected == nbBtn){
				Array.from(obj.parentElement.parentElement.querySelectorAll(".groupe")).forEach(e=>{
					e.classList.toggle("selected");
				})
			}
			
			let groupesSelected = [];
			obj.parentElement.parentElement.querySelectorAll(".groupe:not(.selected)").forEach(e=>{
				groupesSelected.push(e.dataset.groupe);
			})

			document.querySelectorAll(".btnAbsences").forEach(e=>{
				if(groupesSelected.some(valeur => e.dataset.groupe.includes(valeur))){
					e.parentElement.classList.remove("hide")
				} else {
					e.parentElement.classList.add("hide")
				}	
			})
        }

/*************************************/
/* Gestion des dates et des absences */
/*************************************/
        var dateLundi = new Date();
        let dayNumber = dateLundi.getDay();
        dayNumber -= dayNumber == 0 ? -6:1;
        dateLundi.setDate(dateLundi.getDate() - dayNumber);

        function changeDate(num){
            dateLundi.setDate(dateLundi.getDate() + num * 7);
			let dateSamedi = new Date(dateLundi);
            dateSamedi.setDate(dateLundi.getDate() + 5);
            document.querySelector("#actualDate").innerText = `Du lundi ${dateLundi.toLocaleDateString()} au samedi ${dateSamedi.toLocaleDateString()}`;

            let jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
            let dateTemp = new Date(dateLundi);
            let semaine = document.querySelector(".semaine");
            for(let i=0 ; i<6 ; i++){
                semaine.children[i].innerText = `${jours[i+1]} ${dateTemp.getDate()}`;
                dateTemp.setDate(dateTemp.getDate() + 1);
            }

            showAbsences();
        }

		function showAbsences(){
            document.querySelectorAll(".dayStudent").forEach(e=>e.innerHTML = "");

            Object.entries(dataEtudiants.absences).forEach(([etudiant, datesAbsences])=>{
				for(let i=0 ; i<6 ; i++){
					let date = new Date(dateLundi);
                    date.setDate(date.getDate() + i);
                    let absencesJour = datesAbsences[ISODate(date)];

					if(absencesJour && !Array.isArray(absencesJour)){
						message("Une erreur s'est produite avec les données, veuillez en informer le responsable passerelle.")
						console.log(`Problème - pas un tableau : ${etudiant} - ${ISODate(date)}`);
						continue;
					}

					absencesJour?.forEach(absence=>{
						let heureDebut = <?php echo $Config->absence_heureDebut; ?>;
						let heureFin = <?php echo $Config->absence_heureFin; ?>;

						let posiDebut = (absence.debut - heureDebut) / (heureFin - heureDebut) * 100;
						let tailleDuree = (absence.fin - absence.debut) / (heureFin - heureDebut) * 100;
						
						let ligne = document.querySelector(`[data-nip="${etudiant}"]`);

						if(ligne){
							ligne.parentElement.children[i+1].innerHTML += `
								<div 
									style="left:${posiDebut}%;width:${tailleDuree}%" 
									data-statut="${absence.statut}" 
									data-justifie="${absence.justifie}" 
									data-debut="${absence.debut}"
									data-fin="${absence.fin}"
									data-id="${absence.idJustif || ""}"
									title="${floatToHour(absence.debut)} - ${floatToHour(absence.fin)} - ${absence.enseignant}"
									onclick="${(absence.statut != "present") ? "justify(this)":""}">
								</div>`;

							if(config.data_absences_scodoc && ISODate(date) != absence.dateFin) {
								message("Attention, une absence sur plusieurs jours a été intégrée dans Scodoc, la passerelle ne le gère pas. <a target=_blank href=../services/messages.php#absencesMultiJours>Plus d'informations</a>");
							}
						}
					})
				}
            })
        }

		async function justify(obj){
			if(statutSession < ADMINISTRATEUR){
				return message("Seul un administrateur peut justifier une absence");
			}
			let trouve = depAdmins.find( e=>{
				return e.id == session
			})
            if(!trouve && statutSession < SUPERADMINISTRATEUR){
                return message("Vous ne pouvez pas modifier une absence d'un autre département");
            }

			if(config.data_absences_scodoc && obj.dataset.id.search(',') != -1) {
				return message("Plusieurs justificatifs Scodoc couvrent cette absence, la passerelle ne le gère pas, utilisez Scodoc pour réaliser les modifications.");
			}

			if(config.data_absences_scodoc && obj.dataset.id) {
				let regex = new RegExp(`"idJustif":\\[${obj.dataset.id}\\]`, "g")
				if(JSON.stringify(dataEtudiants.absences).match(regex).length > 1) {
					return message("La justification Scodoc couvre plusieurs absences, la passerelle ne le gère pas, utilisez Scodoc pour réaliser les modifications.");
				}
			}

			/********************/

            if(obj.dataset.justifie == "false"){
				obj.setAttribute("data-justifie", "true")
            } else {
                obj.setAttribute("data-justifie", "false")
            }

            let date = new Date(dateLundi);
            date.setDate(dateLundi.getDate() + parseInt(obj.parentElement.dataset.day));
            date = ISODate(date);
           
			obj.classList.add("waitResponse");
            let response = await fetchData("setJustifie" + 
                "&semestre=" + semestre +
                "&etudiant=" + obj.parentElement.parentElement.children[0].dataset.nip +
                "&date=" + date +
                "&debut=" + obj.dataset.debut +
                "&fin=" + obj.dataset.fin +
                "&justifie=" + obj.dataset.justifie +
                "&id=" + obj.dataset.id
            );

            if(response.result != "OK"){
                displayError("Il y a un problème - l'absence n'a pas été enregistrée.");
				return;
            }

			obj.dataset.id = response.id || "";

			obj.classList.remove("waitResponse");

            dataEtudiants.absences[obj.parentElement.parentElement.children[0].dataset.nip][date].forEach(function(e, index, array){
				if(e.debut == obj.dataset.debut){
					array[index].justifie = (obj.dataset.justifie == "true") ? true : false;
					array[index].idJustif = [response.id] || "";
				}
			})
        }

		function ISODate(date){
            // Date ISO du type : 2021-01-28T15:38:04.622Z -- on ne récupère que AAAA-MM-JJ.
            //return date.toISOString().split("T")[0]; // Problème d'heure UTC

			// Transforme la date en date ISO
			return date.toLocaleDateString().split("/").reverse().join("-")
        }

        function message(msg){
            var div = document.createElement("div");
            div.className = "message";
            div.innerHTML = msg;
            document.querySelector("body").appendChild(div);
            setTimeout(()=>{
                div.remove();
            }, 6000);
        }
		function floatToHour(heure){
			return Math.floor(heure) + "h"+ ((heure%1*60 < 10)?"0"+Math.round(heure%1*60) : Math.round(heure%1*60))
		}
/***************************/
/* Gestion des rapports d'absence
/***************************/
        function getExcel(obj, xlsxName) {
			return fetch(xlsxName)
				.then(function (response) { return response.blob() })
				.then(function (blob) {
					return blob;
				})
		}

		function saveFile(name, workbook) {
			workbook.outputAsync()
			.then(function (blob) {
				var url = window.URL.createObjectURL(blob);
				var a = document.createElement("a");
				document.body.appendChild(a);
				a.href = url;
				a.download = name + ".xlsx";
				a.click();
				window.URL.revokeObjectURL(url);
				document.body.removeChild(a);
			});
		}
        function createStudentReport(obj){
            let absences = dataEtudiants.absences[obj.dataset.email];
            let sem = document.querySelector("#semestre");
            let semestreTxt = sem.options[sem.selectedIndex].text;

			XlsxPopulate.fromBlankAsync()
            .then(workbook => {
                let now = new Date();
                now = now.toLocaleDateString();
                const sheet = workbook.sheet(0);
                sheet.name("Absences");

				sheet.column("A").width(14);
				sheet.column("B").width(16);
				sheet.column("C").width(22);
				sheet.column("D").width(36);

                sheet.cell("A1").value("Rapport d'absences").style("fontSize", 18);
                sheet.cell("A2").value(`${semestreTxt}`).style("fontSize", 24);
                sheet.cell("A3").value(`${obj.dataset.prenom} ${obj.dataset.nom}`).style("fontSize", 24);
                sheet.cell("A4").value(`${obj.dataset.nip}`);
                sheet.cell("A5").value(`${now}`);
                
			/*************************/
			/* Absences injustifiées */
			/*************************/
				var i = 7;
                sheet.cell("A"+i).value("Absences injustifiées").style("fontSize", 18);
				i++;

				sheet.cell("A"+i).value([[
						"Date",
						"Créneau",
						"Enseignant",
						"Matière"
					]]).style({
						bold: true,
						fill: "0099CC",
						fontColor: "FFFFFF"
					});
				i++;

				var total = 0;
				Object.entries(dataEtudiants.absences[obj.dataset.nip] || {}).forEach(([date, listeCreneaux])=>{
					listeCreneaux.forEach((data)=>{
						if(data.statut == "absent" && (data.justifie == "false" || data.justifie == false)){
							sheet.cell("A"+i).value(date.split("-").reverse().join("/"));
							sheet.cell("B"+i).value(floatToHour(data.debut) + " - " + floatToHour(data.fin));
							sheet.cell("C"+i).value(data.enseignant);
							sheet.cell("D"+i).value(getMatiere(data.matiereComplet));

							total += data.fin - data.debut;
							i++;
						}
					})
				})

				sheet.cell("A"+i).value(`Nombre d'absences injustifiées : ${floatToHour(total)}`);
				sheet.range("A"+i+":D"+i).style({
					bold: true,
					fill: "00CC99",
					fontColor: "FFFFFF"
				});

			/***********/
			/* Retards */
			/***********/
				i++;
				i++;
 				sheet.cell("A"+i).value("Retards").style("fontSize", 18);
				i++;

                sheet.cell("A"+i).value([[
						"Date",
						"Créneau",
						"Enseignant",
						"Matière"
					]]).style({
						bold: true,
						fill: "0099CC",
						fontColor: "FFFFFF"
					});
				i++;
				
				total = 0;
				Object.entries(dataEtudiants.absences[obj.dataset.nip] || {}).forEach(([date, listeCreneaux])=>{
					listeCreneaux.forEach((data)=>{
						if(data.statut == "retard" && (data.justifie == "false" || data.justifie == false)){
							sheet.cell("A"+i).value(date.split("-").reverse().join("/"));
							sheet.cell("B"+i).value(floatToHour(data.debut) + " - " + floatToHour(data.fin));
							sheet.cell("C"+i).value(data.enseignant);
							sheet.cell("D"+i).value(getMatiere(data.matiereComplet));

							total++;
							i++;
						}
					})
				})

				sheet.cell("A"+i).value(`Nombre de retards : ${total}`);
				sheet.range("A"+i+":D"+i).style({
					bold: true,
					fill: "00CC99",
					fontColor: "FFFFFF"
				});

			/***********************/
			/* Absences justifiées */
			/***********************/
				i++;
				i++;
 				sheet.cell("A"+i).value("Absences justifiées").style("fontSize", 18);
				i++;

                sheet.cell("A"+i).value([[
						"Date",
						"Créneau",
						"Enseignant",
						"Matière"
					]]).style({
						bold: true,
						fill: "0099CC",
						fontColor: "FFFFFF"
					});
				i++;
				
				total = 0;
				Object.entries(dataEtudiants.absences[obj.dataset.nip] || {}).forEach(([date, listeCreneaux])=>{
					listeCreneaux.forEach((data)=>{
						if(data.statut == "absent" && (data.justifie == "true" || data.justifie == true)){
							sheet.cell("A"+i).value(date.split("-").reverse().join("/"));
							sheet.cell("B"+i).value(floatToHour(data.debut) + " - " + floatToHour(data.fin));
							sheet.cell("C"+i).value(data.enseignant);
							sheet.cell("D"+i).value(getMatiere(data.matiereComplet));

							total += data.fin - data.debut;
							i++;
						}
					})
				})

				sheet.cell("A"+i).value(`Nombre d'absences justifiées : ${floatToHour(total)}`);
				sheet.range("A"+i+":D"+i).style({
					bold: true,
					fill: "00CC99",
					fontColor: "FFFFFF"
				});

                saveFile("Absences - " + semestreTxt + " " + obj.dataset.nom + " " + obj.dataset.prenom, workbook);
            });
        }

		function createSemesterReport(options){
            let sem = document.querySelector("#semestre");
            let semestreTxt = sem.options[sem.selectedIndex].text;

			XlsxPopulate.fromBlankAsync()
            .then(workbook => {
                let now = new Date();
                now = now.toLocaleDateString();
                const sheet = workbook.sheet(0);
                sheet.name("Absences");

				sheet.column("A").width(11);
				sheet.column("B").width(11);

                sheet.cell("A1").value("Rapport d'absences").style("fontSize", 18);
                sheet.cell("A2").value(`${semestreTxt}`).style("fontSize", 24);
                sheet.cell("A3").value(now);

                /***********/
				sheet.cell("G4").value("Détail nombre d'heures d'absences");
				sheet.range("G4:J4").style({
					bold: true,
					fill: "00CC99",
					fontColor: "FFFFFF"
				});
				sheet.cell("A5")
					.value([["Nom", "Prenom", "Numéro", "H absen.", "Nb retar.", "H justif.", "Septemb.", "Octobre", "Novemb.", "Décemb.", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septemb.", "Octobre", "Novemb.", "Décemb.", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout"]])
					.style({
						bold: true,
						fill: "0099CC",
						fontColor: "FFFFFF"
					});
				sheet.range("G5:R5").style({fill: "00CC99"});

				/***********/

				sheet.cell("S4").value("Nombre de jours avec au moins une absence");
				sheet.range("S4:W4").style({
					bold: true,
					fill: "0099CC",
					fontColor: "FFFFFF"
				});

				sheet.range("S5:AD5").style({fill: "0099CC"});

				/***********/
				
				var i = 6;

				var colonne = 'D';

				dataEtudiants.etudiants.forEach(etudiant=>{
					if( options.boursiers == true && etudiant.boursier != true ){
						return;
					}
					sheet.cell("A"+i).value([[
						etudiant.nom,
						etudiant.prenom,
						etudiant.nip
					]]);
					let totaux = {
						"01": 0,
						"02": 0,
						"03": 0,
						"04": 0,
						"05": 0,
						"06": 0,
						"07": 0,
						"08": 0,
						"09": 0,
						"10": 0,
						"11": 0,
						"12": 0,
						absent: 0,
						justifie: 0,
						retard: 0
					}
					let totauxJour = {
						"01": [],
						"02": [],
						"03": [],
						"04": [],
						"05": [],
						"06": [],
						"07": [],
						"08": [],
						"09": [],
						"10": [],
						"11": [],
						"12": []
					};

					Object.entries(dataEtudiants.absences[etudiant.nip] || {}).forEach(([date, liste])=>{
						liste.forEach(data=>{
							if(data.statut == "retard" && (data.justifie == "false" || data.justifie == false)){
								totaux.retard++;
							} else if(data.statut == "absent" && (data.justifie == "true" || data.justifie == true)){
								totaux.justifie += data.fin - data.debut;
							} else if(data.statut == "absent" && (data.justifie == "false" || data.justifie == false)){
								let mois = date.split("-")[1];
								totaux[mois] += data.fin - data.debut;
								totaux.absent += data.fin - data.debut;

								totauxJour[mois].push(date);
							}
						})
					})

					sheet.cell("D"+i).value([[
						totaux.absent,
						totaux.retard,
						totaux.justifie,
						totaux["09"],
						totaux["10"],
						totaux["11"],
						totaux["12"],
						totaux["01"],
						totaux["02"],
						totaux["03"],
						totaux["04"],
						totaux["05"],
						totaux["06"],
						totaux["07"],
						totaux["08"],
						[... new Set(totauxJour["09"])].length,
						[... new Set(totauxJour["10"])].length,
						[... new Set(totauxJour["11"])].length,
						[... new Set(totauxJour["12"])].length,
						[... new Set(totauxJour["01"])].length,
						[... new Set(totauxJour["02"])].length,
						[... new Set(totauxJour["03"])].length,
						[... new Set(totauxJour["04"])].length,
						[... new Set(totauxJour["05"])].length,
						[... new Set(totauxJour["06"])].length,
						[... new Set(totauxJour["07"])].length,
						[... new Set(totauxJour["08"])].length
					]])

					sheet.cell("D"+i).style({
						bold: true,
						fill: "ec7068",
						fontColor: "FFFFFF"
					});
					sheet.cell("E"+i).style({
						bold: true,
						fill: "f3a027",
						fontColor: "FFFFFF"
					});
					sheet.cell("F"+i).style({
						bold: true,
						fill: "00cc99",
						fontColor: "FFFFFF"
					});

					i++;
				})

                saveFile("Absences - " + semestreTxt, workbook);
            });
        }

		function changeChar(char, nb){
			return String.fromCharCode(char.charCodeAt(0) + nb);
		}

		/*function mailToTxt(mail){
			let tab = mail.split("@")[0].split(".");
			return tab[0].charAt(0).toUpperCase() + tab[0].slice(1) + " " + tab[1].toUpperCase();
		}*/

		function getMatiere(txt) {
			if(Number.isInteger(txt)) {
				let matiere = [...modules.modules, ...modules.saes].find(e => {
					return e.id == txt;
				});
				return matiere.code + ' - ' + matiere.titre;
			} else {
				return txt || "-";
			}
		}

/***************************/
/* C'est parti !
/***************************/
        checkStatut();
    </script>
    <?php 
        include "$path/config/analytics.php";
    ?>
</body>
</html>
