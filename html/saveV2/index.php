<?php
	ob_start("ob_gzhandler");
	session_start();

/************************/
/* Authentification */
/************************/
	
	// http://notes.iutmulhouse.uha.fr/?t=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzZXNzaW9uIjoiZ2FicmllbC50cmVzY2hAdWhhLmZyIiwic3RhdHVzIjoiZXR1ZGlhbnQiLCJleHAiOjE2MDg0OTg0NDR9.tAJuG0R-7afo9za3tfBxBinBA7ArEH7s_nApoJimoDc

	use \Firebase\JWT\JWT;

	if(isset($_GET['t'])){
		$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
		include $path . '/includes/JWT/JWT.php';

		$key = "xk_5oqnSXpcM9nvoxfBNaQW2BphYvPMe4EHTctqVDvUDXb-fMfQOQkq3CPx9MO36jpKmLC-xvovYuZjgrd2uJk3VqyQUC99Z4oc-RorjS--ao8Gj_qf0uCyeZESqe7n8I";
		
		$decoded = JWT::decode($_GET['t'], $key, array('HS256'));
		$_SESSION['id'] = $decoded->session;
		$_SESSION['enseignant'] = false;//$decoded->status;

		$id = $_SESSION['id'];
		$sco_url = 'https://iutmscodoc9.uha.fr/ScoDoc/';
	}else{
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

		if(isset($_GET['debug'])){
			if($_SESSION['id'] == "sebastien.lehmann@uha.fr"){
				$_SESSION['id'] = "juliette.keller@uha.fr";
			}
		}

		$id = $_SESSION['id'];
		$sco_url = 'https://iutmscodoc9.uha.fr/ScoDoc/';
	
/**************************/
/* Recherche si l'utilisateur est un personnel de l'IUT */
/*************************/

		$_SESSION['enseignant'] = "vide";

		$handle = fopen("../etudiants/export_etu_iutmulhouse.txt", "r");
		while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
			if( strcasecmp($data[21], $id) == 0){	
				$_SESSION['enseignant'] = false;
				break;	
			}
		}
		if($_SESSION['enseignant'] == "vide"){
			$handle = fopen("../etudiants/export_ens_iutmulhouse.txt", "r");
			while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
				if( strcasecmp($data[2], $id) == 0){	
					$_SESSION['enseignant'] = true;
					break;	
				}
			}
			if($_SESSION['enseignant'] == "vide"){
				$handle = fopen("../etudiants/export_biat_iutmulhouse.txt", "r");
				while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
					if( strcasecmp($data[2], $id) == 0){	
						$_SESSION['enseignant'] = true;
						break;	
					}
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html lang=fr>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width">
		<title>Relevé de notes</title>
		<link rel="manifest" href="manifest.json">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<style>
			*{
				box-sizing: border-box;
			}
			html{
				scroll-behavior: smooth;
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
				z-index:1;
			}
			header>a{
				color: #FFF;
				text-decoration: none;
				padding: 10px 0 10px 0;
			}
			h1{
				margin:0;
			}
			main{
				padding:0 10px;
				margin-bottom: 64px;
				max-width: 1000px;
				margin: auto;
			}
			.prenom{
				text-transform: capitalize;
				color:#f44335;
			}
			
			.wait{
				width: 50px;
				height: 10px;
				background: #424242;
				margin: auto;
				animation: wait 0.6s ease-out alternate infinite;
			}
			@keyframes wait{
				100%{transform: translateY(-30px) rotate(360deg)}
			}
/**********************/
/* Gestion de semestres */
/**********************/

			.semestres{
				display: flex;
				flex-wrap: wrap;
			}
			.semestres>div{
				cursor:pointer;
				border: 1px solid #777;
				background: #FFF;
				padding: 10px;
				margin: 10px;
			}
			.semestres>.valide{
				background: #0C9;
				color: #FFF;
			}

/**********************/
/* Zone relevé de notes */
/**********************/
			.releve>a, .button{
				border-radius: 5px;
				margin: 10px;
				padding: 10px;
				display: table;
				box-shadow: 0 0 4px #000;
				background: #FFF;
				color: #000;
				text-decoration: none;
				cursor: pointer;
			}
			.total{
				font-size: 24px;
				margin: 10px;
				text-align: center;
			}
			.total>span{
				font-size: 18px;
				opacity: 0.6;
			}
			.ue, .module{
				border-radius: 10px;
				padding: 10px 20px;
				margin: 10px;
				box-shadow: 0 3px 6px #999;
			}
			.ue{
				background: #09C;
				color: #FFF;
				display: flex;
				justify-content: space-between;
				gap: 10px;
				cursor: pointer;
			}
			.ue>div:nth-child(1){
				max-width: 50%;
				display: -webkit-box;
  				-webkit-line-clamp: 3;
				-webkit-box-orient: vertical;  
				overflow: hidden;
			}
			.ue>div:nth-child(2){
				font-weight: bold;
				text-align: right;
			}
			.module{
				background: #FFF;
				margin-left: 30px;
			}
			.module>div, .eval{
				display: flex;
				justify-content: space-between;
				gap: 15px;
			}
			.module>div>div:nth-child(2){
				text-align: right;
			}
			.module>div>div>span{
				opacity: 0.6;
			}
			.coef{
				font-style: italic;
				opacity: 0.6;
				display: block;
			}
			.eval .coef{
				display:inline-block;
				width: 65px;
				text-align: left;
			}
			.eval{
				padding: 5px;
				background: #87efd5;
				margin-top: 2px;
				border-radius: 5px;
				cursor:pointer;
			}
			.eval:nth-child(odd){
				background: #a4d3e2;
			}
			.eval>div:nth-child(2){
				flex-shrink:0;
			}

			.checked, [data-note=undefined], [data-note=NP]{
				background: #D0D0D0;
				
			}
			[data-note=undefined], [data-note=NP]{
				cursor: initial;
			}
			.checked:nth-child(odd), [data-note=undefined]:nth-child(odd), [data-note=NP]:nth-child(odd){
				background: #F0F0F0;
			}
			.hide{
				display: none;
			}
			body:not(.ShowEmpty) [data-note=undefined], body:not(.ShowEmpty) [data-note=NP], body:not(.ShowEmpty) [data-note=EXC]{
				display: none !important;
			}
			.ShowEmpty .button{
				background: #0C9;
				color: #FFF;
			}

			<?php 
				if($_SESSION['enseignant'] == true){
			?>
/**********************/
/* Mode enseignant */
/**********************/
			.eval{
				cursor: initial;
			}
			.etudiant{
				margin: 20px auto 20px auto;
			}
			.etudiant>input{
				border: 1px solid #ef5350;
				padding: 20px;
				border-radius: 20px;
				font-size: 18px;
				display: inline-block;
				margin: 10px;
			}
			<?php } ?>
		</style>
		<meta name=description content="Relevé de note de l'IUT de Mulhouse">
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
				Bonjour <span class=prenom><?php echo explode('.', $id)[0]; ?></span>.
			</p>
			<p>
				<i>
					Ce relevé de notes est provisoire, il est fourni à titre informatif et n'a aucune valeur officielle.
				<br>
					La moyenne affichée correspond à la moyenne coefficientée des modules qui ont des notes.
				</i>
			</p>

			<?php 

				if($_SESSION['enseignant'] === true){
					echo '<div class=etudiant>Vous êtes un personnel de l\'IUT , <input required list=etudiants name=etudiant placeholder="Choisissez un étudiant" onchange="init(this);this.blur()"><datalist id=etudiants>';
					$handle = fopen("../etudiants/export_etu_iutmulhouse.txt", "r");
					while(($data = fgetcsv($handle, 1000, ':')) !== FALSE){
						echo "<option value='$data[21]'>$data[21]</option>";
					}
					echo '</datalist></div>';
				}
			?>

			<div class=semestres></div>
			<hr>
			<div class=releve></div>

			<div class=wait></div>
			
			<div class=button onclick="ShowEmpty()">Montrer les évaluations sans note</div>

			<hr>
			<small>Ce site utilise deux cookies permettant l'authentification au service et une analyse statistique anonymisée des connexions ne nécessitant pas de consentement selon les règles du RGPD.</small><br>
			<small>Application réalisée par Sébastien Lehmann, enseignant MMI - <a href="maj.html">voir les MAJ</a>.</small>
		</main>
		
		<script>
			<?php 
				if($_SESSION['enseignant'] === true){
					echo 'document.querySelector(".wait").style.display = "none";';
				}else{
					echo 'init();';
				}
			?>
			
			var etu;
			function init(obj = 0){
				etu = obj.value || "";
				fetch('get_sem_list.php?etu='+etu, {method: "POST"})
				.then(res => { return res.json() })
				.then(function(data) {
					if(data == "Fin de session"){
						location.reload();
					}else if(data == "problème nip"){
						document.querySelector(".semestres").innerHTML = "<b>Votre compte n'est pas encore dans l'annuaire. La mise à jour est faite en général tous les 15 jours, si le problème persiste, contactez votre responsable.</b>";
					}else if(data == "probleme recup semestre"){
						document.querySelector(".semestres").innerHTML = "<b>Problème de compte, vous n'êtes pas dans Scodoc ou votre numéro d'étudiant est erroné, si le problème persiste, contactez votre responsable.</b>";
					}else{
						let output = document.querySelector(".semestres");
						output.innerHTML = "";
						for(let i=0, n=data.length;i<n;i++){
							let div = document.createElement("div");
							div.dataset.sem = data[i];
							div.addEventListener("click", get_releve);
							div.innerText = "Semestre " + (n-i);
							output.appendChild(div);
						}
						document.querySelector(".semestres>div:nth-child(1)").click();
					}
				}).catch(function(error) {
					document.querySelector(".releve").innerHTML = 'Il y a eu un problème de connexion - vérifiez internet';
					document.querySelector(".wait").style.display = "none";
				});
			}

			function get_releve(){

				document.querySelector(".wait").style.display = "block";
				document.querySelector(".releve").innerHTML = "";
				Array.from(this.parentElement.children).forEach(e=>{
					e.classList.remove("valide");
				})
				this.classList.add("valide");

				document.querySelector(".releve").innerHTML = `
					<a href=bulletin_PDF.php?sem_id=${this.dataset.sem}&etu=${etu} target=_blank>
						Télécharger le relevé au format PDF
					</a>
					`;
				fetch('bulletin_JSON.php?sem_id='+this.dataset.sem+'&etu='+etu, {method: "POST"})
				.then(res => { return res.json() })
				.then(function(e) {
					if(e == "Fin de session"){
						location.reload();
					}else{
						if(e.rang){
							let decision = e.situation.split(". ");
							if(decision[1]){
								decision = "<b>"+decision[1] + ". " + decision[2]+"</b><br>";
							}else{
								decision = "";
							}
							document.querySelector(".releve").innerHTML += `
								<div class="total">
								${decision}
								Moyenne semestre : ${e.note.value} <br>
								Rang : ${e.rang.value || "Attente"} / ${e.rang.ninscrits} <br>
								<span>Classe : ${e.note.moy} - Max : ${e.note.max} - Min : ${e.note.min}</span>
								</div>
								${ue(e.ue)}`;
								
								set_checked();
						}else{
							document.querySelector(".releve").innerHTML = "<b>Relevé non disponible pour ce semestre, l'export n'est pas autorisé, veuillez contacter votre responsable.</b>";
						}	

						document.querySelector(".wait").style.display = "none";
					}
				})
				.catch(function(error) {
					document.querySelector(".releve").innerHTML = 'Il y a eu un problème de connexion - essayez de vous déconnecter et de revenir. Essayez également de vider les données de navigation (une nouvelle version qui corrigera ce bug arrive).';
					document.querySelector(".wait").style.display = "none";
				});
					
			}
			function ue(ue){
				let output = "";
				ue.forEach(e=>{
					output += `
						<div class=ue data-id="${e.acronyme}" onclick="openClose(this)">
							<div>${e.acronyme} - ${e.titre}</div>
							<div>
								Moyenne&nbsp;:&nbsp;${e.note.value}<br>Rang&nbsp;:&nbsp;${e.rang}
							</div>
						</div>
						${module(e.module)}`;
				})
				return output;
			}
			function module(module){
				let output = "";
				module.forEach(e=>{
					output += `
						<div class=module data-id="${e.titre}">
							<div>
								<div>${e.titre}<span class=coef>Coef ${e.coefficient}</span></div>
								<div>
									Moyenne&nbsp;:&nbsp;${e.note.value} - Rang&nbsp;:&nbsp;${e.rang.value}<br>
									<span>
										Classe&nbsp;:&nbsp;${e.note.moy} - Max&nbsp;:&nbsp;${e.note.max} - Min&nbsp;:&nbsp;${e.note.min}
									</span>
								</div>
							</div>
							

							${evaluation(e.evaluation)}
						</div>`;
				})
				return output;
			}

			function evaluation(evaluation){
				let output = "";
				evaluation.forEach(e=>{
					output += `
						<div class=eval onclick="check_eval(this)" data-id="${e.description}" data-note=${e.note}>
							<div>${e.description}</div>
							<div>${e.note}&nbsp;<span class=coef>Coef&nbsp;${e.coefficient}</span></div>
						</div>`;
				})
				return output;
			}

			function check_eval(obj){
<?php 
	if($_SESSION['enseignant'] !== true){
?>
				if(obj.dataset.note != "undefined" && obj.dataset.note != "NP"){
					obj.classList.toggle("checked");
					let ue = obj.parentElement;
					let security = 100;
					do{
						ue = ue.previousElementSibling;
						if(--security == 0)break;
					}while(ue.className != "ue");

					let id = `[data-id='${ue.dataset.id}']~[data-id='${obj.parentElement.dataset.id}']>[data-id='${obj.dataset.id}']`;

					if(localStorage.getItem(id) != obj.dataset.note){
						localStorage.setItem(id, obj.dataset.note);
					}else{
						localStorage.removeItem(id);
					}
				}
				
<?php 
	}
?>
			}
			
			function set_checked(){
<?php 
	if($_SESSION['enseignant'] !== true){
?>
				Object.keys(localStorage).forEach(function(e){
					let eval=document.querySelector(e);
					if(eval && eval.dataset.note == localStorage.getItem(e)){
						eval.classList.add("checked");
					}
				})

				let firstNotChecked = document.querySelector(".eval:not(.checked):not([data-note=undefined]):not([data-note=NP])");
				if(firstNotChecked){
					let y = firstNotChecked.parentElement.getBoundingClientRect().top + window.scrollY;

					window.scrollTo(0, y - 65); 
				}
<?php 
	}
?>
			}

			function openClose(obj){
				while(obj.nextElementSibling && obj.nextElementSibling.classList.contains("module")){
					obj = obj.nextElementSibling;
					obj.classList.toggle("hide");
				}
			}

			function ShowEmpty(){
				document.querySelector("body").classList.toggle("ShowEmpty");
			}
		
			if('serviceWorker' in navigator){
				navigator.serviceWorker.register('sw.js');
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
		<!--      Avec la participation des étudiants Lysandre et Méline.      -->
		<!-- ----------------------------------------------------------------- -->
	</body>
</html>