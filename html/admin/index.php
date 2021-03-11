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
/*
    text-align: left;
    border-radius: 10px;
    box-shadow: 0 2px 2px 2px #ddd;
    border: 1px solid transparent;
    background: #FFF;
    padding: 10px 20px;
    margin: 10px;
    cursor: pointer;
    transition: 0.1s;
    display: flex;
    gap: 5px;
*/


        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: arial;
            background: #FAFAFA;
        }

        header {
            position: sticky;
            top: 0;
            padding: 10px;
            background: #09C;
            display: flex;
            justify-content: space-between;
            color: #FFF;
            box-shadow: 0 2px 2px #888;
            z-index: 1;
        }

        header>a {
            color: #FFF;
            text-decoration: none;
            padding: 10px 0 10px 0;
        }

        h1 {
            margin: 0;
        }

        h2 {
            margin: 20px 0 0 0;
            padding: 20px;
            background: #0C9;
            color: #FFF;
            border-radius: 10px;
            cursor: pointer;
        }

        main {
            padding: 0 10px;
            margin-bottom: 64px;
            max-width: 1000px;
            margin: 0 auto 20px auto;
            text-align: center;
        }

        .prenom {
            text-transform: capitalize;
            color: #f44335;
        }

        .wait {
            position: fixed;
            width: 50px;
            height: 10px;
            background: #424242;
            top: 80px;
            left: 50%;
            margin-left: -25px;
            animation: wait 0.6s ease-out alternate infinite;
        }

        @keyframes wait {
            100% {
                transform: translateY(-30px) rotate(360deg)
            }
        }

        .auth {
            position: fixed;
            top: 58px;
            left: 0;
            right: 0;
            bottom: 0;
            background: #FAFAFA;
            font-size: 28px;
            padding: 28px 10px 0 10px;
            text-align: center;
            transition: 0.4s;
        }

        .contenu {
/*            opacity: 0.5;
            pointer-events: none;
            user-select: none;  */
        }

        .flex{
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /**********************/
        /*   Zones de choix   */
        /**********************/
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

        /********************/
        /* Liste vacataires */
        /********************/
        .vacataire {
            /*opacity: 0.5;*/
            /*pointer-events: none;*/
            user-select: none;

            border-radius: 10px;
            box-shadow: 0 2px 2px 2px #ddd;
            border: 1px solid transparent;
            background: #FFF;
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
            transition: 0.1s;
        }

        .ready {
            /*opacity: initial;*/
            /*pointer-events: initial;*/
            background: #9FC;
        }

        .nom, .mail, .confirm {
            display: flex;
            justify-content: space-between;
        }

        .nom, .mail, .confirm {
            align-items: center;
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
            20% {
                transform: translate(-50%, 100%)
            }

            80% {
                transform: translate(-50%, 100%)
            }
        }
    </style>
    <meta name=description content="Gestion des vacataires de l'IUT de Mulhouse">
</head>

<body>
    <header>
        <h1>
            Administration
        </h1>
        <a href=/logout.php>Déconnexion</a>
    </header>
    <main>
        <p>
            Bonjour <span class=prenom></span>.
        </p>

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

        <div class=contenu>
            <div class=flex></div>
        </div>
        <div class=wait></div>

    </main>

    <div class=auth>
        <!-- Site en maintenance -->
        Authentification en cours ...
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
    <script>
        checkStatut();
        <?php
        include "$path/includes/clientIO.php";
        ?>
        /***************************************************/
        /* Vérifie l'identité de la personne et son statut */
        /***************************************************/
        async function checkStatut() {
            let data = await fetchData("donnéesAuthentification");
            document.querySelector(".prenom").innerText = data.session.split(".")[0];
            let auth = document.querySelector(".auth");
            auth.style.opacity = "0";
            auth.style.pointerEvents = "none";

            if (data.statut >= ADMINISTRATEUR) {

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
        /* Récupère la liste des vacataires du département */
        /***************************************************/
        async function selectDepartment(departement) {
            let vacataires = await fetchData("listeVacataires&dep=" + departement);

            document.querySelector(".flex").innerHTML = createContractors(vacataires);
            
            document.querySelector("#departement").classList.remove("highlight");

            /* Gestion du storage remettre le même état au retour */
            localStorage.setItem('departement', departement);
        }

        /**************************************************/
        /* Affiche la liste des vacataires du département */
        /**************************************************/
        function createContractors(liste) {
            let dns = '@' + DNS;
            // Pour ajouter un vacataire
            var output = `
                    <div class="vacataire" data-email="" data-nom="" data-prenom="">
                        <div class="nom" onclick="modifContractor(this)">
                            Ajouter un vacataire
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18v18H3zM12 8v8m-4-4h8"/></svg>
                        </div>
                        <div class="confirm hide"></div>
                        <div class="mail hide">
                            <input type="email" placeholder="prénom.nom" required><b>${dns}</b>
                            <svg onclick=processContractor(this) xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Valider</title><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                            <svg onclick=cancel(this) xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Annuler</title><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38"/></svg>
                        </div>
                    </div>
                `;

            liste.forEach(vacataire => {
                let prenom = vacataire.split("@")[0].split(".")[0];
                let nom = vacataire.split("@")[0].split(".")[1];
                output += `
                    <div class="vacataire" data-email="${vacataire}" data-nom="${nom}" data-prenom="${prenom}">
                        <div class="nom">
                            <span><b>${nom}&nbsp;${prenom}</b></span>
                            <span>
                                <svg onclick=modifContractor(this) xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Modifier</title><polygon points="14 2 18 6 7 17 3 17 3 13 14 2"></polygon><line x1="3" y1="22" x2="21" y2="22"></line></svg>
                                <svg onclick=deleteContractor(this) xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Supprimer</title><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg>
                            </span>
                        </div>
                        <div class="confirm hide">
                            <span>Suppression de : <b>${prenom}&nbsp;${nom}</b></span>
                            <span>
                                <svg onclick=deleteContractor(this) xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Supprimer</title><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg>
                                <svg onclick=cancel(this) xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Annuler</title><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38"/></svg>
                            </span>
                        </div>
                        <div class="mail hide">
                            <span>
                                <input type="email" value="${prenom}.${nom}" placeholder="prénom.nom" required><b>${dns}</b>
                            </span>
                            <span>
                                <svg onclick=processContractor(this) xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Valider</title><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                <svg onclick=cancel(this) xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><title>Annuler</title><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38"/></svg>
                            </span>
                        </div>
                    </div>
				`;
            });

            return '<div>'+output+'</div>';
        }

        /*******************************/
        /* Modification d'un vacataire */
        /*******************************/
        function modifContractor(obj) {
            let vac = obj.closest("div.vacataire");

            document.querySelector("div.nom.hide")?.classList.remove("hide");
            document.querySelector("div.confirm.show")?.classList.remove("show");
            document.querySelector("div.mail.show")?.classList.remove("show");
            document.querySelector("div.vacataire.ready")?.classList.remove("ready");
            
            vac.querySelector("div.nom").classList.add("hide");
            vac.querySelector("div.mail").classList.add("show");
            vac.querySelector("input").focus();
            vac.classList.add("ready");
        }

        /*****************************************/
        /* Traite la modification d'un vacataire */
        /*****************************************/
        async function processContractor(obj) {
            const regMail = new RegExp('^[a-z0-9_-]+[.][a-z0-9_-]+$');
            let vac = obj.closest("div.vacataire");
            let oldEmail = vac.getAttribute("data-email");
            let newEmail = vac.querySelector("input").value.trim().toLowerCase();
            let departement = localStorage.getItem('departement');
            let listeVacataires = document.querySelectorAll("div.vacataire");

            if (!regMail.test(newEmail)) { // Si le nouveau nom n'est pas conforme
                message(`Le nom du vacataire "${newEmail}" n'est pas conforme`);
                return;
            }
            newEmail += '@' + DNS;

            if (newEmail == oldEmail) { // Si pas de changement
                cancel(obj);
                return;
            }

            listeVacataires.forEach(vacataire => {
                if (vacataire.getAttribute("data-email") == newEmail) { // Si le nouveau vacataire existe déjà
                    message(`Le vacataire "${newEmail}" est déjà enregistré dans le département`);
                    return;
                }
            });

            let response = await fetchData("modifVacataire&dep=" + departement + "&ancienMail=" + oldEmail + "&nouveauMail=" + newEmail);

            if (response.result == "OK") { // Rechargement de la liste à partir du serveur
                let vacataires = await fetchData("listeVacataires&dep=" + departement);
                document.querySelector(".flex").innerHTML = createContractors(vacataires);
            } else
                message(response.result);
        }

        /*********************************************/
        /* Supprime un vacataire, après confirmation */
        /*********************************************/
        async function deleteContractor(obj) {
            let vac = obj.closest("div.vacataire");

            document.querySelector("div.nom.show")?.classList.remove("show");
            document.querySelector("div.mail.show")?.classList.remove("show");
            document.querySelector("div.vacataire.ready")?.classList.remove("ready");
            vac.classList.add("ready");

            if (vac.querySelector("div.confirm").classList.contains("show")) { // Suppression du vacataire confirmée
                let departement = localStorage.getItem('departement');
                let email = vac.getAttribute("data-email");

                let response = await fetchData("supVacataire&dep=" + departement + "&email=" + email);

                if (response.result != "OK") {
                    message(response.result);
                } else { // Rechargement de la liste à partir du serveur
                    let vacataires = await fetchData("listeVacataires&dep=" + departement);
                    document.querySelector(".flex").innerHTML = createContractors(vacataires);
                }
            } else { // Affichage de la demande de confirmation
                document.querySelector("div.confirm.show")?.classList.remove("show");
                document.querySelector("div.nom.hide")?.classList.remove("hide");

                vac.querySelector("div.confirm").classList.add("show");
                vac.querySelector("div.nom").classList.add("hide");
            }
        }

        /***********************************************************/
        /* Annule la modification ou la suppression d'un vacataire */
        /***********************************************************/
        function cancel(obj) {
            let vac = obj.closest("div.vacataire");
            vac.querySelector("div.mail").classList.remove("show");
            vac.querySelector("div.confirm").classList.remove("show");
            vac.querySelector("div.nom").classList.remove("hide");
            vac.querySelector("input").value = vac.getAttribute("data-prenom") + '.' + vac.getAttribute("data-nom");
            vac.classList.remove("ready");
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