<?php
    $path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
    <style>
        <?php include "$path/html/assets/header.css"?>
        main{
            margin: 0 auto 20px auto;
            text-align: center;
        }
        .contenu {
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
		@media screen and (max-width: 900px){
			.contenu{
				display: block;
			}
			#vacataires{
				margin-top: 32px;
			}
			.contenu>div>div:nth-child(1){
				background: #ddd;
			}
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
        select {
            font-size: 21px;
            padding: 10px;
            margin: 5px;
            background: #09c;
            color: #FFF;
            border: none;
            border-radius: 10px;
        }
        .highlight {
            animation: pioupiou 0.4s infinite ease-in alternate;
        }
        @keyframes pioupiou {
            0% {
                box-shadow: 0 0 4px 0px orange;
            }

            100% {
                box-shadow: 0 0 4px 2px orange;
            }
        }
        /***********************/
        /* Liste des personnes */
        /***********************/
        .administrateur, .vacataire {
            user-select: none;
            border-radius: 10px;
            box-shadow: 0 2px 2px 2px #ddd;
            border: 1px solid transparent;
            background: #FFF;
            padding: 10px 20px;
            margin: 10px;
            transition: 0.1s;
        }
        .administrateur:first-child>.nom, .administrateur svg,
        .vacataire:first-child>.nom, .vacataire svg {
            cursor: pointer;
        }
        .administrateur input, .vacataire input {
            font-size: 16px;
            font-weight: bold;
            padding: 5px;
        }
        .ready {
            /*opacity: initial;*/
            /*pointer-events: initial;*/
            background: #9FC !important;
        }
        .nom, .mail, .confirm {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nom span, .confirm b {
            text-transform: capitalize;
        }
        svg {
            margin-left: 10px;
        }
        .modif {
            background: #0C9;
        }
        .hide {
            display: none;
        }
        .show {
            display: flex;
        }
        .inline {
            display: inline;
        }
        /*********************/
        /* Affichage message */
        /*********************/
        .message {
            position: fixed;
            bottom: 100%;
            left: 50%;
            z-index: 10;
            padding: 20px;
            border-radius: 0 0 10px 10px;
            background: #ec7068;
            color: #FFF;
            font-size: 24px;
            animation: message 3s;
            transform: translate(-50%, 0);
        }
        @keyframes message {
            20% {transform: translate(-50%, 100%)}
            80% {transform: translate(-50%, 100%)}
        }
    </style>
    <meta name=description content="Gestion des administrateurs de l'IUT de Mulhouse">
</head>

<body>
    <?php 
        $h1 = 'Administration';
        include "$path/html/assets/header.php";
    ?>
    <main>
        <p>
            Bonjour <span class=prenom></span>.
        </p>

        <div class="zone">
            <select id=departement class=highlight onchange="selectDepartment(this.value)">
                <option value="" disabled selected hidden>Choisir un département</option>
                <?php
                include "$path/includes/serverIO.php";
                $listDepartement = getDepartmentsList();
                foreach ($listDepartement as $departement) {
                    echo "<option value=$departement>$departement</option>";
                }
                ?>
            </select>
        </div>

        <div class=contenu>
            <div id="administrateurs"></div>
            <div id="vacataires"></div>
        </div>
        <div class=wait></div>

    </main>

    <div class=auth>
        <!-- Site en maintenance -->
        Authentification en cours ...
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
    <script>
        var utilisateur;        // Stockage du mail de l'utilisateur
        var statut;             // Stockage du statut de l'utilisateur
        var selectDep;          // Stockage du département sélectionné
        checkStatut();
        <?php
        include "$path/includes/clientIO.php";
        ?>
        document.querySelector("#admin").classList.add("navActif");
        /***************************************************/
        /* Vérifie l'identité de la personne et son statut */
        /***************************************************/
        async function checkStatut() {
            let data = await fetchData("donnéesAuthentification");
            document.querySelector(".prenom").innerText = data.session.split(".")[0];
            utilisateur = data.session;
            let auth = document.querySelector(".auth");
            auth.style.opacity = "0";
            auth.style.pointerEvents = "none";

            if (data.statut >= ADMINISTRATEUR) {
                document.querySelector("body").classList.add('personnel');
				document.querySelector("#admin").style.display = "block";
                statut = data.statut;

                if (statut >= SUPERADMINISTRATEUR) {    // Ajout des fonctionnalités pour SuperAdministrateur
					document.querySelector("nav").innerHTML += `
						<a class="nav" href="#" onClick="exeCmd('updateLists')">UpdateLists</a>
						<a class="nav" href="#" onClick="exeCmd('setCron')">setCron</a>
					`;
                }

                /* Gestion du storage remettre le même état au retour */
                let departement = localStorage.getItem("departement");
                if (departement) {
                    document.querySelector("#departement").value = departement;
                    selectDepartment(departement);
                }

            } else {
                document.querySelector(".contenu").innerHTML = "Ce contenu est uniquement accessible pour des administrateurs d'un département de l'IUT. ";
            }
        }

        /***************************************************/
        /* Exécution de commandes pour SuperAdministrateur */
        /***************************************************/
        async function exeCmd(commande) {
            let result = await fetchData(commande);
            console.log(result);
        }

        /**************************************************/
        /* Récupère la liste des personnes du département */
        /**************************************************/
        async function selectDepartment(departement) {
            selectDep = departement;
            let administrateurs = await fetchData("listeAdministrateurs&dep=" + departement);
            let vacataires = await fetchData("listeVacataires&dep=" + departement);
            // Est-ce que l'utilisateur est un administrateur du département sélectionné ou un SuperAdministrateur?
            let isAdmin = (administrateurs.indexOf(utilisateur) >= 0) || (statut >= SUPERADMINISTRATEUR);
            
            document.querySelector(".contenu>div#administrateurs").innerHTML = createList(administrateurs, "administrateur", isAdmin);
            document.querySelector(".contenu>div#vacataires").innerHTML = createList(vacataires, "vacataire", isAdmin);
            
            document.querySelector("#departement").classList.remove("highlight");

            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('departement', departement);
        }

        /*******************************************************/
        /* Affiche une liste d'utilisateurs 
            Entrée : 
                liste [array] : Liste des utilisateurs
                type [string] : Catégorie des utilisateurs (administrateur ou vacataire) utilisé pour la classe CSS
                modeAdmin [bool]: modification de la liste possible si TRUE; uniquement affichage de la liste si FALSE
        
            Retour : 
                [string] : contenu HTML de la liste
        */
        /*******************************************************/
        function createList(liste, type, modeAdmin) {
            let dns = '@' + DNS;
            // Pour ajouter un personnel
            if (modeAdmin)
                var output = `
                        <div class="${type}" data-email="">
                            <div class="nom" onclick="modifPerson(this, '${type}')">
                                Ajouter un ${type}
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18v18H3zM12 8v8m-4-4h8"/></svg>
                            </div>
                            <div class="confirm hide"></div>
                            <div class="mail hide">
                                <input type="email" placeholder="prenom.nom" required><b>${dns}</b>
                                <svg onclick="processPerson(this, '${type}')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Valider</title><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                <svg onclick="cancel(this, '${type}')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Annuler</title><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38"/></svg>
                            </div>
                        </div>
                    `;
            else
                var output = `
                        <div class="${type}">
                            <div class="nom">
                                Liste des ${type}s
                            </div>
                        </div>
                    `;

            if(liste !== undefined)
            liste.forEach(personne => {
                let prenom = personne.split("@")[0].split(".")[0];
                let nom = personne.split("@")[0].split(".")[1];
                if (modeAdmin)
                    output += `
                        <div class="${type}" data-email="${personne}" data-nom="${nom}" data-prenom="${prenom}">
                            <div class="nom">
                                <span><b>${nom}&nbsp;${prenom}</b></span>
                                <span>
                                    <svg onclick="modifPerson(this, '${type}')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Modifier</title><polygon points="14 2 18 6 7 17 3 17 3 13 14 2"></polygon><line x1="3" y1="22" x2="21" y2="22"></line></svg>
                                    <svg onclick="deletePerson(this, '${type}')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Supprimer</title><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg>
                                </span>
                            </div>
                            <div class="confirm hide">
                                <span>Suppression de : <b>${nom}&nbsp;${prenom}</b></span>
                                <span>
                                    <svg onclick="deletePerson(this, '${type}')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Supprimer</title><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg>
                                    <svg onclick="cancel(this, '${type}')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Annuler</title><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38"/></svg>
                                </span>
                            </div>
                            <div class="mail hide">
                                <span>
                                    <input type="email" value="${prenom}.${nom}" placeholder="prénom.nom" required><b>${dns}</b>
                                </span>
                                <span>
                                    <svg onclick="processPerson(this, '${type}')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Valider</title><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                    <svg onclick="cancel(this, '${type}')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Annuler</title><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38"/></svg>
                                </span>
                            </div>
                        </div>
                    `;
                else
                    output += `
                        <div class="${type}">
                            <div class="nom">
                                <span><b>${nom}&nbsp;${prenom}</b></span>
                            </div>
                        </div>
                    `;
            });

            return output;
        }

        /*******************************/
        /* Modification d'une personne */
        /*******************************/
        function modifPerson(obj, type) {
            let personne = obj.closest("div."+type);

            document.querySelector("div.nom.hide")?.classList.remove("hide");
            document.querySelector("div.confirm.show")?.classList.remove("show");
            document.querySelector("div.mail.show")?.classList.remove("show");
            document.querySelector("div.ready")?.classList.remove("ready");
            
            personne.querySelector("div.nom").classList.add("hide");
            personne.querySelector("div.mail").classList.add("show");
            personne.querySelector("input").focus();
            personne.classList.add("ready");
        }

        /*****************************************/
        /* Traite la modification d'une personne */
        /*****************************************/
        async function processPerson(obj, type) {
            const regMail = new RegExp('^[a-z0-9_-]+[.][a-z0-9_-]+$');
            let personne = obj.closest("div."+type);
            let oldEmail = personne.getAttribute("data-email");
            let newEmail = personne.querySelector("input").value.trim().toLowerCase();
            let departement = selectDep;
            let liste = document.querySelectorAll("div."+type);

            if (!regMail.test(newEmail)) { // Si le nouveau nom n'est pas conforme
                message(`Le nom "${newEmail}" n'est pas conforme`);
                return;
            }
            newEmail += '@' + DNS;

            if (newEmail == oldEmail) { // Si pas de changement
                cancel(obj, type);
                return;
            }

            liste.forEach(per => {
                if (per.getAttribute("data-email") == newEmail) { // Si la nouvelle personne existe déjà
                    message(`"${newEmail}" est déjà enregistré dans le département`);
                    return;
                }
            });

            if (type == "administrateur") {
                let response = await fetchData("modifAdministrateur&dep=" + departement + "&ancienMail=" + oldEmail + "&nouveauMail=" + newEmail);

                if (response.result == "OK") { // Rechargement de la liste à partir du serveur
                    let liste = await fetchData("listeAdministrateurs&dep=" + departement);
                    document.querySelector(".contenu>div#administrateurs").innerHTML = createList(liste, type, true);
                } else
                    message(response.result);
            }
            if (type == "vacataire") {
                let response = await fetchData("modifVacataire&dep=" + departement + "&ancienMail=" + oldEmail + "&nouveauMail=" + newEmail);

                if (response.result == "OK") { // Rechargement de la liste à partir du serveur
                    let liste = await fetchData("listeVacataires&dep=" + departement);
                    document.querySelector(".contenu>div#vacataires").innerHTML = createList(liste, type, true);
                } else
                    message(response.result);
            }
        }

        /*********************************************/
        /* Supprime une personne, après confirmation */
        /*********************************************/
        async function deletePerson(obj, type) {
            let personne = obj.closest("div."+type);

            document.querySelector("div.nom.show")?.classList.remove("show");
            document.querySelector("div.mail.show")?.classList.remove("show");
            document.querySelector("div.ready")?.classList.remove("ready");
            personne.classList.add("ready");

            if (personne.querySelector("div.confirm").classList.contains("show")) { // Suppression de l'utilisateur confirmée
                let departement = selectDep;
                let email = personne.getAttribute("data-email");
                let response;

                if (type == "administrateur") response = await fetchData("supAdministrateur&dep=" + departement + "&email=" + email);
                if (type == "vacataire")      response = await fetchData("supVacataire&dep=" + departement + "&email=" + email);

                if (response.result != "OK") {
                    message(response.result);
                } else { // Rechargement de la liste à partir du serveur
                    if (type == "administrateur") {
                        let administrateurs = await fetchData("listeAdministrateurs&dep=" + departement);
                        document.querySelector(".contenu>div#administrateurs").innerHTML = createList(administrateurs, "administrateur", true);
                    }
                    if (type == "vacataire") {
                        let vacataires = await fetchData("listeVacataires&dep=" + departement);
                        document.querySelector(".contenu>div#vacataires").innerHTML = createList(vacataires, "vacataire", true);
                    }
                }
            } else { // Affichage de la demande de confirmation
                if ((type == "vacataire") || ((type == "administrateur") && (document.querySelectorAll("div.administrateur").length > 2))) {
                    document.querySelector("div.confirm.show")?.classList.remove("show");
                    document.querySelector("div.nom.hide")?.classList.remove("hide");

                    personne.querySelector("div.confirm").classList.add("show");
                    personne.querySelector("div.nom").classList.add("hide");
                } else 
                    message(`Vous ne pouvez pas supprimer le dernier administrateur de la liste.`);
            }
        }

        /***********************************************************/
        /* Annule la modification ou la suppression d'une personne */
        /***********************************************************/
        function cancel(obj, type) {
            let personne = obj.closest("div."+type);
            personne.querySelector("div.mail").classList.remove("show");
            personne.querySelector("div.confirm").classList.remove("show");
            personne.querySelector("div.nom").classList.remove("hide");
            if(personne.getAttribute("data-prenom") && personne.getAttribute("nom"))
                personne.querySelector("input").value = personne.getAttribute("data-prenom") + '.' + personne.getAttribute("data-nom");
            personne.classList.remove("ready");
        }

        /**************************/
        /* Affichage d'un message */
        /**************************/
        function message(msg) {
            var div = document.createElement("div");
            div.className = "message";
            div.innerHTML = msg;
            document.querySelector("body").appendChild(div);
            setTimeout(() => {
                div.remove();
            }, 3000);
        }
    </script>
    <?php
    include "$path/includes/analytics.php";
    ?>
</body>

</html>