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
		/**************/
		/* Zone dépôt */
		/**************/
		.newJustif {
			background: #FFF;
			border: 1px solid #CCC;
			padding: 8px 32px;
			display: grid;
			grid-template-columns: auto auto 1fr;
			gap: 8px;
			margin-bottom: 16px;
		}
		.newJustif {
			grid-column: span 2;
		}
		.newJustif>label {
			display: grid;
			grid-column: 1 / 3;
			grid-template-columns: subgrid;
		}

		.newJustif>div {
			grid-column: 3 / 4;
			grid-row: 2 / span 2;
		}
		.dropZone {
			background: #FFF;
			border-radius: 8px;
			border: 2px dashed #09C;
			padding: 4px;
			transition: 0.2s;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			gap: 8px;
		}

		.fileOver {
			transform: scale(0.9);
		}

		.newJustif>input[type=submit] {
			grid-column: -1 / -2;
			justify-self: end;
			background: #90c;
			color: #FFF;
			border: none;
			padding: 8px 32px;
			border-radius: 4px;
			cursor: pointer;
			box-shadow: 0 2px 2px #aaa;
			transition: 0.2s;
		}
		.newJustif>input[type=submit]:hover {
			box-shadow: 0 2px 2px 1px #444;
		}
		.newJustif>input[type=submit]:active {
			box-shadow: 0 0px 0px 0px #444;
			transform: translateY(2px)
		}

		/*********/
		/* Liste */
		/*********/
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
   
		<form class="newJustif">
			<b>Saisi d'un nouveau justificatif</b>
			<label>Date de début <input required type="datetime-local" name="date_debut"></label>
			<label>Date de Fin <input required type="datetime-local" name="date_fin"></label>
			<div class=dropZone>
				Déposez une image ou un fichier PDF
				<label>
					<input required type=file accept="application/pdf, image/*" name=file>
				</label>
			</div>
			<input type="submit">
		</form>

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
/******************/
/* Import fichier */
/******************/
		document.querySelector(".dropZone").addEventListener("drop", dropFile);
		document.querySelector(".dropZone").addEventListener("dragover", dragOver);
		document.querySelector(".dropZone").addEventListener("dragleave", dragLeave);
		
		document.querySelector(".newJustif").addEventListener("submit", sendJustif);

		function dropFile(event) {
			event.preventDefault();
			this.classList.remove("fileOver");

			if (event.target.files?.[0] || event.dataTransfer.items[0].type.match(/pdf|image/)) {
				if(event.dataTransfer.items.length > 1) {
					message("Un seul fichier s'il vous plait.");
					return;
				}
				const droppedFile = event.dataTransfer.files[0];
				document.querySelector(".dropZone input").files = event.dataTransfer.files;
			} else {
				message("Type de fichier non valide");
			}
		}

		function dragOver(event) {
			event.preventDefault();
			this.classList.add("fileOver")
		}
		function dragLeave() {
			this.classList.remove("fileOver")
		}

		function message(msg){
            var div = document.createElement("div");
            div.className = "message";
            div.innerHTML = msg;
            document.querySelector("body").appendChild(div);
            setTimeout(()=>{
                div.remove();
            }, 6000);
        }

		function sendJustif(event) {
			event.preventDefault();

			let date_debut = new Date(document.querySelector("[name=date_debut]").value);
			let date_fin = new Date(document.querySelector("[name=date_fin]").value);

			if(date_debut > date_fin) {
				message("La date de fin doit être après la date de début.");
				return;
			}

			const form = new FormData(this);
			//formData.append("avatar", fileField.files[0]);
			fetch("../services/data.php?q=sendJustif", {
				method: "POST",
				body: form
			}).then(r => r.json())
			.then(JSON => {
				console.log(JSON);
			});
		}

    </script>
    <?php 
        include "$path/config/analytics.php";
    ?>
</body>
</html>
