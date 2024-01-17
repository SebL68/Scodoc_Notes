<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Fréquentation</title>
	<style>
		<?php include $_SERVER['DOCUMENT_ROOT']."/assets/styles/global.css"?>
		body{
			padding: 32px 64px;
			max-width: 1200px;
			margin: auto;
		}
		h2 {
			padding: 8px 16px;
			cursor: initial;
		}
		span{
			font-weight: bold;
		}
		svg{
			vertical-align: middle;
		}

		.mobile {
			display: flex; 
			gap: 8px;
		}
		.mobile>* {
			background: #FFF;
			border: 1px solid #CCC;
			border-radius: 8px;
			padding: 8px 16px;
		}
		.canvas {
			height: 220px
		}
		.wait {
			top: 0;
		}
	</style>
</head>
<body>
	<div class="wait">
		<div class="dot"></div>
		<div class="dot"></div>
		<div class="dot"></div>
		<div class="dot"></div>
		<div class="dot"></div>
		<div class="land"></div>
	</div>

	<h1>Analyse du trafic de la passerelle</h1>

	<section>
		<h2>Demandes de relevés</h2>
		<p>Durant la dernière année, <span id="nbRelevé"></span> ont été ouverts sur la passerelle.</p>
		<div class=mobile>
			<div>
				<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0b0b0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12" y2="18"></line></svg>
				<span id="mobile"></span> des connexions ont été réalisées depuis un appareil mobile.
			</div>
			<div>
				<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0b0b0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
				<span id="ordi"></span> des connexions ont été réalisées depuis un ordinateur.
			</div>
		</div>
		<div class="canvas">
			<canvas id="chartRelevé"></canvas>
		</div>

		<p>Il y a eu <span id="nbNewSession"></span> durant l'année.</p>
		<div class="canvas">
			<canvas id="chartNewSession"></canvas>
		</div>
		
	</section>

	<section>
		<h2>Absences</h2>
		<p>La page absences a été visitée, <span id="nbAbsences"></span>.</p>
		<div class="canvas">
			<canvas id="chartAbsences"></canvas>
		</div>
		<p>La page gestion des absences a été visitée, <span id="nbGestionAbsences"></span>.</p>
		<div class="canvas">
			<canvas id="chartGestionAbsences"></canvas>
		</div>
	</section>

	<section>
		<h2>Page documents</h2>
		<p>La page documents a été visitée, <span id="nbDocuments"></span>.</p>
		<div class="canvas">
			<canvas id="chartDocuments"></canvas>
		</div>
	</section>

	<section>
		<h2>Relevé PDF</h2>
		<p>Les étudiants ont téléchargé <span id="nbRelevéPDF"></span> leur relevé PDF.</p>
		<div class="canvas">
			<canvas id="chartRelevéPDF"></canvas>
		</div>
	</section>

	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script>

		<?php
			$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
			include "$path/includes/clientIO.php";
		?>

		async function getData(){
			let date = new Date();
			let nowISO = date.toISOString().split('T')[0];
			date.setFullYear( date.getFullYear() - 1 );
			let oneYearAgoISO = date.toISOString().split('T')[0];

			document.querySelector("h1").innerText += 
				" " + oneYearAgoISO.split("-").reverse().join("/") + " - " +
				nowISO.split("-").reverse().join("/");

			let data = await fetchData(`getAnalyticsData&date_from=${oneYearAgoISO}&date_to=${nowISO}`);

			let results = accumulator(data);

			/* Affichage */
			document.querySelector("#nbRelevé").innerText = results.relevé.toLocaleString() + " relevés";
			document.querySelector("#mobile").innerText = Math.round(results.mobile / results.newSession * 100) + "%";
			document.querySelector("#ordi").innerText = Math.round(100 - results.mobile / results.newSession * 100) + "%";

			document.querySelector("#nbNewSession").innerText = results.newSession.toLocaleString() + " nouvelles sessions";

			document.querySelector("#nbAbsences").innerText = results.absences.toLocaleString() + " fois";
			document.querySelector("#nbGestionAbsences").innerText = results.gestionAbsences.toLocaleString() + " fois";
			document.querySelector("#nbDocuments").innerText = results.documents.toLocaleString() + " fois";
			document.querySelector("#nbRelevéPDF").innerText = results.relevéPDF.toLocaleString() + " fois";

			chart("#chartRelevé", results.relevéJour);
			chart("#chartNewSession", results.newSessionJour);
			chart("#chartAbsences", results.absencesJour);
			chart("#chartGestionAbsences", results.gestionAbsencesJour);
			chart("#chartDocuments", results.documentsJour);
			chart("#chartRelevéPDF", results.relevéPDFJour);
			
		}

		function accumulator(data){
			let results = {
				newSession: 0,
				newSessionJour : {},
				mobile: 0,

				relevé: 0,
				relevéJour : {},

				absences: 0,
				absencesJour : {},

				gestionAbsences: 0,
				gestionAbsencesJour : {},

				documents: 0,
				documentsJour : {},

				relevéPDF: 0,
				relevéPDFJour : {}
			}

			Object.entries(data).forEach(([day, dataDay]) => {
				dataDay.forEach(entry => {

					results[entry.type]++;
					if(entry.mobileDevice) results.mobile++;

					if(results[entry.type + "Jour"][day]) {
						results[entry.type + "Jour"][day]++;
					} else {
						results[entry.type + "Jour"][day] = 1
					}
					
				})
			})

			return results;
		}

		function chart(cible, data) {
			compiledData = [];

			Object.entries(data).forEach(([date, nb]) => {
				compiledData.push({
					x: date.split("-").reverse().join("/"),
					y: nb
				})
			})

			const ctx = document.querySelector(cible).getContext('2d');
			const chartSessions = new Chart(ctx, {
				type: 'line',
				data: {
					datasets: [{
						data: compiledData,
						fill: true,
						borderWidth: 1
					}]
				},
				options: {
					maintainAspectRatio: false,
					plugins: {
						legend: {
							display: false
						}
					},
					elements: {
						point:{
							radius: 0
						}
					},
					interaction: {
						mode: 'index',
						intersect: false
					},
					scales: {
						x: {
							grid: {
								display: false
							}
						}
					}
				}
			});
		}
				
		getData();

	</script>

</body>
</html>




