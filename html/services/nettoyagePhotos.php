<?php 
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Nettoyage Photos</title>
	<style>
        <?php include $_SERVER['DOCUMENT_ROOT']."/assets/styles/global.css"?>
		button {
			border: none;
			border-radius: 4px;
			background: #00be82;
			padding: 8px 32px;
			font-size: 16px;
			box-shadow: 0 2px 2px rgba(0,0,0, 0.5);
			cursor: pointer;
			transition: 0.1s;
		}
		button:hover {
			box-shadow: 0 2px 2px rgba(0,0,0, 0.9);
		}
		button:active {
			transform: translateY(2px);
			box-shadow: 0 0px 0px rgba(0,0,0, 0.9);
		}
		.contenu {
			display: grid;
			grid-template-columns: 1fr 1fr 1fr 1fr;
			gap: 16px;
		}
		.contenu>div {
			counter-reset: cpt;
		}
		.contenu>div>div>div {
			background: var(--fond-clair);
			border: 1px solid #CCC;
			padding: 2px 8px;
			margin: 2px 16px;
			border-radius: 2px;
		}
		.contenu>div>div>div:before {
			counter-increment: cpt;
			content: counter(cpt) " - ";
		}
		
	</style>
</head>
<body>
	<?php 
        $h1 = 'Nettoyage photos';
        include $_SERVER['DOCUMENT_ROOT']."/assets/header.php";
    ?>

	<main>
		<p>Bonjour <span class="nom"></span>, ravi de te revoir.</p>
		<p>Cette page a pour but de supprimer automatiquement les photos étudiants de la passerelle pour les étudiants qui n'ont pas été inscrits dans Scodoc depuis plus d'un an.</p>

		<p>!!! Attention, cette fonctionnalité n'est pas compatible avec PHP-FPM et FastCGI à cause d'un impossbilité de flush un flux de sorti pour le stream de réponse.</p>

		<p>Seul un super administrateur peut lancer la procédure.</p>

		<p>Cette procédure fait une requête à Scodoc pour chaque photo présente sur le serveur et peut prendre du temps. Plus de 160 étudiants devraient être traités à la minute. La limite d'exécusion est de 30min (j'espère que ça suffira).</p>

		<button class=go>C'est parti</button>
		
		<div class="contenu">
			<div>
				<h2>A traiter</h2>
				<div class="aTraiter"></div>
			</div>
			<div>
				<h2>Conservé</h2>
				<div class="conserve"></div>
			</div>
			<div>
				<h2>Supprimé</h2>
				<div class="supprime"></div>
			</div>
			<div>
				<h2>Problème</h2>
				<div class="probleme"></div>
			</div>
		</div>
	</main>

	<div class=auth>
        <!-- Site en maintenance -->
        Authentification en cours ...
    </div>

	<script src="../assets/js/theme.js"></script>
	<script>
		<?php
            include "$path/includes/clientIO.php";
		?>  

		checkStatut();

 		async function checkStatut(){
            let data = await fetchData("donnéesAuthentification");
            session = data.session;
            let auth = document.querySelector(".auth");
            auth.style.opacity = "0";
            auth.style.pointerEvents = "none";
            statutSession = data.statut;

            if(data.statut < SUPERADMINISTRATEUR){
				displayError("Cette fonctionnalité est uniquement accessible pour les super-administrateurs.");
			} else {
				document.querySelector(".go").addEventListener("click", go);
				
				let data = await fetchData("getAllStudentsPic");
				let output = '';
				data.forEach(e=>{
					let nip = e.split('.jpg')[0];
					output += `<div data-nip="${nip}">${nip}</div>`;
				})
				document.querySelector(".aTraiter").innerHTML = output;
			}
        }

		async function go() {
			document.body.classList.add('processing');
			const response = await fetch(
				"/services/data.php?q=cleanStudentsPic", 
				{
					headers: {
						//'Accept-Encoding': '0',
						//'Content-Encoding': '0',
						//'Accept-Encoding': 'gzip;q=0,deflate,sdch'
					}
				}
			);

			/* Meilleure methode non supportée pour le moment :
				https://caniuse.com/mdn-api_readablestream_--asynciterator
				https://developer.mozilla.org/en-US/docs/Web/API/Streams_API/Using_readable_streams
			*/
			/*for await (const chunk of response.body) {
				console.log(chunk);
			}*/

			const reader = response.body.getReader();
			while (true) {
				const { done, value } = await reader.read();
				if (done) {
					document.body.classList.remove('processing');
					document.querySelector(".probleme").append(...document.querySelectorAll(".aTraiter>div"));
					return;
				}
				let datas = JSON.parse("[" + new TextDecoder().decode(value).replaceAll("}{", "},{") + "]");
				datas.forEach(data=>{
					let element = document.querySelector(`[data-nip="${data.nip}"]`);
					document.querySelector(`.${data.statut}`).prepend(element);
				})
			}
		}
		
	</script>
</body>
</html>
