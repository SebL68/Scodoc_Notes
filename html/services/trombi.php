<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Trombinoscope</title>
	<style>

		body{
			font-family: arial;
			background: #fafafa;
			text-align: center;
		}
		.groupes{
			display: flex;
			justify-content: center;
			gap: 4px;
			margin-bottom: 8px;
		}
		.groupes>div{
			padding: 4px 8px;
			border-radius: 4px;
			border: 1px solid #bbb;
		}
		.trombi>a{
			border: 1px solid #bbb;
			margin: 4px;
			width: 240px;
			display: inline-block;
			vertical-align: top;
			text-decoration: none;
			color: #000;
			position: relative;
		}
		.trombi>a>div{
			padding: 4px;
		}
		img{
			width: 100%;
		}
		.admin .suppr{
			position: absolute;
			width: 20px;
			height: 20px;
			top: -10px;
			right: -10px;
			background: #C09;
			border-radius: 100%;
			display: grid;
			align-items: center;
			justify-content: center;
			padding: 0;
		}
		.admin .suppr:before,
		.admin .suppr:after{
			content: "";
			height: 2px;
			width: 16px;
			background: #fff;
			border-radius: 1px;
			grid-row: 1 / 2;
			grid-column: 1 / 2;
			transform: rotate(45deg);
		}
		.admin .suppr:after{
			transform: rotate(-45deg);
		}
		@media print{
			.admin .suppr,
			.nom{
				display: none;
			}
		}
		
	</style>
</head>
<body>
	<div class="wait"></div>
	<div class="auth"></div>
	<div class="nom"></div>
	<div class="contenu"></div>
	<h1></h1>
	<main>
		<div class="groupes"></div>
		<div class="trombi"></div>
	</main>

	<script>

		checkStatut();
		<?php
		 	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
			include "$path/includes/clientIO.php";
		?>
		/*********************************************/
		/* Vérifie l'identité de la personne et son statut
		/*********************************************/			
        async function checkStatut(){
            let data = await fetchData("donnéesAuthentification");

            if(data.statut >= PERSONNEL){
            	go();
            } else {
                document.querySelector(".contenu").innerHTML = "Ce contenu est uniquement accessible pour les personnels de l'IUT. ";
            }
        }

		/*************************/

		function go() {
			let data = JSON.parse(localStorage.getItem("trombi"));
			document.querySelector("h1").innerText = data.titre;

			let output = "";
			data.groupes.forEach(groupe=>{
				output += `<div>${groupe}</div>`;
			})
			document.querySelector(".groupes").innerHTML = output;

			output = "";
			data.etudiants.forEach(etudiant=>{
				output += `<a data-nip=${etudiant.nip} href="../?ask_student=${etudiant.nip}">
					<img src="data.php?q=getStudentPic&nip=${etudiant.nip}">
					<div>${etudiant.prenom}<br><b>${etudiant.nom}</b><br>${etudiant.groupe}</div>
					<div class=suppr></div>
				</a>
				`;
			})
			document.querySelector(".trombi").innerHTML = output;


			document.querySelectorAll(".suppr").forEach(e=>{
				e.addEventListener("click", suppr);
			})
		}
		

		async function suppr(event) {
			event.preventDefault();
			event.stopPropagation();

			let nip = this.closest("a").dataset.nip;

			let reponse = await fetchData("deletePic&nip=" + nip);

			if(reponse.result == "OK") {
				this.closest("a").querySelector("img").src = `data.php?q=getStudentPic&nip=${nip}&t=${Date.now()}`;
			}
		}
	</script>
</body>
</html>
