<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Trombinoscope</title>
	<style>
		body {
			font-family: arial;
			background: var(--fond);
			text-align: center;
		}

		.groupes {
			display: flex;
			justify-content: center;
			gap: 4px;
			margin-bottom: 8px;
		}

		.groupes>div {
			padding: 4px 8px;
			border-radius: 4px;
			border: 1px solid var(--gris);
		}

		.trombi>a {
			border: 1px solid var(--gris);
			margin: 4px;
			width: 240px;
			display: inline-block;
			vertical-align: top;
			text-decoration: none;
			color: var(--contenu);
		}

		.trombi>a>div {
			padding: 4px;
		}

		img {
			width: 100%;
		}

		@media print {
			.trombi>div {
				width: 110px;
				font-size: 12px;
			}
		}
	</style>
</head>

<body>
	<h1></h1>
	<main>
		<div class="groupes"></div>
		<div class="trombi"></div>
	</main>

	<script>
		let data = JSON.parse(localStorage.getItem("trombi"));
		document.querySelector("h1").innerText = data.titre;

		let output = "";
		data.groupes.forEach(groupe => {
			output += `<div>${groupe}</div>`;
		})
		document.querySelector(".groupes").innerHTML = output;

		output = "";
		data.etudiants.forEach(etudiant => {
			output += `<a href="../?ask_student=${etudiant.nip}">
				<img src="data.php?q=getStudentPic&nip=${etudiant.nip}">
				<div>${etudiant.prenom}<br><b>${etudiant.nom}</b><br>${etudiant.groupe}</div>
			</a>
			`;
		})
		document.querySelector(".trombi").innerHTML = output;


	</script>
</body>

</html>