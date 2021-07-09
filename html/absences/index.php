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
        <?php include "$path/html/assets/header.css"?>
        .admin{
            background: #FFF;
            color: #424242;
            margin-left: auto;
            padding: 8px 16px;
            border-radius: 16px;
        }
        main{
            margin: 0 auto 20px auto;
            text-align: center;
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
            border: none;
        }
        .date>svg{
            cursor: pointer;
        }
        #actualDate{
            padding: 4px 0;
        }
        .btnAbsences{
            position: relative;
            text-align: left;
            border-radius: 10px;
            box-shadow: 0 2px 2px 2px #ddd;
            border: 1px solid transparent;
            background: #FFF;
            padding: 10px 20px;
            margin: 10px 42px;
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
            gap:1px;
            transform: translateY(8px);
        }
        .hint>div{
            outline: 1px solid #AAA;
            height: 8px;
            background: #FFF;
            cursor: initial;
        }
        .hint>.now{
            outline: none;
            background: transparent;
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
    <?php 
        $h1 = 'Absences';
        include "$path/html/assets/header.php";
    ?>
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

            <select id=semestre onchange="clearStorage(['matiere']);selectSemester(this.value)" disabled>
                <option value="" disabled selected hidden>Choisir un semestre</option>
            </select>

            <select id=matiere onchange="selectMatiere(this.value)" disabled>
                <option value="" disabled selected hidden>Choisir une matière</option>
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
        document.querySelector("#absences").classList.add("navActif");
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

                document.querySelector("body").classList.add('personnel');
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
        var UE = "";
        var matiereComplet = "";
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

            getStudentsListes();
            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('semestre', semestre);

            let matiere = localStorage.getItem("matiere");
            if(matiere){
                document.querySelector("#matiere").value = matiere;
                selectMatiere(matiere);
            }
		}
        
        async function selectMatiere(mat){
            matiere = mat;
            let obj = document.querySelector('#matiere [value="'+matiere+'"]');
            matiereComplet = obj.innerText;
            UE = obj.parentElement.label;

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

        let hintHours = `<div class=hint onclick="messageHint(event)">${
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

        function messageHint(event){
            event.stopPropagation();
            message("Ceci est une zone d'information pour indiquer si l'étudiant a été absent à d'autres créneaux.");
        }

        function actualDate(){
            let jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
            return `${jours[date.getDay()]} ${date.toLocaleDateString()} <br> ${creneaux[creneauxIndex][0]}h / ${creneaux[creneauxIndex][1]}h`;
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
                if(
                    (absencesEtudiant[date]?.[creneaux[creneauxIndex]]?.enseignant || session) != session
                    && statutSession < ADMINISTRATEUR
                ){
                    return message("Vous ne pouvez changer l'absence d'un autre enseignant : <span class=capitalize>" + absencesEtudiant[date][creneaux[creneauxIndex]].enseignant.split("@")[0].replace(/[.]/g, " ") + "</span>");
                }
            }
        // Il faut choisir une matière pour ajouter une absence
            if(matiere == ""){
                return message("Vous devez dabord choisir une matière pour ajouter une absence.");
            }

        // On ne peut pas toucher à une absence justifiée
            if(obj.classList.contains("excuse")){
                return message("Vous ne pouvez pas supprimer une absence qui est justifiée.");
            }
        
        // Toggle de l'absence
            if(obj.classList.toggle("absent")){
                var statut = "absent";

                // Ajouter l'absence aux données stockée côté client
                if(!dataEtudiants.absences[obj.dataset.email]){
                    dataEtudiants.absences[obj.dataset.email] = {};
                }
                if(!dataEtudiants.absences[obj.dataset.email][ISODate()]){
                    dataEtudiants.absences[obj.dataset.email][ISODate()] = {};
                }
                dataEtudiants.absences[obj.dataset.email][ISODate()][creneaux[creneauxIndex]] =
                    {
                        "enseignant": session,
                        "creneauxIndex": creneauxIndex,
                        "matiere": matiere,
                        "matiereComplet": matiereComplet,
                        "UE": UE,
                        "statut": statut
                    };
                
            } else {
                var statut = "présent";
                obj.classList.remove("excuse");
                // Supprimer l'absence des données
                delete dataEtudiants.absences[obj.dataset.email][ISODate()][creneaux[creneauxIndex]];
            }

        // Sauvegarde de l'absence sur le serveur
            let response = await fetchData("setAbsence" + 
                "&dep=" + departement +
                "&semestre=" + semestre +
                "&matiere=" + matiere +
                "&matiereComplet=" + matiereComplet +
                "&UE=" + UE +
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
            document.querySelectorAll(".absent").forEach(e=>e.classList.remove("absent", "excuse"));

            var date = ISODate();

            Object.entries(dataEtudiants.absences).forEach(([etudiant, listeAbsences])=>{
                Object.entries(listeAbsences[date] || {}).forEach(([creneauNom, dataAbsence])=>{
                    let ligne = document.querySelector(`[data-email="${etudiant}"]`);
                    if(dataAbsence.creneauxIndex == creneauxIndex){
                        ligne.classList.add(...dataAbsence.statut.split(" ")); // Changement de couleur de la ligne
                    } else {
                        ligne.children[1].children[dataAbsence.creneauxIndex].classList.add(...dataAbsence.statut.split(" ")); // Changement de couleur des barres pour la journée
                    }
                })
            })

            document.querySelectorAll(".hint").forEach(e=>{
                e.children[creneauxIndex].classList.add("now");
            })
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