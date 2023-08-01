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
		<summary>Gestion des absences et des justifications entre la passerelle et Scodoc</summary>

		<p>Même si l'affichage aux étudiants fonctionne, la passerelle ne gère pas la saisi, la justification et les statistiques des absences sur plusieurs jours et de la même manière que Scodoc.</p>

		<p>Scodoc gère les absences d'un côté et les justificatifs d'un autre, avec des périodes qui peuvent être différentes les unes des autres. Par exemple un justificatif peut durer une semaine et justifie toutes les absences qui sont dans la période.</p>

		<p>La passerelle gère les absences individuellement. Chaque absence peut alors être justifiée ou non.</p>

		<p>Dans certains cas, il est possible qu'enlever une justification sur une absence depuis la passerelle impacte d'autres absences non souhaitées. La passerelle détecte ces cas et invite alors à utiliser Scodoc.</p>

		<p>Attention aux statisques : la passerelle est prévue pour comptabiliser et "certifier" chaque heure d'absence. Ces statistiques peuvent servir à générer par exemple des malus. Pour ces raisons, la passerelle n'accepte pas d'absences sur plusieurs jours.</p>

	</details>

	<script>
		document.querySelector(location.hash || "body").open = true;
	</script>
</body>
</html>