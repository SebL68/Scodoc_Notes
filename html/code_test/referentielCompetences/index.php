<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Référentiel de compétences</title>
	<style>
		body{
			margin: 0;
		}
	</style>
</head>
<body>
    
	<ref-competences></ref-competences>

	<script src="ref_competences.js"></script>
	<script>
		let data = <?php include "mmi.json"; ?>
		document.querySelector("ref-competences").setData = data;
	</script>
</body>
</html>