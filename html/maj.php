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
		.txt-barre{
			text-decoration:line-through;
		}
	</style>
</head>
<body>
	<h1>Historique des mises à jours</h1>

	<main>
		<h2>17/03/2022 - V4.7.10</h2>
		<ul>
			<li>Correction bug : prise en compte du non export des ECTS.</li>
		</ul>
		<h2>15/03/2022 - V4.7.9</h2>
		<ul>
			<li>Correction bug : prise en compte des options d'affichage Scodoc pour les relevés BUT.</li>
		</ul>
		<h2>11/03/2022 - V4.7.8</h2>
		<ul>
			<li>Changement de l'indication textuelle pour les justifications d'absences et ainsi coller au code du travail - avant, 48h après le retour pour justifier - maintenant, 48h après le début de l'absence pour justifier.</li>
		</ul>
		<h2>04/03/2022 - V4.7.7</h2>
		<div>!!! <b>Attention</b>, pour cette mise à jour, si vous utilisez le LDAP, il faut vérifier que les variables $LDAP_idCAS et $LDAP_autocompletion soient conformes à votre configuration.<br>Par défaut, leur valeur est à 'mail'.</div>
		<ul>
			<li>LDAP : ajout d'une option pour désactiver le TLS.</li>
			<li>LDAP : amélioration du nom d'une variable de configuration : $LDAP_mail -> $LDAP_idCAS.</li>
			<li>LDAP : possibilité de choisir un champ LDAP différent que l'idCAS pour l'autocomplétion - attention, ne pas modifier si $CAS_return_type != 'nip' - pour plus d'info, voir dans le fichier config.php</li>
		</ul>
		<h2>15/02/2022 - V4.7.6</h2>
		<ul>
			<li>Relevé BUT : amélioration des espacements en mode mobile.</li>
			<li>Relevé BUT : affichage du rang UE.</li>
			<li>Relevé BUT : prise en compte de l'option "ne pas afficher le rang".</li>
		</ul>
		<h2>14/02/2022 - V4.7.5</h2>
		<ul>
			<li>Correction bug : les absences étudiants ne s'affichaient plus pour les personnels.</li>
		</ul>
		<h2>14/02/2022 - V4.7.4</h2>
		<ul>
			<li>Correction faille critique : getStatut modifie le statut de l'utilisateur.</li>
		</ul>
		<h2>14/02/2022 - V4.7.3</h2>
		<ul>
			<li>Correction bug choix semestre étudiant.</li>
		</ul>
		<h2>03/02/2022 - V4.7.2</h2>
		<ul>
			<li>Correction bug téléchargement relevé version PDF.</li>
		</ul>
		<h2>02/02/2022 - V4.7.1</h2>
		<ul>
			<li>Ajout des informations identité de l'étudiant sur les relevés DUT.</li>
			<li>Amélioration du relevé DUT.</li>
		</ul>
		<h2>02/02/2022 - V4.7.0</h2>
		<ul>
			<li>Passage de paramètres aux fonctions par rapport au NIP et plus par rapport à l'idCAS.</li>
			<li>Gestion de l'autocomplétion à partir du NIP + idCAS.</li>
			<li>Au clic sur un étudiant d'une liste : affichage du relevé à partir du NIP.</li>
		</ul>
		<h2>02/02/2022 - V4.6.7</h2>
		<ul>
			<li>Correction de bugs et amélioration du code.</li>
		</ul>
		<h2>28/01/2022 - V4.6.6</h2>
		<ul>
			<li>Relevés BUT : affichage des bonus dans une UE.</li>
			<li>Relevés BUT : correction de l'affichage des absences : ajout du total semestre des absences.</li>
			<li>Possibilité de modifier, à partir du fichier config, les photos renvoyées par l'API => function customPic()</li>
			<li>Possibilité de modifier, à partir du fichier config, les data générés par l'API avant l'envoie => function customOutput()</li>
			<li>Ajout d'une zone "custom", remplie au choix de chaque IUT, depuis le fichier config => voir function customOutput().</li>
		</ul>
		<h2>26/01/2022 - V4.6.5</h2>
		<ul>
			<li>Prise en charge de l'option Scodoc : ne pas publier les relevés sur la passerelle.</li>
			<li>Relevés BUT : message par défaut sur nom de l'évaluation non défini.</li>
		</ul>
		<h2>24/01/2022 - V4.6.4</h2>
		<ul>
			<li>Update CAS : mise en minucule automatique des mails renvoyés par CAS.</li>
			<li>Config : option pour ne pas autoriser le téléchargement PDF des relevés.</li>
		</ul>
		<h2>21/01/2022 - V4.6.3</h2>
		<ul>
			<li>Correction bug : concaténation prénom / nom dans les listes étudiants.</li>
			<li>Correction bug : filtrage des groupes dans les listes étudiants.</li>
			<li>Correction bug : données vides dans le téléchargement des fichiers XLSX dans les listes d'étudiants.</li>
			<li>Correction bug : téléchargement des fichiers XLSX sans extension.</li>
		</ul>
		<h2>19/01/2022 - V4.6.2</h2>
		<ul>
			<li>Mise en place d'un fichier CSS dans /config pour personnaliser localement le style des relevés.</li>
			<li>Liste des départements générés automatiquement depuis Scodoc (et plus dans le fichier config) en utilisant la nouvelle API.</li>
			<li>Lors du clic sur un étudiant dans le trombinoscope ou sur les listes : affichage de son relevé de notes.</li>
			<li>Modification du filtrage LDAP : possibilité de ne pas filtrer par UFR - contribution : Marc Leforestier (Bordeaux)</li>
			<li>Correction : commandes CLI updates listes non fonctionnelles avec la nouvelle config</li>
		</ul>

		<h2>19/01/2022 - V4.x.x</h2>
		<ul>
			<li>Reprise des notes de version dans le fichier de mises à jours.</li>
			<li>Refonte complète du système côté serveur : passage du code en POO, réorganisation des fichiers, etc.</li>
			<li>Mise en place d'un système de gestion des absences.</li>
			<li>Mise en place d'un système de gestion des comptes.</li>
			<li>Mise en place d'une communication avec le LDAP pour récupérer statut.</li>
			<li>Prise en charge automatique des relevés DUT et BUT.</li>
			<li>Lise en place d'un système de gestion des photos étudiants : l'étudiant gère sa propre photo. Les photos serveur pour les trombinoscopes et les absences.</li>
			<li>Mise en place d'un système pour un versionnage du fichier de configuration et d'une configuration par défaut.</li>
		</ul>

		<h2>04/01/2021 - V3.0.1</h2>
		<ul>
			<li class=txt-barre>Création d'un <a target=_blank href="https://notes.iutmulhouse.uha.fr/?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzZXNzaW9uIjoiQ29tcHRlX0RlbW8udGVzdEB1aGEuZnIiLCJzdGF0dXQiOiJldHVkaWFudCJ9.kHuiNx8X2mWUjv1LAHVOdcLGCu2yQS_i6fxqZZICuEA" >compte démo</a></li>
			<li>Mise en ligne du code source sur <a target=_blank href="https://github.com/SebL68/Scodoc_Notes">GitHub</a></li>
		</ul>
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
		include "$path/config/analytics.php";
	?>
</body>
</html>
