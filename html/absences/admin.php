<?php 
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absences</title>
    <style>
		:root{
			--nb-creneaux: <?php 
					include_once "$path/includes/config.php";
					echo count($creneaux); 
				?>;
		}
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
            align-items: center;
            gap: 16px;
            color:#FFF;
            box-shadow: 0 2px 2px #888;
            z-index:1;
        }
        header a{
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
            text-align: center;
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
        .contenu{
            opacity: 0.5;
            pointer-events: none;
            user-select: none;
        }
        .ready{
            opacity: initial;
            pointer-events: initial;
        }
        .message{
            position: fixed;
            bottom: 100%;
            left: 50%;
            z-index: 10;
            padding: 20px;
            border-radius: 0 0 10px 10px;
            background: #ec7068;
            background: #90c;
            color: #FFF;
            font-size: 24px;
            animation: message 3s;
            transform: translate(-50%, 0);
        }
        @keyframes message{
            20%{transform: translate(-50%, 100%)}
            80%{transform: translate(-50%, 100%)}
        }
        .capitalize{
            text-transform: capitalize;
        }
/**********************/
/*   Zones de choix   */
/**********************/
        .zone{
            background: #FFF;
            padding: 8px;
            margin-bottom: 8px;
            border-radius: 4px;
            border: 1px solid #CCC;
        }
		select{
			font-size: 21px;
			padding: 10px;
			margin: 5px auto;
			background: #09c;
			color: #FFF;
			border: none;
			border-radius: 10px;
            max-width: 100%;
            display: table;
            box-shadow: 0 2px 2px #888;
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
        .flex{
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        
        .groupes{
            margin-bottom: 10px;
			display: flex;
            justify-content: center;
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
            display: none !important;
        }

/*****************************/
/* Zone absences */
/*****************************/
        .date{
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 20px;
            background: #0C9;
            color: #FFF;
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
			grid-column: span var(--nb-creneaux);
			cursor: initial !important;
			transition-delay: .035s;
			border-color: #09c;
		}
		.semaine>div:nth-child(1){
			grid-column: 2 / span var(--nb-creneaux);
		}
		.etudiants>div:hover:not(.semaine)>div:nth-child(1), .showDay{
			background: #c09 !important;
			color: #FFF;
			transition-delay: 0s !important;
			border-color: #c09 !important;
		}
		.etudiants>div{
			display: grid;
			grid-template-columns: 300px repeat(calc(var(--nb-creneaux) * 6), 24px);
			gap: 1px;
		}
		.etudiants>div>div{
			border-radius: 10px;
            border: 1px solid #eee;
            background: #FFF; 
            cursor: pointer;
		}
		.etudiants>div:not(.semaine)>div:hover{
			border: 1px solid #777;
		}

        .etudiants .btnAbsences{
			overflow: hidden;
            position: relative;
            text-align: left;
            padding: 10px 20px;
			cursor: initial;
			border-color: #09c;
        }
        .btnAbsences>div:nth-child(1){
            display: flex;
            gap:5px;
        }
        .btnAbsences>div:nth-child(1)::before{
            counter-increment: cpt;
            content: counter(cpt) " ";
            display: inline-block;
        }
        .btnAbsences>div:nth-child(1)>:last-child{
            margin-left: auto;
        }
        .absent{
            background: #ec7068 !important;
            color: #FFF;
        }
        .excuse{
            background: #0C9 !important;
        }
    </style>
    <meta name=description content="Gestion des absences de l'IUT de Mulhouse">
</head>
<body>
    <header>
        <h1>
            <a href="index.php">Absences</a>
        </h1>
        <a href=/logout.php>Déconnexion</a>
    </header>
    <main>
        <p>
            Bonjour <span class=prenom></span>.
        </p>

        <div class="zone">
            <select id=departement class=highlight onchange="clearStorage(['semestre', 'matiere']);selectDepartment(this.value)">
                <option value="" disabled selected hidden>Choisir un département</option>
                <?php
                    include "$path/includes/serverIO.php";
                    $listDepartement = getDepartmentsList();
                    foreach($listDepartement as $departement){
                        echo "<option value=$departement>$departement</option>";
                    }
                ?>
            </select>

            <select id=semestre onchange="selectSemester(this.value)" disabled>
                <option value="" disabled selected hidden>Choisir un semestre</option>
            </select>
        </div>
   

        <div class=contenu></div>
        <div class=wait></div>
        
    </main>

    <div class=auth>
        <!-- Site en maintenance -->
        Authentification en cours ...
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
    <script>
		<?php
            include "$path/includes/clientIO.php";
		?>  

		var creneaux = <?php
            include_once "$path/includes/config.php";
            echo json_encode($creneaux);
		?>;
/*********************************************/
/* Vérifie l'identité de la personne et son statut
/*********************************************/		
        var session = "";
        var statutSession = "";
        async function checkStatut(){
            let data = await fetchData("donnéesAuthentification");
            session = data.session;
            document.querySelector(".prenom").innerText = data.session.split(".")[0];
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
        var dataEtudiants;

        async function selectDepartment(dep){
            departement = dep;
			let data = await fetchData("semestresDépartement&dep="+departement);
			
			let select = document.querySelector("#semestre");
			select.innerHTML = `<option value="" disabled selected hidden>Choisir un semestre</option>`;
			data.forEach(function(semestre){
				let option = document.createElement("option");
				option.value = semestre.semestre_id;
				option.innerText = semestre.titre;
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
                selectSemester(semestre);
            }
		}
		
		async function selectSemester(sem){
            semestre = sem;

            document.querySelector("#semestre").classList.remove("highlight");
            document.querySelector(".contenu").classList.add("ready");

            getStudentsListes();
            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('semestre', semestre);
		}

        async function getStudentsListes(){
            dataEtudiants = await fetchData(`listeEtudiantsSemestre&dep=${departement}&semestre=${semestre}&absences=true`);
            document.querySelector(".contenu").innerHTML = createSemester(dataEtudiants);

            setAbsences();  
        }

        function clearStorage(keys){
            keys.forEach(function(key){
                localStorage.removeItem(key);
            });
        }

        function createSemester(liste){
			var output = "";

            var groupes = "";
            if(liste.groupes.length > 1){
                liste.groupes.forEach(groupe=>{
                    var num = groupe?.replace(/ |\./g, "") || "Groupe1";
                    groupes += `<div class=groupe onclick="hideGroupe(this, '${num}')">${groupe || "Groupe 1"}</div>`;
                })
            }else{
                groupes = `<div class=groupe onclick="hideGroupe(this, 'Groupe1')">Groupe 1</div>`;
            }
            output += `
				<div class=flex>
					<div>
						<div class=groupes>${groupes}</div>
						<div class=date>

							<svg onclick=changeDate(-1) xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>

							<div id=actualDate>Choisissez la semaine</div>

							<svg onclick=changeDate(1) xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>

						</div>
						<div class=etudiants>${createStudents(liste.etudiants)}</div>
					</div>
				</div>
            `;

            return output;
        }

        function createStudents(etudiants){
			var output = `
				<div class=semaine>
					<div>Lundi</div>
					<div>Mardi</div>
					<div>Mercredi</div>
					<div>Jeudi</div>
					<div>Vendredi</div>
					<div>Samedi</div>
				</div>
			`;
			var calFrame = "";

			for(let i=0 ; i<creneaux.length * 6 ; i++){
				calFrame += `
					<div 
						data-num="${Math.floor(i/creneaux.length)}" 
						title="${creneaux[i%creneaux.length][0]}-${creneaux[i%creneaux.length][1]}" 
						onmouseenter="showDay(this)" 
						onmouseout="stopShowDay(this)"
						onclick="justify(this)">
					</div>`;
			}
           
			etudiants.forEach(etudiant=>{
				output += `
					<div>
						<div class="btnAbsences ${etudiant.groupe?.replace(/ |\./g, "") || "Groupe1"}" 
							data-nom="${etudiant.nom}" 
							data-prenom="${etudiant.prenom}" 
							data-groupe="${etudiant.groupe || "Groupe 1"}"
							data-num="${etudiant.num_etudiant}"
							data-email="${etudiant.email}">
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
			document.querySelector(".semaine").children[obj.dataset.num].classList.add("showDay");
		}
		function stopShowDay(obj){
			document.querySelector(".semaine").children[obj.dataset.num].classList.remove("showDay");
		}

		function hideGroupe(obj, num){
			obj.classList.toggle("selected");
			obj.parentElement.nextElementSibling.nextElementSibling.querySelectorAll('.'+num).forEach(e=>{
				e.parentElement.classList.toggle("hide");
			})
        }

/*************************************/
/* Gestion des dates et des absences */
/*************************************/
		function setAbsences(){
			
		}

		async function justify(obj){

			if(statut < ADMINISTRATEUR){
				return message("Seul un administrateur peut justifier une absence");
			}
			if(!obj.classList.contains("absent")){
				return message("Vous ne pouvez pas justifier s'il n'y a pas d'absence");
			} 
            if(obj.classList.toggle("excuse")){
                var statut = "absent excuse";
            } else {
                var statut = "absent";
            }

           /* var date = ISODate();
            dataEtudiants.absences[obj.dataset.email][date][creneaux[creneauxIndex]].statut = statut;

            let response = await fetchData("setAbsence" + 
                "&dep=" + departement +
                "&semestre=" + semestre +
                "&matiere=" + matiere +
                "&matiereComplet=" + matiereComplet +
                "&etudiant=" + obj.dataset.email +
                "&date=" + date +
                "&creneau=" + creneaux[creneauxIndex] +
                "&creneauxIndex=" + creneauxIndex +
                "&statut=" + statut
            );
            if(response.result != "OK"){
                displayError("Il y a un problème - l'absence n'a pas été enregistrée.");
            }*/
            
        }
       
        function message(msg){
            var div = document.createElement("div");
            div.className = "message";
            div.innerHTML = msg;
            document.querySelector("body").appendChild(div);
            setTimeout(()=>{
                div.remove();
            }, 3000);
        }
/***************************/
/* C'est parti !
/***************************/
        checkStatut();
    </script>
    <?php 
        include "$path/includes/analytics.php";
    ?>
</body>
</html>