<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mises à jours</title>
    <style>
        body{
            margin:0;
            font-family:arial;
            background: #FAFAFA;
        }
        h1{
            position:sticky;
            margin: 0;
            top:0;
            padding:10px;
            background:#09C;
            color:#FFF;
            box-shadow: 0 2px 2px #888;
        }

        h2{
            background: #9C0;
            display: table;
            padding: 10px;
            color: #FFF;
        }

        main{
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>Historique des mises à jours</h1>

    <main>
        <h2>01/01/2020 - V3.0.0</h2>
        <p>
            Mise à jour majeure, refonte du système back-end pour un fonctionnement sous forme de services.
            <ul>
                <li>Améliorations et robustification du code.</li>
                <li>Améliorations et robustification du système d'authentification.</li>
                <li>Architecture du code plus modulable pour une adaptation à d'autres universités.</li>
                <li>Architecture sous forme de services.</li>
                <li>Mise en place d'un système d'authentification par jeton JWT.</li>
                <li>Possibilité d'accéder aux services et données pour d'autres applications grâce au jeton JWT.</li>
                <li>Communication avec le serveur 100% JSON.</li>
                <li>Application des principes REST.</li>
                <li>Création d'une documentation pour les services. Améliorations des commentaires.</li>
                <li>Optimisation des performances : gzip requêtes, réduction du nombre de requêtes envoyées aux services (une à la place de trois) et du serveur à Scodoc.</li>
                <li>Le site est désormais une PWA complètement valide - fonctionnement hors ligne avec message.</li>
                <li>Ajout d'un "splash screen" lors de l'authentification.</li>
                <li>Correction bug double requête au clique sur le semestre.</li>
                <li>Ajout d'un système de génération automatique des listes étudiantes en fonction de groupes.</li>
                <li>Génération automatique des fichiers Excel pour les listes d'émargements, les groupes d'étudiants, le retour des notes, les données des étudiants.</li>
                <li>Ajout de l'identification des vacataires pour le département MMI.</li>
            </ul>
        </p>
        <h2>15/10/2020 - V2.1.1</h2>
        <p>
            <ul>
                <li>Amélioration de la détection d'erreurs (ajout du cas où le NIP est erroné et de la non autorisation de l'export des notes dans la configuration du semestre).</li>
            </ul>
        </p>

        <h2>28/09/2020 - V2.1.0</h2>
        <p>
            <ul>
                <li>Mise en place d'un système pour masquer / afficher les évaluations sans note.</li>
            </ul>
        </p>

        <h2>10/09/2020 - V2.0.1</h2>
        <p>
            <ul>
                <li>Correction d'un bug affichant un statut de réussite semi vide pour les étudiants en cours de cursus.</li>
                <li>Ajout de cette page listant les mises à jours.</li>
            </ul>
        </p>

        <h2>01/09/2020 - V2.0.0</h2>
        <p>
            Mise à jour majeure, refonte du système de récupération de notes :
            <ul>
                <li>automatisation du choix du département,</li>
                <li>relevé en version HTML / CSS (version PDF toujours disponible),</li>
                <li>possibilité de choisir le semestre,</li>
                <li>possibilité pour les étudiants de pointer les évaluations (on ne peut pas voir en mode enseignant),</li>
                <li>lors de la connexion, scroll automatique vers les nouvelles évaluations,</li>
                <li>possibilité d'installer l'appli sur smartphone pour y avoir accès via une icône.</li>
            </ul>
        </p>

        <h2>01/09/2019 - V1.0.0</h2>
        <p>
        Mise en ligne du premier système de récupération de relevés notes :
        <ul>
                <li>connexion au CAS de l'UHA,</li>
                <li>lien avec un listing LDAP pour identifier les étudiants à partir de leur mail,</li>
                <li>mise en place d'un serveur passerelle entre le serveur Scodoc et l'extérieur,</li>
                <li>mise en place d'un certificat SSL,</li>
                <li>création de comptes en lecture spécifique à chaque département.</li>
            </ul>
        </p>
    </main>
    <?php 
        $path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
        include "$path/includes/analytics.php";
    ?>
</body>
</html>
