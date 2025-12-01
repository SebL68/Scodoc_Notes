<?php 
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
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
			background: var(--fond-clair);
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

		.dropZone, .noticeMenstruel {
			border-radius: 8px;
			border: 2px dashed #09C;
			padding: 4px;
			transition: 0.2s;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			gap: 8px;
			grid-column: 3 / 4;
			grid-row: 2 / span 2;
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
		.newJustif>input[type=submit]:disabled {
			cursor: initial;
			opacity: 0.4;
		}
		.newJustif>input[type=submit]:not(:disabled):hover {
			box-shadow: 0 2px 2px 1px #444;
		}
		.newJustif>input[type=submit]:not(:disabled):active {
			box-shadow: 0 0px 0px 0px #444;
			transform: translateY(2px)
		}

		.newJustif>.labelMenstruel {
			border: 1px solid #777;
			text-align: center;
			align-items: center;
			grid-column: 3 / 4;
    		grid-row: 1 / 2;
			cursor: pointer;
			display: flex;
			justify-content: center;
			gap: 8px;
		}
		.newJustif>.labelMenstruel:hover {
			background: #aaa;
		}
		.newJustif>.labelMenstruel:has(input:checked){
			border: 2px solid var(--secondaire);
		}
		.newJustif:not(.menstruelActive)>.noticeMenstruel,
		.newJustif.menstruelActive>.dropZone{
			display: none;
		}

		.newJustif.menstruelActive input[type=submit]{
			opacity: 0.5;
			pointer-events: none;
		}

		.tropMenstruel {
			pointer-events: none;
			opacity: 0.4;
		}

		@media screen and (max-width:700px) {
			.dropZone, .noticeMenstruel {
				grid-column: 1 / 3;
				grid-row: 4;
			}
			.newJustif>input[type=submit] {
				grid-column: 2 / 3;
				grid-row: 5;
			}
			.newJustif>.labelMenstruel{
				grid-column: 2 / 3;
			}
		}

		/*********/
		/* Liste */
		/*********/
		.listeJustif {
			display: grid;
			grid-template-columns: repeat(5, auto);
			padding: 1px;
			background: #CCC;
			gap: 1px;
			color: #000;
			max-width: calc(100vw - 20px);
    		overflow: auto;
		}
		.listeJustif>.firstLine {
			background: #09C;
			color: #FFF;
		}
		.listeJustif>div {
			color: var(--text);
			background: var(--fond-clair);
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
        $h1 = 'Justificatifs';
        include $_SERVER['DOCUMENT_ROOT']."/assets/header.php";
    ?>
    <main>
        <p>
            Bonjour <span class=nom></span>. 
        </p>
		<p>
			Cette interface permet de visualiser et d'ajouter des justificatifs d'absences. Après un ajout, il faut que le justificatif soit validé par une personne référente du département.
		</p>
		<p>
			Les justificatifs doivent être valides et déposés dans les délais, selon les règles de l'IUT. 
		</p>
		<p class="messageJustif"></p>
   
		<form class="newJustif">
			<b>Saisi d'un nouveau justificatif</b>
			<label>Date de début <input required type="datetime-local" name="date_debut"></label>
			<label>Date de Fin <input required type="datetime-local" name="date_fin"></label>
			
			<label class=labelMenstruel><input type=checkbox name=menstruel value='on'>Demande de congé menstruel<span></span></label>
			<div class=dropZone>
				Déposez une image ou un fichier PDF - 8Mo max
				<label>
					<input required type=file accept="application/pdf, image/jpeg, image/png, image/avif, image/webp" size="8000000" name=file>
				</label>
			</div>
			<div class=noticeMenstruel>
				<zone-signature></zone-signature>
			</div>
			<input type="submit">
		</form>

        <div class=listeJustif></div>
        
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

			document.querySelector(".messageJustif").innerHTML = config.message_justificatifs;

            if(data.statut == ETUDIANT){
				getJustifs();
			} else {
				//displayError("Cette page est réservée aux étudiants pour justifier leurs absences.");
				let params = new URLSearchParams(window.location.search);
				getJustifs(params.get("nip"));
				document.querySelector(".newJustif>input[type=submit]").disabled = true;
				document.querySelector(".newJustif>input[type=submit]").value = "Envoi reservé aux étudiants";
			}

			if(config.nb_conge_menstruel == 0){
				document.querySelector(".labelMenstruel").remove();
			} else {
				document.querySelector(".labelMenstruel>input").addEventListener("change", toggleMenstruel)
			}
        }

		function toggleMenstruel() {
			document.querySelector(".newJustif").classList.toggle("menstruelActive");
			/*let fileInput = document.querySelector(".dropZone input");
			let signature = document.querySelector(".signature");
			if(fileInput.disabled) {
				fileInput.disabled = false;
				signature.disabled = true;
			} else {
				fileInput.disabled = true;
				signature.disabled = false;
			}*/
		}

		function calculMenstruel(data){
			const MATIN = [0, 13];
			const APRESMIDI = [13, 24];
			let nb = 0;

			/* Ajout des intervals menstruels */
			let intervals = [];
			data.forEach(justif => {
				if(justif.raison == "Congé menstruel" && justif.etat != "NON_VALIDE") {
					//nb += (new Date(justif.date_fin) - new Date(justif.date_debut)) / 3600000;
					intervals.push([new Date(justif.date_debut), new Date(justif.date_fin)]);
				}
			})

			/* Tri */
			intervals.sort((a, b)=> a[0] - b[0]);

			/* Fusion des intervalles */
			const merged = [];
			for (const [start, end] of intervals) {
				if (!merged.length || start > merged[merged.length - 1][1]) {
				merged.push([start, end]);
				} else {
				merged[merged.length - 1][1] = new Date(
					Math.max(merged[merged.length - 1][1], end)
				);
				}
			}

			// Fonction intersection demi-journée
			const intersects = (dayStart, dayEnd, startHour, endHour) => {
				const rStart = new Date(dayStart);
				rStart.setHours(startHour, 0, 0, 0);

				const rEnd = new Date(dayStart);
				rEnd.setHours(endHour, 0, 0, 0);

				return dayEnd > rStart && dayStart < rEnd;
			};

			// Découper intervalles fusionnés jour par jour ---
			for (const [start, end] of merged) {
				let current = new Date(start);

				while (current < end) {
				const dayStart = new Date(current);
				dayStart.setHours(0, 0, 0, 0);

				const dayEnd = new Date(dayStart);
				dayEnd.setHours(23, 59, 59, 999);

				const slotStart = new Date(Math.max(start, dayStart));
				const slotEnd = new Date(Math.min(end, dayEnd));

				if (intersects(slotStart, slotEnd, ...MATIN)) nb++;
				if (intersects(slotStart, slotEnd, ...APRESMIDI)) nb++;

				// Passer au jour suivant
				current = new Date(dayStart);
				current.setDate(current.getDate() + 1);
				}
			}

			/* Affichage */
			document.querySelector(".labelMenstruel span").innerText = `${nb} / ${config.nb_conge_menstruel} demi-journées`;

			if(nb >= config.nb_conge_menstruel) {
				document.querySelector(".labelMenstruel").classList.add("tropMenstruel");
			}
		}

		async function getJustifs($nip = "") {
			let data = await fetchData("getJustifs&nip="+$nip);
			let output = `
				<div class=firstLine>Saisi le</div>
				<div class=firstLine>Début</div>
				<div class=firstLine>Fin</div>
				<div class=firstLine>Statut</div>
				<div class=firstLine>Raison</div>`;
			
			data.reverse().forEach(justification => {
				output += newJustifLine(justification);
			})

			document.querySelector(".listeJustif").innerHTML = output;
			if(config.nb_conge_menstruel != 0){
				calculMenstruel(data);
			}
		}

		function newJustifLine(justif) {
			return `
				<div>${ISODateToDisplay(justif.entry_date)}</div>
				<div>${ISODateToDisplay(justif.date_debut)}</div>
				<div>${ISODateToDisplay(justif.date_fin)}</div>
				<div data-statut="${justif.etat}"></div>
				<div>${justif.raison || ""}</div>`;
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

			if (event.target.files?.[0] || event.dataTransfer.items[0].type.match(/pdf|jpeg|png|avif|webp/)) {
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
			if(Array.from(form)[2][1].size > 8000000) {
				message("Fichier trop grand");
				return;
			}
			document.querySelector(".wait").style.display = "flex";
			document.querySelector("input[type=submit]").disabled = true;
			document.querySelector("input[type=submit]").value = "Envoi en cours...";

			fetch("../services/data.php?q=sendJustif", {
				method: "POST",
				body: form
			}).then(r => r.json())
			.then(JSON => {
				if(JSON.result == "OK") {
					let date = new Date();
					var raison = document.querySelector(".menstruelActive") ? "Congé menstruel" : "";
					let justif = {
						entry_date: date.toISOString(),
						date_debut: date_debut,
						date_fin: date_fin,
						etat: "ATTENTE",
						raison: raison
					};
					document.querySelector(".firstLine:nth-child(5)").insertAdjacentHTML("afterend" ,newJustifLine(justif));
				} else {
					displayError("Un problème est survenu.")
				}
				document.querySelector(".wait").style.display = "none";
				document.querySelector("form").reset();
				document.querySelector("input[type=submit]").disabled = false;
				document.querySelector("input[type=submit]").value = "Envoyer";

				if(raison) {
					toggleMenstruel();
				}
			})
			.catch(error => displayError("Un problème est survenu : " + error));
		}

    </script>

	<script>

		class signature extends HTMLElement {
			constructor(){
				super();
				const shadow = this.attachShadow({ mode: "open" });
				shadow.innerHTML = `
					<style>
						:host {
							position: relative;
						}
						canvas{
							border: 1px solid #777;
							border-radius: 8px;
						}
						.corbeille {
							position: absolute;
							top: 2px;
							left: 278px;
							cursor: pointer;
						}
					</style>
				`;

				// Corbeille
				let corbeille = document.createElement("div");
				corbeille.className = "corbeille";
				corbeille.innerText = "🗑️";
				corbeille.addEventListener("click", ()=>{this.clear()});
				shadow.appendChild(corbeille);

				// Canvas
				this.c = document.createElement("canvas");
				this.c.width = "300";
				this.c.height = "150";
				shadow.appendChild(this.c);
				this.ctx = this.c.getContext("2d");

				this.ctx.fillStyle = "#FFFFFF";
				this.ctx.fillRect(0, 0, this.c.width, this.c.height);

				this.ctx.fillStyle = "#000000";
				this.ctx.font = "10px Arial";
				this.ctx.fillText("J'atteste sur l'honneur que le congé menstruel est saisi pour", 4, 10);
				this.ctx.fillText("motif de dysménhorrées.", 4, 24);
				this.ctx.fillText("Ne fonctionne pas pour les examens annoncés.", 4, 142);

				this.ctx.font = "40px Arial";
				this.ctx.fillStyle = "#eeeeee";
				this.ctx.fillText("Signature", 60, 85);

				this.ctx.strokeStyle = "#000080";
				this.ctx.lineWidth = 3;

				this.startHandler = (event)=>{this.start(event)}
				this.moveHandler = (event)=>{this.move(event)}
				this.endHandler = (event)=>{this.end(event)}

				this.c.addEventListener("mousedown", this.startHandler);
				this.c.addEventListener("touchstart", this.startHandler);
			}

			start(event) {
				event.preventDefault();

				this.x = (event.clientX || event.changedTouches[0].clientX) - this.c.getBoundingClientRect().x;
				this.y = (event.clientY || event.changedTouches[0].clientY) - this.c.getBoundingClientRect().y;

				this.ctx.moveTo(this.x, this.y);

				this.c.addEventListener("mousemove", this.moveHandler);
				this.c.addEventListener("touchmove", this.moveHandler);

				document.addEventListener("mouseup", this.endHandler);
				document.addEventListener("touchend", this.endHandler);
			}

			move(event) {
				event.preventDefault();

				let x = (event.clientX || event.changedTouches[0].clientX) - this.c.getBoundingClientRect().x;
				let y = (event.clientY || event.changedTouches[0].clientY) - this.c.getBoundingClientRect().y;

				let distance = Math.sqrt(
					(this.x - x) * (this.x - x) + 
					(this.y - y) * (this.y - y)
				);
				let largeur = 0.5 + 2 / distance;
				this.ctx.lineWidth = largeur;

				this.ctx.beginPath();
				this.ctx.moveTo(this.x, this.y);
				this.ctx.lineTo(x, y);
				this.ctx.stroke();

				this.x = x;
				this.y = y;
			}

			end() {
				this.c.removeEventListener("mousemove", this.moveHandler);
				this.c.removeEventListener("touchmove", this.moveHandler);

				document.removeEventListener("mouseup", this.endHandler);
				document.removeEventListener("touchend", this.endHandler);

				/*let img = this.c.toDataURL("image/png");

				document.querySelector('input.signature').value = img;*/
				this.c.toBlob((blob)=>{
					const file = new File([blob], 'signature.png', { type: 'image/png' });
					  // Créer un DataTransfer pour simuler la sélection de fichier
					const dataTransfer = new DataTransfer();
					dataTransfer.items.add(file);

					// Attacher le fichier à l'input
					document.querySelector(".dropZone input").files = dataTransfer.files;
				}, "image/png");

				let submit = document.querySelector(".newJustif.menstruelActive input[type=submit]");
				submit.style.opacity = "1";
				submit.style.pointerEvents = "auto";
			}

			clear() {
				this.ctx.fillStyle = "#FFFFFF";
				this.ctx.fillRect(0, 0, this.c.width, this.c.height);
				this.ctx.beginPath();

				this.ctx.fillStyle = "#000000";
				this.ctx.font = "10px Arial";
				this.ctx.fillText("J'atteste sur l'honneur que le congé menstruel est saisi pour", 4, 10);
				this.ctx.fillText("motif de dysménhorrées.", 4, 24);
				this.ctx.fillText("Ne fonctionne pas pour les examens annoncés.", 4, 142);

				this.ctx.font = "40px Arial";
				this.ctx.fillStyle = "#eeeeee";
				this.ctx.fillText("Signature", 60, 85);

				let submit = document.querySelector(".newJustif.menstruelActive input[type=submit]");
				submit.style.opacity = "";
				submit.style.pointerEvents = "";
			}
		}

		customElements.define("zone-signature", signature);

	</script>

    <?php 
        include "$path/config/analytics.php";
    ?>
</body>
</html>
