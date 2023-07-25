<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Photo de profil</title>
	<style>
		<?php include $_SERVER['DOCUMENT_ROOT']."/assets/styles/global.css"?>

		body{
			font-family: arial;
			background: var(--fond);
		}
		.grab *{
			cursor: grabbing !important;
		}

		.imageManager{
			text-align: center;
		}
		.dropZone{
			background: var(--fond);
			border-radius: 32px;
			border: 8px dashed var(--primaire);
			min-height: 50vh;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			font-size: 32px;
			margin-bottom: 16px;
			padding: 16px;
			transition: 0.2s;
		}
		.dropZone>svg{
			margin-top: auto;
		}
		.imageManager label{
			display: inline-block;
			cursor: pointer;
			background: var(--primaire);
			color: var(--primaire-contenu);
			padding: 4px 16px;
			margin: 8px;
			border-radius: 8px;
		}
		.imageManager input{
			display: none;
		}
		.consignes{
			font-size: 14px;
			text-align: left;
			margin-top: auto;
			pointer-events:none;
		}
		.fileOver{
			transform: scale(0.9);
		}
		.fileOver>*{
			pointer-events:none;
		}
		.fileOver path{
			animation: path 0.5s infinite linear;
			stroke-dasharray: 5;
		}
		@keyframes path{
			0%{stroke-dashoffset: 0}
			0%{stroke-dashoffset: 10}
		}

		.fadeOff{
			transition: 0.4s;
			opacity: 0;
			transform: scale(0.4);
		}
		/*************/
		.etape2 .dropZone,
		body:not(.etape2) .imageModifyer{
			display: none;
		}

		.imageModifyer{
			position: relative;
		}

		canvas{
			display: inline-block;
			width: 350px;
			height: 450px;
			border-radius: 16px;
			border: 1px solid #bbb;
			background: #000;
			cursor: grab;
		}

		.imageModifyer>button{
			display: block;
			width: 350px;
			cursor: pointer;
			background: var(--secondaire);
			color: var(--secondaire-contenu);
			padding: 4px 16px;
			margin: 8px auto;
			border-radius: 8px;
			border: none;
			font-size: 32px;
		}
		.imageModifyer>button:nth-child(3){
			background: var(--accent);
			color: var(--accent-contenu);
		}
		.imageModifyer>div{
			position: absolute;
			left: calc(50% + 140px);
			top: 8px;
			color: var(--gris-estompe);
			background: var(--fond-clair);
			border: 1px solid #bbb;
			width: 26px;
			height: 26px;
			border-radius: 4px;
			font-size: 22px;
			cursor: pointer;
		}
		.imageModifyer>div:hover{
			color: var(--gris);
		}
		.imageModifyer>.moins{
			top: 38px;
		}
		.imageModifyer>.zero{
			top: 88px;
		}
		
	</style>
</head>
<body>
	<div class="imageManager">
		<a href="/"><svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="var(--contenu)" stroke-width="2" stroke-linecap="round"><path d="M20 9v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9"/><path d="M9 22V12h6v10M2 10.6L12 2l10 8.6"/></svg></a>
		<h1>Photo de profil</h1>
		<form class=dropZone>
			<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 24 24" fill="none" stroke="#0099cc" stroke-width="2" stroke-linecap="round"><path pathlength="100" d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
			<div>
				Déposez une image ou <br>
				<label>
					Choisissez un fichier
					<input type=file accept=image/*>
				</label>
			</div>
			<div class="consignes">
				La photo servira uniquement pour les trombinoscopes et les absences. Vous pouvez la modifier à tout moment. La photo est conservée au maximum 1 an après la fin du cursus universitaire.<br><br>
				Cette photo est utilisée à des fins sérieuses, elle doit vous représenter et permettre de vous reconnaître comme vous êtes au quotidien, évitez :
				<ul>
					<li>les filtres,</li>
					<li>les grimaces,</li>
					<li>les déguisements,</li>
					<li>etc.</li>
				</ul>
			</div>
		</form>


		<div class=imageModifyer>
			<canvas width=350 height=450></canvas>
			<button>Valider</button>
			<button>Supprimer</button>
			<div class="plus">+</div>
			<div class="moins">-</div>
			<div class="zero">●</div>
		</div>
		
	</div>
	<script src="assets/js/theme.js"></script>
	<script>
		document.querySelector(".dropZone").addEventListener("drop", dropFile);
		document.querySelector(".dropZone input").addEventListener("change", dropFile);
		document.querySelector(".dropZone").addEventListener("dragover", dragOver);
		document.querySelector(".dropZone").addEventListener("dragleave", dragLeave);

		document.querySelector(".plus").addEventListener("click", zoomPlus);
		document.querySelector(".moins").addEventListener("click", zoomMoins);
		document.querySelector(".zero").addEventListener("click", raz);

		document.querySelector("canvas").addEventListener("mousedown", grabStart);
		document.querySelector("canvas").addEventListener("touchstart", grabStart);

		document.querySelector(".imageModifyer>button:nth-child(2)").addEventListener("click", valider);
		document.querySelector(".imageModifyer>button:nth-child(3)").addEventListener("click", supprimer);

		let canvas = document.querySelector("canvas").getContext('2d');
		let reader = new FileReader();
		let img = new Image();
		let zoom = 1;
		let dx = 0;
		let dy = 0;
		let grabX;
		let grabY;

		fetch("services/data.php?q=getStudentPic")
		.then(r=>{
			if(r.headers.get('Content-Type') != "image/svg+xml"){
				return r.blob();
			}
		})
		.then(blob=>{
			if(blob)
				manageFile(URL.createObjectURL(blob));
		})

		function dropFile(event){
			event.preventDefault();
			this.classList.remove("fileOver");
			
			if(event.target.files?.[0] || event.dataTransfer.items[0].type.match('^image/')){
				let file = event.target.files?.[0] || event.dataTransfer.items[0].getAsFile();

				reader.readAsDataURL(file);
				reader.onloadend = ()=>{
					manageFile(reader.result)
				}
			}
		}

		function manageFile(src){
			img.src = src;

			img.onload = ()=>{
				zoom = 1;
				dx = 0;
				dy = 0;
				draw();
				document.querySelector(".dropZone").classList.add("fadeOff");
				setTimeout(()=>{document.body.classList.add("etape2")},400);
			}
		}
		function dragOver(event){
			event.preventDefault();
			this.classList.add("fileOver")
		}
		function dragLeave(){
			this.classList.remove("fileOver")
		}

		function zoomPlus(event){
			event.stopPropagation();
			zoom /= 0.9;
			draw();
		}

		function zoomMoins(event){
			event.stopPropagation();
			zoom *= 0.9;
			draw();
		}
		function raz(event){
			event.stopPropagation();
			zoom = 1;
			dx = 0;
			dy = 0;
			draw();
		}

		function grabStart(event){
			document.body.classList.add("grab");
			document.body.addEventListener("mouseup", grabEnd);
			document.body.addEventListener("touchend", grabEnd);

			document.body.addEventListener("mousemove", grabMove);
			document.body.addEventListener("touchmove", grabMove);

			grabX = (event.pageX || event.touches[0].clientX) - dx;
			grabY = (event.pageY || event.touches[0].clientY) - dy;
		}
		function grabMove(event){
			dx = (event.pageX || event.touches[0].clientX) - grabX;
			dy = (event.pageY || event.touches[0].clientY) - grabY;
			draw();
		}
		function grabEnd(event){
			document.body.classList.remove("grab");
			document.body.removeEventListener("mouseup", grabEnd);
			document.body.removeEventListener("touchend", grabEnd);
			document.body.removeEventListener("mousemove", grabMove);
			document.body.removeEventListener("touchmove", grabMove);
		}

		function draw(){
			let scale = 350 / img.width;
			let width =  img.width * scale * zoom;
			let height = img.height * scale * zoom;
			let dxZoom = dx + (1 - zoom) * 350 / 2;
			let dyZoom = dy + (1 - zoom) * 350 / 2;
			canvas.clearRect(0, 0, 350, 450);
			canvas.drawImage(img, dxZoom, dyZoom, width, height);
		}

		function valider(){
			document.querySelector("canvas").toBlob((blob)=>{
				let formData = new FormData();

				let token = (window.location.search.match(/token=([a-zA-Z0-9._-]+)/)?.[1] || ""); // Récupération d'un token GET pour le passer au service
				if(token){
					formData.append('token', token);
				}
				formData.append('image', blob, "photo.jpg");

				fetch("services/data.php?q=setStudentPic",
					{
						method: "POST",
						body: formData
					}
				)
				.then(r=>{return r.json()})
				.then((data)=>{
					if(data.redirect){
						// Utilisateur non authentifié, redirection vers une page d'authentification pour le CAS.
						// Passage de l'URL courant au CAS pour redirection après authentification
						window.location.href = data.redirect + "?href="+encodeURIComponent(window.location.href); 
					}
					if(data.erreur){
						// Il y a une erreur pour la récupération des données - affichage d'un message explicatif.
						//displayError(data.erreur);
					}else if(data.result == "OK"){
						this.innerText = "C'est tout bon !";
						fetch("/services/data.php?q=getStudentPic");
						setTimeout(()=>{window.location.href = "/"}, 1000); 
					} else {
						this.innerText = "L'enregistrement a échoué";
						setTimeout(()=>{window.location.href = "/"}, 1000); 
					}
				})
			}, 'image/jpeg', 0.8);
		}

		function supprimer(){
			fetch("services/data.php?q=deleteStudentPic")
			.then(r=>{return r.json()})
			.then(function(data) {
				if(data.redirect){
					// Utilisateur non authentifié, redirection vers une page d'authentification pour le CAS.
					// Passage de l'URL courant au CAS pour redirection après authentification
					window.location.href = data.redirect + "?href="+encodeURIComponent(window.location.href); 
				}
				if(data.erreur){
					// Il y a une erreur pour la récupération des données - affichage d'un message explicatif.
					//displayError(data.erreur);
				}else if(data.result == "OK"){
					document.body.classList.remove("etape2");
					document.querySelector(".dropZone").classList.remove("fadeOff");
					fetch("services/data.php?q=getStudentPic");
				}
			})
		}
	</script>

</body>
</html>

