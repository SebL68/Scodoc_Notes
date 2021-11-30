<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Relevé de notes</title>
	<style>
		body{
			font-family: arial;
		}
		h2{
			margin-bottom: 0;
		}
	</style>
</head>
<body spellcheck=true contenteditable=true>
	<main>
		<div id=infoEtudiant></div>

		<section>
			<h2>Semestre</h2>
			<em>La moyenne sert à situer l'étudiant dans la promotion et ne reflètent pas l'obtention du semestre.</em>
			
		</section>

		<section>
			<h2>Évaluations</h2>

		</section>

		<section>
			<h2>Synthèse</h2>
			
		</section>

	</main>
	<script>
		let data = <?php include('releveNotes.json'); ?>;

		/*******************************/
		/* Informations sur l'étudiant */
		/*******************************/
		
		document.querySelector("#infoEtudiant").innerHTML = `
			${data.etudiant.civilite}
			${data.etudiant.nom}
			${data.etudiant.prenom}
			né${(data.etudiant.civilite == "F") ? "e" : ""} le 
			${data.etudiant.dateNaissance}<br>
			Numéro étudiant : ${data.etudiant.code_nip}
		`;

	</script>
</body>
</html>