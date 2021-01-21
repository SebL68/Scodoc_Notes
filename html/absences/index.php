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

			<select id="departement" onchange="selectDepartment(this.value)">
				<option value="" disabled selected hidden>Choisir un département</option>
				<option value="MMI">MMI</option>
				<option value="GEII">GEII</option>
				<option value="GEA">GEA</option>
				<option value="SGM">SGM</option>
				<option value="GLT">GLT</option>
				<option value="GMP">GMP</option>
			</select>

			<select id="semestre" onchange="selectSemester(this.value)"></select>

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
                
            } else if(data.statut == ETUDIANT) {
				// Faire le mode étudiant ici
			} else {
				document.querySelector(".contenu").innerHTML = "Ce contenu est uniquement accessible pour l'IUT. ";
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
		}
		
		async function selectSemester(semestre){
			
            let data = await fetchData("test&dep=MMI");
			
			console.log(data);
		}
		

    </script>
    <?php 
        include "$path/includes/analytics.php";
    ?>
</body>
</html>