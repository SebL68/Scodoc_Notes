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
		select{
			font-size: 21px;
			padding: 10px;
			margin: 5px;
			background: #09c;
			color: #FFF;
			border: none;
			border-radius: 10px;
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
        .etudiants{
            counter-reset: cpt;
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
        }
        .date>svg{
            cursor: pointer;
        }
        .btnAbsences{
            position: relative;
            text-align: left;
            border-radius: 10px;
            box-shadow: 0 2px 2px 2px #ddd;
            border: 1px solid transparent;
            background: #FFF;
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
            transition: 0.1s;  
        }
        .btnAbsences>div:nth-child(1){
            display: flex;
            gap:5px;
        }
        .btnAbsences>div:nth-child(1)::before{
            counter-increment: cpt;
            content: counter(cpt) " - ";
            display: inline-block;
        }
        .btnAbsences>div:nth-child(1)>:last-child{
            margin-left: auto;
        }
        .btnAbsences:active{
            transform: translateY(2px);
            box-shadow: 0 0 0 0 #777;
            border: 1px solid #777;
            transition: 0s;
        }
        .hint{
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: 1fr;
            transform: translateY(8px);
        }
        .hint>div{
            outline: 1px solid #AAA;
            height: 8px;

        }
        .hint>.now{
            outline: none;
        }
        .absent{
            background: #ec7068;
            color: #FFF;
        }
        .excuse{
            background: #0C9;
        }
        .validate{
            position: absolute;
            left: calc(100% + 5px);
            top: 0;
            margin: 0;
            padding: 0;
        }
    </style>
    <meta name=description content="Gestion des absences de l'IUT de Mulhouse">
</head>
<body>
    <header>

        <h1>
            Absences
        </h1>
        <a href=/logout.php>Déconnexion</a>
    </header>
    <main>
        <p>
            Bonjour <span class=prenom></span>.
        </p>

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

        <select id=semestre onchange="clearStorage(['matiere']);selectSemester(this.value)" disabled>
            <option value="" disabled selected hidden>Choisir un semestre</option>
        </select>

        <select id=matiere onchange="selectMatiere(this.value)" disabled>
            <option value="" disabled selected hidden>Choisir une matière</option>
        </select>

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
/*********************************************/
/* Vérifie l'identité de la personne et son statut
/*********************************************/		
        var session = "";
        var statut = "";
        async function checkStatut(){
            let data = await fetchData("donnéesAuthentification");
            session = data.session;
            document.querySelector(".prenom").innerText = data.session.split(".")[0];
            let auth = document.querySelector(".auth");
            auth.style.opacity = "0";
            auth.style.pointerEvents = "none";
            statut = data.statut;

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
        var matiere = "";
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
            document.querySelector("#matiere").disabled = true;

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
            let data = await fetchData(`UEEtModules&dep=${departement}&semestre=${semestre}`);

			let select = document.querySelector("#matiere");
			select.innerHTML = `<option value="" disabled selected hidden>Choisir une matière</option>`;
			data.forEach(function(ue){
                let optgroup = document.createElement("optgroup");
                optgroup.label = ue.UE;

                ue.modules.forEach(function(module){
                    let option = document.createElement("option");
                    option.value = module.code;
                    option.innerText = module.titre;
                    optgroup.appendChild(option);
                });

				select.appendChild(optgroup);
            });
            document.querySelector("#semestre").classList.remove("highlight");
            select.disabled = false;
            document.querySelector(".contenu").classList.remove("ready");
            select.classList.add("highlight");

            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('semestre', semestre);

            let matiere = localStorage.getItem("matiere");
            if(matiere){
                document.querySelector("#matiere").value = matiere;
                selectMatiere(matiere);
            }

            getStudentsListes();
		}
        
        async function selectMatiere(mat){
            matiere = mat;
            document.querySelector(".contenu").classList.add("ready");
            document.querySelector("#matiere").classList.remove("highlight");
            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('matiere', matiere);
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

                            <div id=actualDate>${actualDate()}</div>

                            <svg onclick=changeDate(1) xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>

                        </div>
                        <div class=etudiants>${createStudents(liste.etudiants)}</div>
                    </div>
                    
                </div>
            `;

            return output;
        }

        function createStudents(etudiants){
			var output = "";
           
			etudiants.forEach(etudiant=>{
				output += `
					<div class="btnAbsences ${etudiant.groupe?.replace(/ |\./g, "") || "Groupe1"}"  onclick="absent(this)"
                        data-nom="${etudiant.nom}" 
                        data-prenom="${etudiant.prenom}" 
                        data-groupe="${etudiant.groupe || "Groupe 1"}"
                        data-num="${etudiant.num_etudiant}"
                        data-email="${etudiant.email}">
                            <div>
                                <b>${etudiant.nom}</b>
                                <span>${etudiant.prenom}</span>
                            </div>
                            ${hintHours}
                            ${
                                (()=>{
                                    if(statut > PERSONNEL){
                                        return `<div class=validate onclick="justify(event, this)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#00b0ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline><path fill="#FFFFFF" stroke="#424242" d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path><polyline stroke="#00CC99" points="9 11 12 14 22 4"></svg>
                                        </div>`;
                                    }
                                })()
                            }
                    </div>
				`;
			})
			return output;
		}

		function hideGroupe(obj, num){
			obj.classList.toggle("selected");
			obj.parentElement.nextElementSibling.nextElementSibling.querySelectorAll('.'+num).forEach(e=>{
				e.classList.toggle("hide");
			})
        }

/*************************************/
/* Gestion des dates et des absences */
/*************************************/
        var date = new Date();
        let heure = date.getHours() + date.getMinutes() / 60; // Heure en décimale : exemple 10h30 => 10.5
        
        var creneaux = <?php
            include_once "$path/includes/config.php";
            echo json_encode($creneaux);
		?>;
        var creneauxIndex = creneaux.length -1;

        let hintHours = `<div class=hint>${
            creneaux.map(e=>{
                return `<div title="Créneau ${e[0]}-${e[1]}"></div>`
            }).join("")
        }</div>`;

        for(let i=0 ; i<creneaux.length ; i++){
            if(heure  < creneaux[i][1] - 0.5){ // 30 min avant la fin on change de créneaux
                creneauxIndex = i;
                break;
            }
        }

        function actualDate(){
            let jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
            return `${jours[date.getDay()]} ${date.toLocaleDateString()} - ${creneaux[creneauxIndex][0]}h / ${creneaux[creneauxIndex][1]}h`;
        }

        function changeDate(num){

            document.querySelectorAll(".hint").forEach(e=>{
                e.children[creneauxIndex].classList.remove("now");
            })

            creneauxIndex += num;
            if(creneauxIndex < 0){
                date.setDate(date.getDate() - 1);
                creneauxIndex = creneaux.length-1;
            }else if(creneauxIndex > creneaux.length - 1){
                date.setDate(date.getDate() + 1);
                creneauxIndex = 0;
            }
            document.querySelector("#actualDate").innerHTML = actualDate();

            setAbsences();
        }

        async function absent(obj){
            
            var date = ISODate();
            var numAbsence = null;
            var absencesEtudiant = dataEtudiants.absences[obj.dataset.email];
        // Recherche s'il y a une absence et si un autre enseignant l'a entré
            if(absencesEtudiant){
                for(let i = 0, n = absencesEtudiant.length ; i < n ; i++){
                    if(absencesEtudiant[i].date == date && absencesEtudiant[i].creneau == creneaux[creneauxIndex]){
                        numAbsence = i;
                        break;
                    }
                }

                if((absencesEtudiant[numAbsence]?.enseignant || session) != session){
                    return message("Vous ne pouvez changer l'absence d'un autre enseignant : <span class=capitalize>" + absencesEtudiant[numAbsence].enseignant.split("@")[0].replace(/[.]/g, " ") + "</span>");
                }
            }
        
        // Toggle de l'absence
            if(obj.classList.toggle("absent")){
                var statut = "absent";
                var structure = {
                    "date": ISODate(),
                    "creneau": creneaux[creneauxIndex],
                    "creneauxIndex": creneauxIndex,
                    "statut": statut
                };
                // Ajouter l'absence au tableau
                if(absencesEtudiant){
                    absencesEtudiant.push(structure);
                }else{
                    dataEtudiants.absences = [structure];
                }
                
            } else {
                var statut = "présent";
                // Supprimer l'absence du tableau
                absencesEtudiant?.splice(numAbsence, 1);

            }

        // Sauvegarde de l'absence sur le serveur
            let response = await fetchData("setAbsence" + 
                "&dep=" + departement +
                "&semestre=" + semestre +
                "&matiere=" + matiere +
                "&etudiant=" + obj.dataset.email +
                "&date=" + date +
                "&creneau=" + creneaux[creneauxIndex] +
                "&creneauxIndex=" + creneauxIndex +
                "&statut=" + statut
            );
            if(response.result != "OK"){
                displayError("Il y a un problème - l'absence n'a pas été enregistrée.");
            }
        }

        function setAbsences(){
            document.querySelectorAll(".absent").forEach(e=>e.classList.remove("absent"));

            var date = ISODate();

            for(var etudiant in dataEtudiants.absences){
                dataEtudiants.absences[etudiant].forEach(function(absence){
                    if(absence.date == date){
                        let ligne = document.querySelector(`[data-email="${etudiant}"]`);
                        if(absence.creneau == creneaux[creneauxIndex]){
                            ligne.classList.add(absence.statut);
                        } else {
                            ligne.children[1].children[absence.creneauxIndex].classList.add(absence.statut);
                        }
                        
                        
                    }
                });
                
            };

            document.querySelectorAll(".hint").forEach(e=>{
                e.children[creneauxIndex].classList.add("now");
            })
        }

        function justify(event, obj){
            event.stopPropagation();
            console.log(obj);
        }

        function ISODate(){
            // Date ISO du type : 2021-01-28T15:38:04.622Z -- on ne récupère que AAAA-MM-JJ.
            return date.toISOString().split("T")[0];
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