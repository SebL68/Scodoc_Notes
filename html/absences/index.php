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
        <?php include $_SERVER['DOCUMENT_ROOT']."/assets/header.css"?>
        .admin{
            background: #FFF;
            color: #424242;
            margin-left: auto;
            padding: 8px 16px;
            border-radius: 16px;
        }
        main{
            margin: 0 auto 20px auto;
            text-align: center;
        }
        .contenu{
            opacity: 0.5;
            pointer-events: none;
            user-select: none;
        }
        .ready{
            opacity: initial;
            pointer-events: initial;
        }
        .message{
            position: fixed;
            bottom: 100%;
            left: 50%;
            z-index: 1000;
            padding: 20px;
            border-radius: 0 0 10px 10px;
            background: #90c;
            color: #FFF;
            font-size: 24px;
            animation: message 3s;
            transform: translate(-50%, 0);
        }
        @keyframes message{
            20%, 80%{transform: translate(-50%, 100%)}
        }
        .capitalize{
            text-transform: capitalize;
        }
/**********************/
/*   Zones de choix   */
/**********************/
        .zone{
            background: #FFF;
            padding: 8px;
            margin-bottom: 8px;
            border-radius: 4px;
            border: 1px solid #CCC;
        }
		select{
			font-size: 21px;
			padding: 10px;
			margin: 5px auto;
			background: #09c;
			color: #FFF;
			border: none;
			border-radius: 10px;
            max-width: 100%;
            display: table;
            box-shadow: 0 2px 2px #888;
		}
        .highlight{
            animation: pioupiou 0.4s infinite ease-in alternate;
        }
        @keyframes pioupiou{
            0%{
                box-shadow: 0 0 4px 0px orange;
            }
            100%{
                box-shadow: 0 0 4px 2px orange;
            }
        }

/*******************************/
/* Listes étudiants */
/*******************************/
        .flex{
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        
        .groupes{
            margin-bottom: 10px;
			display: flex;
            justify-content: center;
        }
        .groupe{
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 10px;
            margin: 2px;
            background: #09C;
            color: #FFF;
            border-radius: 8px;
        }
        @media screen and (max-width: 700px){
            .flex{
                flex-direction: column-reverse;
                align-items: center;
            }
            .groupes{
                margin-right: 20px;
                justify-content: center;
            }
        }
        .selected{
            opacity: 0.5;
        }
        .hide{
            display: none !important;
        }
        .etudiants{
            counter-reset: cpt;
			display: grid;
			justify-content: center;
			gap: 2px;
        }
       
/*****************************/
/* Module choix heure / date */
/*****************************/
		.date{
			display: grid;
			grid-template-columns: 60px 1fr 60px;
			column-gap: 8px;
			margin-bottom: 32px;
		}

		.date>.info {
			text-align: center;
			font-size: 24px;
			grid-column: span 3;
		}

		.date>svg {
			cursor: pointer;
			transition: 0.06s;
			transition-timing-function: ease-in;
			background: #FFF;
			box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
			border-radius: 8px;
		}

		.date>svg:active {
			box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25);
			transform: translateY(3px);
		}

		.timeZone {
			height: 60px;
			flex: 1;
			position: relative;
			touch-action: none;
			background: #FFF;
			box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
			border-radius: 8px;
		}

		.timeZone>.slider {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			background: rgba(0, 204, 153, 0.5);
			border: 2px solid #0c9;
			border-radius: 8px;
			display: flex;
			justify-content: center;
			align-items: center;
			cursor: grab;
		}

		.timeZone>.slider>.sizer {
			position: absolute;
			top: 13px;
			bottom: 13px;
			right: -1px;
			transform: translateX(50%);
			background: #0c9;
			border-radius: 4px;
			display: flex;
			cursor: e-resize;
		}

		.timeZone>.slider>.sizer::before {
			content: "";
			display: inline-block;
			background: #FFF;
			width: 2px;
			margin: 6px;
		}

		.infoHeures {
			display: flex;
			justify-content: space-between;
			margin: -8px 68px 0 68px;
			grid-column: span 3;
		}

		.infoHeures>div {
			position: relative;
			width: 2px;
			height: 16px;
			background: #424242;
		}

		.infoHeures>div>div {
			position: absolute;
			top: calc(100% + 2px);
			left: 1px;
			transform: translateX(-50%);
		}
		/*@media screen and (max-width: 600px) {*/
			.date{
				gap: 8px;
			}
			.date>.info{
				grid-column: span 1;
				align-self: center;
			}
			.date>.jourMoins{
				grid-row: 1;
			}
			.date>.jourPlus{
				grid-row: 1;
				grid-column: 3;
			}
			.date>.timeZone{
				grid-column: span 3;
			}
			.date>.infoHeures{
				margin: -16px 0 0 0;
			}
		/*}*/

/*****************************/
/* Liste étudiants */
/*****************************/
        .btnAbsences{
            position: relative;
            text-align: left;
            padding: 4px 4px 6px 20px;
			margin: 0px 22px 0px 62px;
			border-radius: 12px;

			display: flex;
			flex-wrap: wrap;
			align-items: center;
            gap:6px;
			row-gap: 10px;
        }
		.btnAbsences:hover{
			background: #ccc;
		}
		.grpBtn{
			display: flex;
			gap:4px;
		}
		.btn{
			border-radius: 10px;
            box-shadow: 0 2px 2px 2px #ddd;
			background: #FFF;
			cursor: pointer;
			transition: 0.1s;
			padding: 4px;
		}
		.miniature{
			position: absolute;
			top: -2px;
			left:-34px;
			width: 38px;
			height: 48px;
			
		}
		.miniature:hover{
			z-index: 1;
		}
		.miniature>img{
			pointer-events:none;
			background: #FFF;
			width: 100%;
			transition: 0.2s;
			transform-origin: 0% 100%;
			border-radius: 8px 0 0 8px;
			box-shadow: 0 2px 2px 2px #ddd;
		}
		.miniature:hover>img{
			transform: translate(38px, -52px) scale( calc(250/38) ) ;
			border-radius: 2px;
			box-shadow: none;
		}
        .btnAbsences>.nomEtudiants::before{
            counter-increment: cpt;
            content: counter(cpt) " - ";
            display: inline-block;
        }
        .btnAbsences>.nomEtudiants{
            flex: 1;
			position: relative;
        }
		.hint{
			position: absolute;
			top: calc(100% + 4px);
			left: 0;
			right: 0;
			height: 4px;
			background: #fff;
			outline: 1px solid #aaa;
		}
		.hint>div{
			position: absolute;
			top: 0;
			bottom: 0;
			background: red;
		}
		.hintCreneau{
			position: absolute;
			top: calc(100% + 3px);
			height: 6px;
			/*background: rgba(0, 204, 153, 0.5);*/
			outline: 2px solid #0c9;
			border-radius: 2px;
			transition: 0.2s;
			pointer-events: none;
		}
		.btn{
			display: flex;
		}
		.btn:hover{
			outline: 1px solid #90c;
		}
        .btn:active{
            transform: translateY(2px);
            box-shadow: 0 0 0 0 #777;
            outline: 1px solid #777;
            transition: 0s;
			z-index: 1;
        }
		[data-statut=present] [data-command=present],
		.hint [data-statut=present]{
            background: #00bcd4 !important;
			color: #FFF;
        }
        [data-statut=absent] [data-command=absent],
		.hint [data-statut=absent]{
            background: #ec7068 !important;
            color: #FFF;
        }
		[data-statut=retard] [data-command=retard],
		.hint [data-statut=retard]{
            background: #f3a027 !important;
            color: #FFF;
        }
        /*[data-statut=excuse] [data-command=present],
		.hint [data-statut=present]{
            background: #0C9 !important;
        }*/
    </style>
    <meta name=description content="Gestion des absences de l'<?php echo $Config->nom_IUT; ?>">
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

        <div class="zone">
            <select id=departement class=highlight onchange="clearStorage(['semestre', 'matiere']);selectDepartment(this.value)">
                <option value="" disabled selected hidden>Choisir un département</option>
                <?php
                    include "$path/includes/scodoc.class.php";
					$Scodoc = new Scodoc();
					$listDepartement = $Scodoc->getDepartmentsList();
                    foreach($listDepartement as $departement){
                        echo "<option value=$departement>$departement</option>";
                    }
                ?>
            </select>

            <select id=semestre onchange="clearStorage(['matiere']);selectSemester(this.value)" disabled>
                <option value="" disabled selected hidden>Choisir un semestre</option>
            </select>

            <select id=matiere onchange="selectMatiere(this.value)" disabled>
                <option value="" disabled selected hidden>Choisir une matière</option>
            </select>
        </div>
   

        <div class=contenu></div>
        <div class=wait></div>
        
    </main>

    <div class=auth>
        <!-- Site en maintenance -->
        Authentification en cours ...
    </div>
    <script>
		<?php
            include "$path/includes/clientIO.php";
		?>  
        document.querySelector("#absences").classList.add("navActif");
/*********************************************/
/* Vérifie l'identité de la personne et son statut
/*********************************************/		
        var session = "";
        var statutSession = "";
        async function checkStatut(){
            let data = await fetchData("donnéesAuthentification");
            session = data.session;
            document.querySelector(".nom").innerText = data.name;
            let auth = document.querySelector(".auth");
            auth.style.opacity = "0";
            auth.style.pointerEvents = "none";
            statutSession = data.statut;

            if(data.statut >= PERSONNEL){
                document.querySelector("body").classList.add('personnel');
				if(data.statut >= ADMINISTRATEUR){
					document.querySelector("#admin").style.display = "inherit";
				}
                /* Gestion du storage remettre le même état au retour */
                let departement = localStorage.getItem("departement");
                if(departement){
                    document.querySelector("#departement").value = departement;
                    selectDepartment(departement);
                }

			} else {
				document.querySelector(".contenu").innerHTML = "Ce contenu est uniquement accessible pour des personnels de l'IUT. ";
			}
        }
/*********************************************/
/* Récupère et traite les listes d'étudiants du département */
/*********************************************/		
        var departement = "";
        var semestre = "";
        var matiere = "";
        var UE = "";
        var matiereComplet = "";
        var dataEtudiants;
		var moduleDate;

        async function selectDepartment(dep){
            departement = dep;
			let data = await fetchData("semestresDépartement&dep="+departement);
			
			let select = document.querySelector("#semestre");
			select.innerHTML = `<option value="" disabled selected hidden>Choisir un semestre</option>`;
			data.forEach(function(semestre){
				let option = document.createElement("option");
				option.value = semestre.id;
				option.innerText = `${semestre.titre_court} - semestre ${semestre.num}`;
				select.appendChild(option);
            });
            document.querySelector("#departement").classList.remove("highlight");
            document.querySelector(".contenu").classList.remove("ready");
            select.disabled = false;
            select.classList.add("highlight");
            document.querySelector("#matiere").disabled = true;

            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('departement', departement);

            let semestre = localStorage.getItem("semestre");
            if(semestre){
                document.querySelector("#semestre").value = semestre;
                selectSemester(semestre);
            }
		}
		
		async function selectSemester(sem){
            semestre = sem;
            let data = await fetchData(`UEEtModules&dep=${departement}&semestre=${semestre}`);

			let select = document.querySelector("#matiere");
			select.innerHTML = `<option value="" disabled selected hidden>Choisir une matière</option>`;
			data.forEach(function(ue){
                let optgroup = document.createElement("optgroup");
                optgroup.label = ue.UE;

                ue.modules.forEach(function(module){
                    let option = document.createElement("option");
                    option.value = module.code;
                    option.innerText = module.titre;
                    optgroup.appendChild(option);
                });

				select.appendChild(optgroup);
            });
            document.querySelector("#semestre").classList.remove("highlight");
            select.disabled = false;
            document.querySelector(".contenu").classList.remove("ready");
            select.classList.add("highlight");

            getStudentsListes();
            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('semestre', semestre);

            let matiere = localStorage.getItem("matiere");
            if(matiere){
                document.querySelector("#matiere").value = matiere;
                selectMatiere(matiere);
            }
		}
        
        async function selectMatiere(mat){
            matiere = mat;
            let obj = document.querySelector('#matiere [value="'+matiere+'"]');
            matiereComplet = obj.innerText;
            UE = obj.parentElement.label;

            document.querySelector(".contenu").classList.add("ready");
            document.querySelector("#matiere").classList.remove("highlight");
            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('matiere', matiere);
        }

        async function getStudentsListes(){
            dataEtudiants = await fetchData(`listeEtudiantsSemestre&semestre=${semestre}&absences=true`);
            document.querySelector(".contenu").innerHTML += createSemester(dataEtudiants);

			document.querySelectorAll(".btn").forEach(btn=>{ 
				btn.addEventListener("click", setAbsence) 
			});

			moduleDate = new choixDate(
				{
					heureDebut: <?php echo $Config->absence_heureDebut; ?>,
					heureFin: <?php echo $Config->absence_heureFin; ?>,
					pas: <?php echo $Config->absence_pas; ?>,
					dureeSeance: <?php echo $Config->absence_dureeSeance; ?>,
					callback: setDate
				}
			);

			moduleDate.doCallback();
            //showAbsences();  
        }

        function clearStorage(keys){
            keys.forEach(function(key){
                localStorage.removeItem(key);
            });
        }

        function createSemester(liste){
			var output = "";

            var groupes = "";
            if(liste.groupes.length > 1){
                liste.groupes.forEach(groupe=>{
                    groupes += `<div class=groupe data-groupe="${groupe}" onclick="hideGroupe(this)">${groupe}</div>`;
                })
            }
            output += `
				<div class=groupes>${groupes}</div>
				<!-- Module choix date / heure -->
				<div class="date">
					<div class="info">Vendredi 04/02/2022</div>
					<svg class="jourMoins" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#424242"
						stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
						<path d="M15 18l-6-6 6-6"></path>
					</svg>
					<div class="timeZone">
						<div class="slider">
							<div class="sizer"></div>
							<div class="sliderInfo"></div>
						</div>
					</div>
					<svg class="jourPlus" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#424242"
						stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
						<path d="M9 18l6-6-6-6"></path>
					</svg>
					<div class="infoHeures"></div>
				</div>
				<!-- / -->
				<div class=etudiants>${createStudents(liste.etudiants)}</div>
            `;

            return output;
        }

        function createStudents(etudiants){
			var output = "";
           
			etudiants.forEach(etudiant=>{
				output += `
					<div class="btnAbsences ${etudiant.groupe?.replace(/ |\./g, "") || "Groupe1"}"
						data-nom="${etudiant.nom}" 
						data-prenom="${etudiant.prenom}" 
						data-groupe="${etudiant.groupe}"
						data-nip="${etudiant.nip}"
						title="${etudiant.groupe}">

						<div class="miniature" onclick="event.stopPropagation()">
							<img src="../services/data.php?q=getStudentPic&nip=${etudiant.num_etudiant}">
						</div>
						
						<div class="nomEtudiants">
							<b>${etudiant.nom}</b>
							<span>${etudiant.prenom}</span>
							<div class=hint></div>
							<div class=hintCreneau></div>
						</div>

						<div class=grpBtn>
							<div class=btn data-command=present  title=Présent>
								<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0b0b0b" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
							</div>
							<div class=btn data-command=absent title=Absent>
								<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0b0b0b" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
							</div>
							<div class=btn data-command=retard title="En retard">
								<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0b0b0b" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
							</div>
							<div class=btn data-command=unset title=Annuler>
								<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0b0b0b" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
							</div>
						</div>
        
                    </div>
				`;
			})
			return output;
		}

		function hideGroupe(obj){
			let nbSelected = obj.parentElement.querySelectorAll(".selected").length;
			let nbBtn = obj.parentElement.children.length;
			
			if(nbSelected == 0){
				Array.from(obj.parentElement.children).forEach(e=>{
					e.classList.toggle("selected");
				})
			}
			obj.classList.toggle("selected");

			nbSelected = obj.parentElement.querySelectorAll(".selected").length;
			if(nbSelected == nbBtn){
				Array.from(obj.parentElement.children).forEach(e=>{
					e.classList.toggle("selected");
				})
			}
			
			let groupesSelected = [];
			obj.parentElement.querySelectorAll(":not(.selected)").forEach(e=>{
				groupesSelected.push(e.dataset.groupe);
			})

			document.querySelectorAll(".btnAbsences").forEach(e=>{
				if(groupesSelected.includes(e.dataset.groupe)){
					e.classList.remove("hide")
				} else {
					e.classList.add("hide")
				}	
			})
        }

/*****************************/
/* Module choix date / heure */
/*****************************/
		class choixDate {
			constructor(config) {
				this.heureDebut = config.heureDebut || 8;
				this.heureFin = config.heureFin || 20;
				this.pas = config.pas || 2;
				this.dureeSeance = config.dureeSeance || 2;
				this.callback = config.callback;

				this.debut;
				this.fin;

				this.pasSize = 100 / ((this.heureFin - this.heureDebut) / this.pas);
				this.slider = document.querySelector(".slider");
				this.sizer = document.querySelector(".sizer");

				this.posiXStart;
				this.timeZoneSize;
				this.sliderSize;

				this.handleSliderMove = (event) => { this.sliderMove(event) };
				this.handleSliderStop = (event) => { this.sliderStopGrab(event) };
				this.handleSizerMove = (event) => { this.sizerMove(event) };
				this.handleSizerStop = (event) => { this.sizerStopGrab(event) };

				/* Mise en place du jour actuel */
				this.date = new Date();
				this.joursFR = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
				document.querySelector(".date>.info").innerText = `${this.joursFR[this.date.getDay()]} ${this.date.toLocaleDateString()}`;
				document.querySelector(".jourMoins").addEventListener("click", (event) => { this.changeJour(event) });
				document.querySelector(".jourPlus").addEventListener("click", (event) => { this.changeJour(event) });
				document.querySelector(".jourMoins").addEventListener("mousedown", (event) => { event.preventDefault() });	// Eviter la selection au double click,
				document.querySelector(".jourPlus").addEventListener("mousedown", (event) => { event.preventDefault() });

				/* Mise en place des heures */
				let output = "";

				for (let i = this.heureDebut; i <= this.heureFin; i += this.pas) {
					output += `<div>
									<div>${(i % 1 == 0) ? i :/*Math.floor(i)+"<sup>½</sup>"*/""}</div>
								</div>`;
				}
				document.querySelector(".infoHeures").innerHTML = output;
				this.slider.style.width = `calc(${this.dureeSeance / this.pas * this.pasSize}% + 2px)`;

				/* Gestion du slider */
				this.slider.addEventListener("mousedown", (event) => { this.sliderStartGrab(event) });
				this.slider.addEventListener("touchstart", (event) => { this.sliderStartGrab(event) });

				/* Gestion du sizer */
				this.sizer.addEventListener("mousedown", (event) => { this.sizerStartGrab(event) });
				this.sizer.addEventListener("touchstart", (event) => { this.sizerStartGrab(event) });

				/* Initialisation de l'heure actuelle */
				let heure = this.date.getHours() + this.date.getMinutes() / 60;	// Heure en décimale : exemple 10h30 => 10.5
				let startPosition = this.limit(
					Math.floor((heure - this.heureDebut) / this.pas),
					0,
					(this.heureFin - this.heureDebut - this.dureeSeance) / this.pas
				);
				this.setPosition(startPosition);

				return this;
			}
			/**********************************/
			/* Méthode d'aide                 */
			/**********************************/
			limit(value, min, max) {
				if (value > max) {
					return max;
				} else if (value < min) {
					return min;
				} else {
					return value;
				}
			}

			doCallback(){
				if(typeof(this.callback) === "function"){
					this.callback(
						{
							date: this.date.toISOString().split("T")[0],
							debut: this.debut,
							fin: this.fin
						}
					);
				}
			}
			/**********************************/
			/* Jours + / -                    */
			/**********************************/
			changeJour(event) {
				if (event.currentTarget.classList.contains("jourPlus")) {
					this.date.setDate(this.date.getDate() + 1);
				} else {
					this.date.setDate(this.date.getDate() - 1);
				}
				document.querySelector(".date>.info").innerText = `${this.joursFR[this.date.getDay()]} ${this.date.toLocaleDateString()}`;

				this.doCallback();
			}

			/**********************************/
			/* Gestion du changement d'heures */
			/**********************************/
			sliderStartGrab(event) {
				this.posiXStart = (event.pageX || event.changedTouches[0].pageX) - this.slider.offsetLeft;	// Position souris - position de départ
				this.timeZoneSize = document.querySelector(".timeZone").offsetWidth;
				this.sliderSize = this.slider.offsetWidth;

				this.slider.style.cursor = "grabbing";
				document.addEventListener("mousemove", this.handleSliderMove);
				document.addEventListener("touchmove", this.handleSliderMove);
				document.addEventListener("mouseup", this.handleSliderStop);
				document.addEventListener("touchend", this.handleSliderStop);
			}

			sliderMove(event) {
				let deltaX = this.limit(	// Borné entre le début et la fin de timeZone;
					(event.pageX || event.changedTouches[0].pageX) - this.posiXStart,
					0,
					this.timeZoneSize - this.sliderSize	// On soustrait la taille de l'élément
				);

				this.slider.style.left = 100 * deltaX / this.timeZoneSize + "%";
				event.preventDefault();
			}
			sliderStopGrab(event) {
				let numPosi = Math.round(parseInt(this.slider.style.left) / this.pasSize);
				this.setPosition(numPosi);
				this.slider.children[1].innerText = "";

				this.slider.style.cursor = "";
				document.removeEventListener("mousemove", this.handleSliderMove);
				document.removeEventListener("touchmove", this.handleSliderMove);
				document.removeEventListener("mouseup", this.handleSliderStop);
				document.removeEventListener("touchend", this.handleSliderStop);

				this.doCallback();
			}
			setPosition(position) {
				this.slider.style.left = `calc(${position * this.pasSize}% - ${2 * Math.round(position * this.pasSize / 100)}px)`;
				this.debut = position * this.pas + this.heureDebut;
				this.fin = this.debut + this.dureeSeance;
			}

			/**********************************/
			/* Gestion de la plage horaire    */
			/**********************************/
			sizerStartGrab(event) {
				event.stopPropagation();
				this.posiXStart = (event.pageX || event.changedTouches[0].pageX);
				this.timeZoneSize = document.querySelector(".timeZone").offsetWidth;
				this.sliderSize = this.slider.offsetWidth;

				document.addEventListener("mousemove", this.handleSizerMove);
				document.addEventListener("touchmove", this.handleSizerMove);
				document.addEventListener("mouseup", this.handleSizerStop);
				document.addEventListener("touchend", this.handleSizerStop);
			}

			sizerMove(event) {
				let deltaX = (event.pageX || event.changedTouches[0].pageX) - this.posiXStart;
				let size = this.limit(
					100 * (this.sliderSize + deltaX) / this.timeZoneSize,
					0,
					100 * (this.timeZoneSize - this.slider.offsetLeft - 4) / this.timeZoneSize
				);

				this.slider.style.width = size + "%";

				let numSize = Math.round(parseInt(this.slider.style.width) / this.pasSize);
				if(numSize == 0) numSize = 1;
				this.slider.children[1].innerText = numSize * this.pas + "h";
				event.preventDefault();
			}

			sizerStopGrab(event) {
				let numSize = Math.round(parseInt(this.slider.style.width) / this.pasSize);
				if(numSize == 0) numSize = 1;
				this.slider.style.width =  `calc(${numSize * this.pasSize}% + 2px)`;
				this.slider.children[1].innerText = "";

				this.dureeSeance = this.pas * numSize;
				this.fin = this.debut + this.dureeSeance;

				document.removeEventListener("mousemove", this.handleSizerMove);
				document.removeEventListener("touchmove", this.handleSizerMove);
				document.removeEventListener("mouseup", this.handleSizerStop);
				document.removeEventListener("touchend", this.handleSizerStop);

				this.doCallback();
			}

		}

/*************************************/
/* Gestion des dates et des absences */
/*************************************/
		let creneau;
        function setDate(data){
			creneau = data;
			showAbsences();
        }

        async function setAbsence(){
			let etudiant = this.parentElement.parentElement;

			etudiant.dataset.statut = this.dataset.command;

			let reponse = await fetchData("setAbsence" + 
                "&semestre=" + semestre +
                "&matiere=" + matiere +
                "&matiereComplet=" + matiereComplet +
                "&UE=" + UE +
                "&etudiant=" + etudiant.dataset.nip +
                "&date=" + creneau.date +
                "&debut=" + creneau.debut +
                "&fin=" + creneau.fin +
                "&statut=" + this.dataset.command
            );

			if(reponse.problem) {
				etudiant.dataset.statut = "";
				message(reponse.problem);
				return;
			}

			////////////// 

			let data = dataEtudiants.absences[etudiant.dataset.nip] ??= {};
			data = data[creneau.date] ??= [];

			for(var i=0 ; i<data.length ; i++){
				if(data[i].debut == creneau.debut && data[i].fin == creneau.fin){
					data[i].statut = this.dataset.command;
					break;
				}
			}

			if(i == data.length){
				dataEtudiants.absences[etudiant.dataset.nip][creneau.date][i] = {
					UE: UE,
					debut: creneau.debut,
					fin: creneau.fin,
					matiere: matiere,
					matiereComplet: matiereComplet,
					statut: this.dataset.command
				}
				addHint(
					etudiant.querySelector(".hint"),
					creneau.debut,
					creneau.fin,
					this.dataset.command,
					"Vous-même"
				)
			}
			
        }

        function showAbsences(){
            document.querySelectorAll(".btnAbsences[data-statut]").forEach(e=>e.dataset.statut = "");
			document.querySelectorAll(".hint").forEach(e=>e.innerHTML = "");

			let posiDebut = (moduleDate.debut - moduleDate.heureDebut) / (moduleDate.heureFin - moduleDate.heureDebut) * 100;
			let tailleDuree = (moduleDate.fin - moduleDate.debut) / (moduleDate.heureFin - moduleDate.heureDebut) * 100;

			document.querySelectorAll(".hintCreneau").forEach(e=>{
				e.style.left = posiDebut + "%";
				e.style.width = tailleDuree + "%";
			});

            Object.entries(dataEtudiants.absences).forEach(([etudiant, datesAbsences])=>{
                datesAbsences[creneau.date]?.forEach( absenceJour=>{
					
					let ligne = document.querySelector(`[data-nip="${etudiant}"]`);

					if(absenceJour.debut == creneau.debut 
						&& absenceJour.fin == creneau.fin ) {
						ligne.dataset.statut = absenceJour.statut;
					}

					addHint(
						ligne.querySelector(".hint"),
						absenceJour.debut,
						absenceJour.fin,
						absenceJour.statut,
						absenceJour.enseignant
					)

                })
            })
        }

		function addHint(target, debut, fin, statut, enseignant){
			let posiDebut = (debut - moduleDate.heureDebut) / (moduleDate.heureFin - moduleDate.heureDebut) * 100;
			let tailleDuree = (fin - debut) / (moduleDate.heureFin - moduleDate.heureDebut) * 100;
					
			target.innerHTML += `<div style="left:${posiDebut}%;width:${tailleDuree}%" data-statut="${statut}" title="${enseignant}"></div>`;
		}

        function message(msg){
            var div = document.createElement("div");
            div.className = "message";
            div.innerHTML = msg;
            document.querySelector("body").appendChild(div);
            setTimeout(()=>{
                div.remove();
            }, 3000);
        }

/***************************/
/* C'est parti !
/***************************/
        checkStatut();
    </script>
    <?php 
        include "$path/config/analytics.php";
    ?>
</body>
</html>