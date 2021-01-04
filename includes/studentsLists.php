<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents</title>
    <style>
        *{
            box-sizing: border-box;
        }
        html{
            scroll-behavior: smooth;
        }
        body{
            margin:0;
            font-family:arial;
            background: #FAFAFA;
        }
        header{
            position:sticky;
            top:0;
            padding:10px;
            background:#09C;
            display: flex;
            justify-content: space-between;
            color:#FFF;
            box-shadow: 0 2px 2px #888;
            z-index:1;
        }
        header>a{
            color: #FFF;
            text-decoration: none;
            padding: 10px 0 10px 0;
        }
        h1{
            margin:0;
        }
        h2{
            margin: 20px 0 0 0;
            padding: 20px;
            background: #0C9;
            color: #FFF;
            border-radius: 10px;
            cursor: pointer;
        }
        main{
            padding:0 10px;
            margin-bottom: 64px;
            max-width: 1000px;
            margin: 0 auto 20px auto;
        }
        .prenom{
            text-transform: capitalize;
            color:#f44335;
        }
        
        .wait{
            position: fixed;
            width: 50px;
            height: 10px;
            background: #424242;
            top: 80px;
            left: 50%;
            margin-left: -25px;
            animation: wait 0.6s ease-out alternate infinite;
        }
        @keyframes wait{
            100%{transform: translateY(-30px) rotate(360deg)}
        }

        .auth{
            position: fixed;
            top: 58px;
            left: 0;
            right: 0;
            bottom: 0;
            background: #FAFAFA;
            font-size: 28px;
            padding: 28px 10px 0 10px;
            text-align: center;
            transition: 0.4s;
        }
/*******************************/
/* Listes étudiants */
/*******************************/
        .flex{
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .groupes{
            margin-left: 20px;
            margin-bottom: 10px;
			display: flex;
        }
        .groupe{
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 10px;
            margin: 2px;
            background: #09C;
            color: #FFF;
            border-radius: 8px;
        }
        @media screen and (max-width: 700px){
            .flex{
                flex-direction: column-reverse;
                align-items: center;
            }
            .groupes{
                margin-right: 20px;
                justify-content: center;
            }
        }
        .selected{
            opacity: 0.5;
        }
        .hide{
            display: none;
        }
        .etudiants{
            counter-reset: cpt;
            margin-left: 20px;
        }
		.etudiants>div:nth-child(odd){
			background: #eee;
		}
        .etudiants>div:before{
            counter-increment: cpt;
            content: counter(cpt) " - " attr(data-groupe);
			display: inline-block;
			min-width: 100px;
        }
		.load path{
			animation: chargement 0.4s infinite linear;
		}
        @keyframes chargement{
            0%{stroke-dasharray: 25;}
            100%{stroke-dasharray: 25;stroke-dashoffset:100;}
        }
    </style>
</head>
<body>
    <header>

			<h1>
				Documents
			</h1>
			<a href=/logout.php>Déconnexion</a>
		</header>
        <main>
			<p>
				Bonjour <span class=prenom></span>.
			</p>
            <div class=contenu></div>
			<div class=wait></div>
			
		</main>

		<div class=auth>
			<!-- Site en maintenance -->
			Authentification en cours ...
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
    <script>
        checkStatut();

/*********************************************/
/* Fonction de communication avec le serveur
Gère la déconnexion et les messages d'erreur
/*********************************************/
        function fetchData(query){
            document.querySelector(".wait").style.display = "block";
            let token = (window.location.search.match(/token=([a-zA-Z0-9._-]+)/)?.[1] || ""); // Récupération d'un token GET pour le passer au service
            if(token){
                var postData = new FormData();
                postData.append('token', token);
            }
            return fetch(
                "/services/data.php?q="+query, 
                {
                    method: "post",
                    body: token ? postData : ""
                }
            )
            .then(res => { return res.json() })
            .then(function(data) {
                document.querySelector(".wait").style.display = "none";
                if(data.redirect){
                    // Utilisateur non authentifié, redirection vers une page d'authentification pour le CAS.
                    // Passage de l'URL courant au CAS pour redirection après authentification
                    window.location.href = data.redirect + "?href="+encodeURIComponent(window.location.href); 
                }
                if(data.erreur){
                    // Il y a une erreur pour la récupération des données - affichage d'un message explicatif.
                    document.querySelector(".contenu").innerHTML = `<b>${data.erreur}</b>`;
                }else{
                    return data;
                }
            })
        }

        function displayError(message){
            let auth = document.querySelector(".auth");
            auth.style.opacity = "1";
            auth.style.pointerEvents = "initial";
            auth.innerHTML = message;
        }
/*********************************************/
/* Vérifie l'identité de la personne et son statut
/*********************************************/			
        async function checkStatut(){
            let data = await fetchData("donnéesAuthentification");
            document.querySelector(".prenom").innerText = data.session.split(".")[0];
            let auth = document.querySelector(".auth");
            auth.style.opacity = "0";
            auth.style.pointerEvents = "none";

            if(data.statut == 'personnel'){
                getStudentsListes(window.location.pathname.replace(/\//g,"")); // Répertoir courant - exemple : MMI
            } else {
                document.querySelector(".contenu").innerHTML = "Ce contenu est uniquement accessible pour les personnels de l'IUT. ";
            }
        }
/*********************************************/
/* Récupère et traite les listes d'étudiants du département
/*********************************************/		
        async function getStudentsListes(departement){
            let data = await fetchData("listesEtudiantsDépartement&dep="+departement);
            document.querySelector(".contenu").innerHTML = createSemester(data);
        }

        function createSemester(data){
			var output = "";

			data.forEach(semestre=>{
                var groupes = "";
                if(semestre.groupes.length > 1){
                    semestre.groupes.forEach(groupe=>{
                        var num = groupe?.replace(/ /g, "") || "Groupe1";
                        groupes += `<div class=groupe onclick="hideGroupe(this, '${num}')">${groupe || "Groupe 1"}</div>`;
                    })
                }else{
                    groupes = `<div class=groupe onclick="hideGroupe(this, 'Groupe1')">Groupe 1</div>`;
                }
				output += `
                    <h2 onclick="hideSemester(this)">${semestre.titre}</h2>
                    <div class="flex hide">
                        <div>
                            <div class="groupes">${groupes}</div>
                            <div class="etudiants">${createStudents(semestre.etudiants)}</div>
                        </div>
						<div>
							<div class=groupe onclick="processSigning(this)">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path pathLenght="100" d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5"/></svg>
								Feuille d'émargement
							</div>
							<div class=groupe onclick="processGroups(this)">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path pathLenght="100" d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5"/></svg>
								Groupes étudiants
							</div>
                            <div class=groupe onclick="processNotes(this)">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path pathLenght="100" d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5"/></svg>
								Retours notes
							</div>
                            <div class=groupe onclick="processStudentsData(this)">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path pathLenght="100" d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5"/></svg>
								Données étudiants
							</div>
						</div>
                    </div>
				`;
			})
            return output;
        }

        function createStudents(etudiant){
			var output = "";
           
			etudiant.forEach(etudiant=>{
				output += `
					<div class="${etudiant.groupe?.replace(/ /g, "") || "Groupe1"}" 
                        data-nom="${etudiant.nom}" 
                        data-prenom="${etudiant.prenom}" 
                        data-groupe="${etudiant.groupe || "Groupe 1"}"
                        data-num="${etudiant.num_etudiant}"
                        data-email="${etudiant.email}">
                            ${etudiant.nom} ${etudiant.prenom}
                    </div>
				`;
			})
			return output;
		}

        function hideSemester(obj){
            obj.nextElementSibling.classList.toggle("hide");
        }
		function hideGroupe(obj, num){
			obj.classList.toggle("selected");
			obj.parentElement.nextElementSibling.querySelectorAll('.'+num).forEach(e=>{
				e.classList.toggle("hide");
			})
        }
        
        
/*********************************************/
/* Gestion du téléchargement XLSX des données - Utilisation de xlsx-populate
/*********************************************/	
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
				a.download = name;
				a.click();
				window.URL.revokeObjectURL(url);
				document.body.removeChild(a);
			});
		}

		async function processSigning(obj){
			obj.classList.add("load");
			var blob = await getExcel(obj, '/documents/Emargements.xlsx');
			XlsxPopulate.fromDataAsync(blob)
            .then(workbook => {
                var h2 = obj.parentElement.parentElement.previousElementSibling;
                var groupes = [...h2.nextElementSibling.querySelectorAll(".groupes>.groupe:not(.selected)")].map(function(e) { return e.innerText; })
                const sheet = workbook.sheet("Emargements");
                sheet.cell("A4").value(h2.innerText);
                sheet.cell("A5").value(groupes.join(", "));

                var i = 7;
                h2.nextElementSibling.querySelectorAll(".etudiants>div:not(.hide)").forEach(etudiant=>{
                    sheet.row(i).height(38);
                    sheet.cell("A"+i).value(etudiant.dataset.nom).style("border", true);
                    sheet.cell("B"+i).value(etudiant.dataset.prenom).style("border", true);
                    sheet.cell("C"+i++).style("border", true);

                })
                
                saveFile("Emargement - " + h2.innerText + " " + groupes.join(", "), workbook);
                obj.classList.remove("load");
            });
		}

		async function processGroups(obj){
			obj.classList.add("load");
			XlsxPopulate.fromBlankAsync()
            .then(workbook => {
                var h2 = obj.parentElement.parentElement.previousElementSibling;
                var groupes = [...h2.nextElementSibling.querySelectorAll(".groupes>.groupe:not(.selected)")].map(function(e) { return e.innerText; })
                const sheet = workbook.sheet(0);
                sheet.name("Groupes");
                sheet.cell("A1").value(h2.innerText).style("fontSize", 24);

                var column = 65;
                groupes.forEach(function(groupe){
                    sheet.column(String.fromCharCode(column)).width(26);
                    sheet.cell(String.fromCharCode(column) + 3).value(groupe).style({
                        border: true,
                        bold: true,
                        fontSize: 16
                    });
                    var line = 4;
                    h2.nextElementSibling.querySelectorAll("." + groupe.replace(/ /g, "")).forEach(etudiant=>{

                        sheet.cell(String.fromCharCode(column) + line).value(etudiant.innerText);
                        line++;
                    });
                    column = column + 2;
                });

                saveFile("Groupes - " + h2.innerText + " " + groupes.join(", "), workbook);
                obj.classList.remove("load");
            });
		}

        async function processNotes(obj){
			obj.classList.add("load");
			XlsxPopulate.fromBlankAsync()
            .then(workbook => {
                var h2 = obj.parentElement.parentElement.previousElementSibling;
                var groupes = [...h2.nextElementSibling.querySelectorAll(".groupes>.groupe:not(.selected)")].map(function(e) { return e.innerText; })
                const sheet = workbook.sheet(0);
                sheet.name("Groupes");
                sheet.cell("A1").value("Retours des notes").style("fontSize", 24)
                sheet.cell("A2").value("Groupes " + h2.innerText).style("fontSize", 18);
                sheet.cell("A3").value(groupes.join(", "));
                sheet.cell("A5").value("Ces notes sont à transmettre au reponsable pour intégration dans Scodoc.")

                sheet.cell("E6").value("Date").style("bold", true).style("horizontalAlignment", "right");
                sheet.cell("E7").value("Nom de l'enseignant").style("bold", true).style("horizontalAlignment", "right");
                sheet.cell("E8").value("Nom du module").style("bold", true).style("horizontalAlignment", "right");
                sheet.cell("E9").value("Intitulé du partiel").style("bold", true).style("horizontalAlignment", "right");
                sheet.cell("E10").value("Coefficient dans le module").style("bold", true).style("horizontalAlignment", "right");
                
                for(let i=6; i<11; i++){
                    sheet.cell("F"+i).style("bottomBorder", true).style("fill", "FFC000");
                }

                sheet.cell("E14").value(" ABS : étudiant absent non justifié, compte comme 0");
                sheet.cell("E15").value(" EXC : étudiant absent justifié, pas de rattrapage prévu, note neutralisée");
                sheet.cell("E16").value(" ATT : note non encore attribuée laissée en attente");

                sheet.cell("A6").value("Nom").style("bold", true);
                sheet.cell("B6").value("Prénom").style("bold", true);
                sheet.cell("C6").value("Groupe").style("bold", true);
                sheet.cell("D6").value("Note /20").style("bold", true);

                var i = 7;
                h2.nextElementSibling.querySelectorAll(".etudiants>div:not(.hide)").forEach(etudiant=>{
                    sheet.cell("A"+i).value(etudiant.dataset.nom);
                    sheet.cell("B"+i).value(etudiant.dataset.prenom);
                    sheet.cell("C"+i).value(etudiant.dataset.groupe);
                    sheet.cell("D"+i++).style("fill", "FFC000");
                })

                sheet.column("A").width(20);
                sheet.column("B").width(14);
                sheet.column("E").width(25);
                sheet.column("F").width(25);

                saveFile("Retour Notes - " + h2.innerText + " " + groupes.join(", "), workbook);
                obj.classList.remove("load");
            });
		}

        async function processStudentsData(obj){
			obj.classList.add("load");
			XlsxPopulate.fromBlankAsync()
            .then(workbook => {
                var h2 = obj.parentElement.parentElement.previousElementSibling;
                var groupes = [...h2.nextElementSibling.querySelectorAll(".groupes>.groupe:not(.selected)")].map(function(e) { return e.innerText; })
                const sheet = workbook.sheet(0);
                sheet.name("Données");
                sheet.cell("A1").value("Données étudiants").style("fontSize", 24)
                sheet.cell("A2").value(h2.innerText).style("fontSize", 18);
                sheet.cell("A3").value(groupes.join(", "));

                sheet.cell("B5").value("Nom").style("bold", true);
                sheet.cell("C5").value("Prénom").style("bold", true);
                sheet.cell("D5").value("Groupe").style("bold", true);
                sheet.cell("E5").value("Num étudiant").style("bold", true);
                sheet.cell("F5").value("Email UHA").style("bold", true);

                var i = 6;
                h2.nextElementSibling.querySelectorAll(".etudiants>div:not(.hide)").forEach(etudiant=>{
                    sheet.cell("A"+i).value(i-5);
                    sheet.cell("B"+i).value(etudiant.dataset.nom);
                    sheet.cell("C"+i).value(etudiant.dataset.prenom);
                    sheet.cell("D"+i).value(etudiant.dataset.groupe);
                    sheet.cell("E"+i).value(etudiant.dataset.num);
                    sheet.cell("F"+i).value(etudiant.dataset.email);
                    i++;
                });

                sheet.column("A").width(4);
                sheet.column("B").width(20);
                sheet.column("C").width(14);
                sheet.column("D").width(10);
                sheet.column("E").width(14);
                sheet.column("F").width(30);

                saveFile("Données étudiants - " + h2.innerText + " " + groupes.join(", "), workbook);
                obj.classList.remove("load");
            });
		}

    </script>
    <?php 
        $path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
        include "$path/includes/analytics.php";
    ?>
</body>
</html>