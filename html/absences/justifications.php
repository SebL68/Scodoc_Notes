<?php 
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	require_once "$path/includes/default_config.php";
	require_once "$path/includes/analytics.class.php";
	Analytics::add('absences');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absences</title>
    <style>
        <?php include $_SERVER['DOCUMENT_ROOT']."/assets/styles/global.css"?>
		.listeJustif {
			display: grid;
			grid-template-columns: 1fr 1fr 1fr 1fr;
			padding: 1px;
			background: #CCC;
			gap: 1px;
			color: #000;
		}
		.listeJustif>.firstLine {
			background: #09C;
			color: #FFF;
		}
		.listeJustif>div {
			background: #FFF;
			padding: 4px 16px;
		}
		[data-statut=VALIDE],
		[data-statut=VALIDE]:before {
			content: "Valide";
			background: #00be82 !important;
		}
		[data-statut=NON_VALIDE],
		[data-statut=NON_VALIDE]:before {
			content: "Non valide";
			background: #ec7068 !important;
		}
		[data-statut=ATTENTE],
		[data-statut=ATTENTE]:before {
			content: "En attente de validation";
			background: #f3a027 !important;
		}
    </style>
    <meta name=description content="Justification des absences">
</head>
<body>
    <?php 
        $h1 = 'Absences';
        include $_SERVER['DOCUMENT_ROOT']."/assets/header.php";
    ?>
    <main>
        <p>
            Bonjour <span class=nom></span>. 
        </p>
		<p>
			Cette interface permet de visualiser et d'ajouter des justificatifs d'absences. Après un ajout, il faut que le justificatif soit validé par la personne référente du département.
		</p>
		<p>
			Les justificatifs doivent être valides et déposés dans les délais, selon les règles de l'IUT. 
		</p>
   

		<div class="newJustif">

		</div>

        <div class=listeJustif></div>
        <div class=wait></div>
        
    </main>

    <div class=auth>
        <!-- Site en maintenance -->
        Authentification en cours ...
    </div>
	
	<script>
		/**************************/
		/* Service Worker pour le message "Installer l'application" et pour le fonctionnement hors ligne PWA
		/**************************/		
		if('serviceWorker' in navigator){
			navigator.serviceWorker.register('../sw.js');
		}
	</script>
	<script src="../assets/js/theme.js"></script>
    <script>
		<?php
            include "$path/includes/clientIO.php";
		?>  
/*********************************************/
/* Vérifie l'identité de la personne et son statut
/*********************************************/		
        async function checkStatut() {
            let data = await fetchData("donnéesAuthentification");
            let auth = document.querySelector(".auth");
            auth.style.opacity = "0";
            auth.style.pointerEvents = "none";

			if(config.data_absences_scodoc == false) {
				displayError("Cette page ne peut être activée que si les absences sont gérées par Scodoc.");
				return;
			}
			if(config.autoriser_justificatifs == false) {
				displayError("Le dépot des justificatifs n'est pas autorisé.");
				return;
			}

            if(data.statut == ETUDIANT){
				getJustifs();
			} else {
				displayError("Cette page est réservée aux étudiants pour justifier leurs absences.");
			}
        }

		async function getJustifs() {
			let data = await fetchData("getJustifs");
			console.log(data);
			let output = `
				<div class=firstLine>Saisi le</div>
				<div class=firstLine>Début</div>
				<div class=firstLine>Fin</div>
				<div class=firstLine>Statut</div>`;
			
			data.forEach(justification => {
				output += newJustifLine(justification);
			})

			document.querySelector(".listeJustif").innerHTML = output;
		}

		function newJustifLine(justif) {
			return `
				<div>${ISODateToDisplay(justif.entry_date)}</div>
				<div>${ISODateToDisplay(justif.date_debut)}</div>
				<div>${ISODateToDisplay(justif.date_fin)}</div>
				<div data-statut="${justif.etat}"></div>`;
		}

		function ISODateToDisplay(date) {
			let d = new Date(date);
			return d.toLocaleDateString() + " - " + d.toLocaleTimeString();
		}

        checkStatut();
    </script>
    <?php 
        include "$path/config/analytics.php";
    ?>
</body>
</html>
