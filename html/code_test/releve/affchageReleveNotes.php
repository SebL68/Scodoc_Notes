<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Relevé de notes d'un semestre">
	<title>Relevé de notes</title>
	<link rel="stylesheet" href="releve.css">
</head>
<body spellcheck=true contenteditable=//true>
	<div class="wait"></div>
	<main class="releve">
<!--------------------------->
<!-- Info. étudiant        -->
<!--------------------------->
		<section class=etudiant>
			<img class=studentPic src="" alt="Photo de l'étudiant" width=100 height=120>
			<div class=infoEtudiant></div>
		</section>

<!--------------------------->
<!-- Semestre              -->
<!--------------------------->
		<section>
			<h2>Semestre </h2>
			<div class=dateInscription>Inscrit le </div>
			<em>Les moyennes servent à situer l'étudiant dans la promotion et ne correspondent pas à des validations de compétences ou d'UE.</em>
			<div class=infoSemestre></div>
		</section>

<!--------------------------->
<!-- Synthèse              -->
<!--------------------------->
		<section>
			<div>
				<div>
					<h2>Synthèse</h2>
					<em>La moyenne des ressources dans une UE dépend des poids donnés aux évaluations.</em>
				</div>
				<div class=CTA_Liste>
					Liste <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"/></svg>
				</div>
			</div>
			<div class=synthese></div>
		</section>

<!--------------------------->
<!-- Evaluations           -->
<!--------------------------->
		<section>
			<div>
				<h2>Évaluations</h2>
				<div class=CTA_Liste>
					Liste <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"/></svg>
				</div>
			</div>
			<div class=evaluations></div>
		</section>

		<section>
			<div>
				<h2>SAÉ</h2>
				<div class=CTA_Liste>
					Liste <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"/></svg>
				</div>
			</div>
			<div class=sae></div>
		</section>

	</main>

	<script>
		let dataSrc = "formsemestre_bulletinetud-sans-classements-coef-txt.json";
	</script>
	<script src=releve.js></script>
</body>
</html>