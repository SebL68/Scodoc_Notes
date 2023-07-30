<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<style>
		body{
			background : #fafafa;
			font-family: arial;
		}
		h1{
			margin-left: 16px;
		}
		details{
			margin: 16px;
		}
		summary{
			color: #FFF;
			background: #09c;
			border-radius: 8px;
			padding: 8px 32px;
			font-size: 20px;
		}

	</style>
</head>
<body>
	<h1>Aides et explications</h1>
	<details id=absencesMultiJours>
		<summary>Attention, une absence sur plusieurs jours a été intégrée dans Scodoc, la passerelle ne le gère pas.</summary>

		<p>Même si l'affichage aux étudiants fonctionne, la passerelle ne gère pas la saisi, la justification et les statistiques des absences ajoutées directement dans Scodoc qui durent plusieurs jours.</p>
		<p>La raison est que les statistiques de la passerelle peuvent servir à générer des malus, il faut donc "certifier" chaque séance où l'étudiant est absent et ne pas saisir une plage indifféremment des séances présentes ou non.</p>
	</details>

	<script>
		document.querySelector(location.hash || "body").open = true;
	</script>
</body>
</html>