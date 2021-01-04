<?php
	ob_start("ob_gzhandler");
	session_start();

/************************/
/* Authentification */
/************************/
	if(!isset($_SESSION['id']) || $_SESSION['id'] == ''){
		require_once '../CAS/include/CAS.php';
		require_once '../CAS/config/cas_uha.php';

		// Initialize phpCAS
		phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
			
		// force CAS authentication
		phpCAS::setNoCasServerValidation() ;
		//phpCAS::setCasServerCACert($cas_server_ca_cert_path);
		phpCAS::forceAuthentication(); 
		
		$_SESSION['id'] = phpCAS::getUser();
	}
	$id = $_SESSION['id'];
	
/**************************/
/* Recherche si l'utilisateur est enseignant */
/*************************/

	$_SESSION['enseignant'] = -1;

	$handle = fopen("../etudiants/export_etu_iutmulhouse.txt", "r");
	while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
		if( strcasecmp($data[21], $id) == 0){	
			$_SESSION['enseignant'] = false;
			break;	
		}
	}
	if($_SESSION['enseignant'] == -1){
		$handle = fopen("../etudiants/export_ens_iutmulhouse.txt", "r");
		while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
			if( strcasecmp($data[2], $id) == 0){	
				$_SESSION['enseignant'] = true;
				break;	
			}
		}
		if(!$_SESSION['enseignant']){
			$handle = fopen("../etudiants/export_biat_iutmulhouse.txt", "r");
			while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
				if( strcasecmp($data[2], $id) == 0){	
					$_SESSION['enseignant'] = true;
					break;	
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width">
		<title>Relevé de notes</title>
		<style>
			*{
				box-sizing: border-box;
			}
			body{
				margin:0;
				font-family:arial;
				background: #FAFAFA;
			}
			header{
				position:sticky;
				top:0;
				padding:10px;
				background:#09C;
				display: flex;
				justify-content: space-between;
				color:#FFF;
				box-shadow: 0 2px 2px #888;
			}
			header>a{
				color: #FFF;
				text-decoration: none;
				padding: 10px 0 10px 0;
			}
			h1{
				margin:0;
			}
			.etudiant{
				width: 100%;
			}
			main{
				padding:0 10px;
				margin-bottom: 64px;
			}
			.prenom{
				text-transform: capitalize;
				color:#f44335;
			}
			[type=radio]{
				display: none;
			}
			label{
				display: inline-block;
				padding:20px;
				text-align:center;
				border: 1px solid #9C0;
				width: 100%;
				transition: 0.2s;
				background:#FFF;
				margin-top: -1px;
				cursor:pointer;
			}
			form{
				margin-top:10px;
			}
			[type=radio]:checked + label{
				background: #9C0;
				color:#FFF;
			}
			[type=submit]{
				background: #09C;
				color:#FFF;
				border: none;
				padding:20px;
				border-radius:5px;
				cursor:pointer;
				margin-top: 15px;
				transform: translateX(-100vw);
				transition: transform 0.4s;
				font-size: 16px;
				box-shadow: 0 2px 2px #888;

				position: fixed;
				right: 5px;
				bottom: 5px;
			}
			[type=submit]:hover{
				box-shadow: 0 2px 3px #424242;
			}
			[type=radio]:checked ~ [type=submit]{
				transform: translateX(0);
			}
			@media screen and (min-width: 768px){
				form{
					display:flex;
					flex-wrap: wrap;
					justify-content: space-evenly;
					align-items: top;
				}
				label{
					width:200px;
					height:200px;
					font-size: 24px;
					padding-top:56px;
					margin: 10px;
				}
				label:hover{
					animation: vibre 0.2s infinite alternate;
				}
				[type=submit]{
					position: static;
					width: 100%;
				}
				@keyframes vibre{
					0%{transform:translateX(-2px)}
					100%{transform:translateX(2px)}
				}
			}
			.etudiant{
				margin: 50px auto 50px auto;
			}
			.etudiant>input{
				border: 1px solid #ef5350;
				padding: 20px;
				border-radius: 20px;
				font-size: 18px;
				display: inline-block;
				margin: 10px;
			}
		</style>
	</head>
	<body>
		<header>

			<h1>
				Relevé de notes
			</h1>
			<a href=logout.php>Déconnexion</a>
		</header>
		<main>
			<p>
				Bonjour <span class=prenom><?php echo explode('.', $id)[0]; ?></span>, quel est ton département ?
			</p>
			
			
			<form action=bulletin.php>

				<?php 
					if($_SESSION['enseignant'] == true){
						echo '<div class=etudiant>Vous êtes enseignant, <input required list=etudiants name=etudiant placeholder="Choissez un étudiant"><datalist id=etudiants>';
						$handle = fopen("../etudiants/export_etu_iutmulhouse.txt", "r");
						while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
							echo "<option value='$data[21]'>$data[21]</option>";
						}
						echo '</datalist></div>';
					}
				?>

				<input type=radio name=departement value=GEII id=GEII><label for=GEII>GEII</label>
				<input type=radio name=departement value=GMP id=GMP><label for=GMP>GMP</label>
				<input type=radio name=departement value=GLT id=GLT><label for=GLT>GLT</label>
				<input type=radio name=departement value=GEA id=GEA><label for=GEA>GEA</label>
				<input type=radio name=departement value=SGM id=SGM><label for=SGM>SGM</label>
				<input type=radio name=departement value=MMI id=MMI><label for=MMI>MMI</label>
				<p>
					<i>Ce relevé de notes est provisoire, il est fourni à titre informatif et n'a aucune valeur officielle.</i>
				</p>
				<p>
					<i>La moyenne affichée correspond à la moyenne coefficientée des modules qui ont des notes.</i>
				</p>
				
				<input type=submit value="Récupérer">
			</form>
		</main>
		
		<script>
			document.querySelectorAll("[type=radio]").forEach((e)=>{
				e.addEventListener("click", select);
			});
			
			function select(){
				localStorage.setItem("value", this.value);
			}
			
			if(localStorage.value){
				document.querySelector("[value=" + localStorage.value + "]").checked = true;
			}
		</script>
		
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-126874346-1"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());
			gtag('config', '<GA_MEASUREMENT_ID>', { 'anonymize_ip': true });
			gtag('config', 'UA-126874346-1');
		</script>
		<!-- ----------------------------------------------------------------- -->
		<!-- Fait avec beaucoup d'amour par Sébastien Lehmann - enseignant MMI -->
		<!--     Merci à Denis Graef, Alexandre Kieffer et Bruno Colicchio.    -->
		<!-- ----------------------------------------------------------------- -->
	</body>
</html>