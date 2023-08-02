<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Fréquentation</title>
	<style>
		<?php include $_SERVER['DOCUMENT_ROOT']."/assets/styles/global.css"?>
		.wait{
			position: fixed;
			width: 50px;
			height: 10px;
			background: var(--gris);
			top: 80px;
			left: 50%;
			margin-left: -25px;
			animation: wait 0.6s ease-out alternate infinite;
		}
		@keyframes wait{
			100%{transform: translateY(-30px) rotate(360deg)}
		}
	</style>
</head>
<body>
    <div class="wait"></div>
	<canvas id="myChart" width="400" height="400"></canvas>

	<script src="../assets/js/theme.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script>

		<?php
			$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
			include "$path/includes/clientIO.php";
		?>

		async function getData(){
			let data = await fetchData("getAnalyticsData&date_from=2023-07-01&date_to=2023-08-01");

			const ctx = document.getElementById('myChart').getContext('2d');
			const myChart = new Chart(ctx, {
				type: 'line',
				data: {
					datasets: [{

						//data: Object.values(data)
						data: [{
							x: '2021-11-01 23:39:30',
							y: 50
						}, {
							x: '2021-11-07 01:00:28',
							y: 60
						}, {
							x: '2021-11-07 09:00:28',
							y: 20
						}]
					}]
				},
				
			});
		}

		getData();

		function accumulator(data, duration){
			
		}
		
	</script>

</body>
</html>




