<?php 
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
?>
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
        .contenu{
            opacity: 0.5;
            pointer-events: none;
        }
        .ready{
            opacity: initial;
            pointer-events: initial;
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
        checkStatut();
		<?php
			include "$path/includes/clientIO.js";
		?>
/*********************************************/
/* Vérifie l'identité de la personne et son statut
/*********************************************/			
        async function checkStatut(){
            let data = await fetchData("donnéesAuthentification");
            document.querySelector(".prenom").innerText = data.session.split(".")[0];
            let auth = document.querySelector(".auth");
            auth.style.opacity = "0";
            auth.style.pointerEvents = "none";

            if(data.statut >= PERSONNEL){

                /* Gestion du storage remettre le même état au retour */
                let departement = localStorage.getItem("departement")
                if(departement){
                    document.querySelector("#departement").value = localStorage.getItem("departement");
                    selectDepartment(departement);
                }

			} else {
				document.querySelector(".contenu").innerHTML = "Ce contenu est uniquement accessible pour des personnels de l'IUT. ";
			}
        }
/*********************************************/
/* Récupère et traite les listes d'étudiants du département
/*********************************************/		
        async function selectDepartment(departement){
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

            let semestre = localStorage.getItem("semestre")
            if(semestre){
                document.querySelector("#semestre").value = localStorage.getItem("semestre");
                selectSemester(semestre);
            }
		}
		
		async function selectSemester(semestre){
			let departement = document.querySelector("#departement").value;
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

            let matiere = localStorage.getItem("matiere")
            if(matiere){
                document.querySelector("#matiere").value = localStorage.getItem("matiere");
                selectMatiere(matiere);
            }

            getStudentsListes();
		}
        
        async function selectMatiere(matiere){
            document.querySelector(".contenu").classList.add("ready");
            document.querySelector("#matiere").classList.remove("highlight");
            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('matiere', matiere);
        }

        async function getStudentsListes(){
            let departement = document.querySelector("#departement").value;
            let semestre = document.querySelector("#semestre").value;
            let etudiants = await fetchData(`listeEtudiantsSemestre&dep=${departement}&semestre=${semestre}`);
            document.querySelector(".contenu").innerHTML = createSemester(etudiants);
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
                    var num = groupe?.replace(/ /g, "") || "Groupe1";
                    groupes += `<div class=groupe onclick="hideGroupe(this, '${num}')">${groupe || "Groupe 1"}</div>`;
                })
            }else{
                groupes = `<div class=groupe onclick="hideGroupe(this, 'Groupe1')">Groupe 1</div>`;
            }
            output += `
                <div class="flex">
                    <div>
                        <div class="groupes">${groupes}</div>
                        <div class="etudiants">${createStudents(liste.etudiants)}</div>
                    </div>
                    
                </div>
            `;

            return output;
        }

        function createStudents(etudiants){
			var output = "";
           
			etudiants.forEach(etudiant=>{
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

		function hideGroupe(obj, num){
			obj.classList.toggle("selected");
			obj.parentElement.nextElementSibling.querySelectorAll('.'+num).forEach(e=>{
				e.classList.toggle("hide");
			})
        }
    </script>
    <?php 
        include "$path/includes/analytics.php";
    ?>
</body>
</html>