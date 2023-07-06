<?php
$APIRootPath = 'https://notes.iutmulhouse.uha.fr/';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="prism/prism.css">
	<style>
		body {
			font-family: arial;
			margin: 0;
			background: var(--fond);
		}

		section {
			margin: 10px auto 10px auto;
			padding: 20px;
			border-radius: 20px;
			background: var(--fond-clair);
			box-shadow: var(--box-shadow);
			max-width: 1000px;
		}

		h1 {
			position: sticky;
			top: 0;
			padding: 10px;
			margin: 0;
			background: var(--primaire);
			color: var(--contenu-inverse);
			box-shadow: var(--box-shadow);
			z-index: 1;
		}

		h2 {
			margin: 0;
			padding: 20px;
			background: var(--secondaire);
			color: var(--secondaire-contenu);
			border-radius: 10px;
			cursor: pointer;
		}

		.show~* {
			display: block;
		}

		h3 {
			display: table;
			background: var(--gris);
			padding: 5px;
			padding-left: 60px;
			color: var(----contenu-inverse);
		}

		h2~* {
			display: none;
		}

		summary {
			cursor: pointer;
		}
	</style>
</head>

<body contenteditable=//true spellcheck=//true>
	<h1>Documentation générale des API proposées sur "notes"</h1>
	<!----------------------------------------->
	<!-- Introduction -->
	<!----------------------------------------->
	<section>
		<h2>Introduction</h2>
		<p>
			Pour des raisons de sécurité, le serveur Scodoc qui gère les notes n'est pas accessible en ligne, il est
			nécessaire d'être sur une liste blanche de postes depuis l'université ou être un utilisateur autorisé en
			utilisant le VPN de l'université.
		</p>
		<p>
			Le serveur "notes" est une passerelle sécurisé entre Scodoc et Internet. Ce serveur propose une interface
			HTML / CSS aux étudiants leurs permettant de consulter leurs notes et moyennes. Les enseignants peuvent
			consulter les notes d'un étudiant choisi.
		</p>
		<p>
			Pour des raisons de sécurité, le lien entre Scodoc et le serveur "notes" est en lecture seule.
		</p>
		<p>
			L'interface HTML / CSS utilise différents services proposés sur le serveur "notes". Ces services sont
			accessibles via une API REST.
		</p>
		<div>
			<h3>Communication</h3>
			<p>
				L'accès à l'API se fait via des requêtes GET :
			</p>
			<pre><code class="language-js">https://urlDuServeur.fr/services/data.php?q={requête}&autresParamètres</code></pre>
			<p>
				q étant la requête.<br>
				En fonction de cette requête, d'autres paramètres complémentaires peuvent être transmis.
			</p>
			<p>
				Pour la suite, l'URL du serveur sera https://notes.iutmulhouse.uha.fr
			</p>
			<p>
				Il est possible d'utiliser fetch en JS ou via du CURL en PHP par exemple.<br>
				L'ensemble des réponses se fait au format JSON.
			</p>
			<pre><code class="language-js">/**************************/
/* Exemple de traitement de la communication */
/**************************/
function fetchData(query){
	return fetch("services/data.php?q="+query, {"method": "post"})
	.then(res => { return res.json() })
	.then(function(data) {
		if(data.redirect){
			// Utilisateur non authentifié, redirection vers une page d'authentification pour le CAS.
			// Passage de l'URL courant au CAS pour redirection après authentification.
			// Voir la section "Authentification aux services".
			window.location.href = data.redirect + "?href="+encodeURIComponent(window.location.href); 
		}
		if(data.erreur){
			// Il y a une erreur pour la récupération des données - affichage d'un message explicatif.
			document.querySelector(".zoneErreur").innerHTML = data.erreur;
		}else{
			return data;
		}
	})
}</code></pre>
		</div>

		<div>
			<h3>Gestion des erreurs</h3>
			<p>
				En cas d'erreur, la réponse sera :
			</p>
			<pre><code class="language-js">{
	"erreur": "Message d'erreur"
}</code></pre>
			<p>
				Ces messages d'erreurs comportent des indications sur comment résoudre le problème.<br>
				Il peut par exemple y avoir un problème d'import du numéro d'étudiant dans Scodoc, il faut supprimer le
				.0 qui se trouve à la fin.
			</p>

		</div>

		<div>
			<h3>Utilisation d'un jeton JWT</h3>
			<p>
				Un jeton JWT peut être passé en paramètre POST 'token' pour autoriser l'accès aux services. Ce jeton est
				à transmettre à chaque requête lors de la communication entre serveurs (et uniquement dans ce cas).<br>
				Voir la section "Authentification aux services".
			</p>
		</div>
	</section>
	<!----------------------------------------->
	<!-- Authentification -->
	<!----------------------------------------->
	<section>
		<h2>API : Authentification aux services</h2>
		<p>
			Permet de vérifier et le cas échéant de s'authentifier au CAS de l'UHA.<br>
			Il y a deux manières de s'authentifier, soit avec le CAS UHA, soit avec un jeton JWT, ce jeton est à générer
			au préalable par l'administrateur.
		</p>
		<p>
			L'utilisation du CAS est à privilégier pour une interface HTML / CSS (uniquement sur le serveur où
			l'authentification se fait).<br>
			Le jeton JWT est à utiliser lors d'une communication inter-serveur en le passant en paramètre POST
			'token'.<br>
			Il est également possible d'utiliser le jeton pour montrer le service à une personne n'étant pas dans le CAS
			- il faut de manière générale éviter d'utiliser cette méthode dans une fonctionnement récurrent et si c'est
			le cas définir une durée de vie de jeton courte.
		</p>
		<p>
			<b>!!! Attention, ce jeton est personnel et permet d'accéder aux services. Il ne doit en aucun cas être
				public !!!</b>
		</p>
		<div>
			<h3>Query</h3>
			<pre><code>donnéesAuthentification</code></pre>
			<h3>Exemple</h3>
			<pre><code><?php echo $APIRootPath; ?>services/data.php?q=donnéesAuthentification</code></pre>
			<h3>Entrée</h3>
			Pas de paramètre d'entrée.
			<h3>Sortie</h3>
			<pre><code class="language-js">{
	"redirect": "URL vers le CAS"
}</code></pre>
			<p><b>ou</b></p>
			<pre><code class="language-js">{
	"session": "mail de la personne",
	"statut" : "etudiant" | "personnel" | "none" // none étant identifié à l'UHA mais inconnu à l'IUT
}</code></pre>
		</div>
	</section>
	<!----------------------------------------->
	<!-- Liste étudiants LDAP IUT -->
	<!----------------------------------------->
	<section>
		<h2>API : Liste étudiants de l'IUT</h2>
		<p>Cette ressource est limitée aux utilisateurs avec un statut "personnel".</p>
		<p>Récupère la liste de tous les étudiants inscrits dans le LDAP pour l'IUT.</p>
		<p>Pour le moment, cette liste est mise à jour manuellement - si un étudiant n'y est pas listé, veuillez
			contacter l'administrateur pour une mise à jour et vérification.</p>
		<div>
			<h3>Query</h3>
			<pre><code>listeEtudiants</code></pre>
			<h3>Exemple</h3>
			<pre><code><?php echo $APIRootPath; ?>services/data.php?q=listeEtudiants</code></pre>
			<h3>Entrée</h3>
			Pas de paramètre d'entrée.
			<h3>Sortie</h3>
			<pre><code class="language-js">[
	"etudiant1@uha.fr",
	"etudiant2@uha.fr", 
	etc.
]</code></pre>
		</div>
	</section>
	<!----------------------------------------->
	<!-- Liste des semestres étudiants -->
	<!----------------------------------------->
	<section>
		<h2>API : Liste des semestres d'un étudiant</h2>
		<p>Liste les identifiants de semestres qu'un étudiant a suivi.</p>
		<p>Si l'utilisateur est un "personnel", il peut demander les semestres de n'importe quel étudiant.</p>
		<div>
			<h3>Query</h3>
			<pre><code>semestresEtudiant</code></pre>
			<h3>Exemples</h3>
			<pre><code><?php echo $APIRootPath; ?>services/data.php?q=semestresEtudiant&etudiant=alexandre.aab@uha.fr
<?php echo $APIRootPath; ?>services/data.php?q=semestresEtudiant</code></pre>
			<h3>Entrée</h3>
			// Ce paramètre n'est utilisable que si l'utilisateur est reconnu comme "personnel".<br>
			[Optionnel] etudiant : identifiant de l'étudiant (adresse mail) sinon, l'adresse de session est
			automatiquement utilisé.
			<h3>Sortie</h3>
			<pre><code class="language-js">// Identifiant Scodoc des semestres - utile pour d'autres requêtes.
// Par ordre chonologique.
[
	"SEM8871", 
	"SEM8833", 
	etc.
]</code></pre>
		</div>
	</section>
	<!----------------------------------------->
	<!-- Relevé de notes d'un étudiant -->
	<!----------------------------------------->
	<section>
		<h2>API : Relevé de notes d'un étudiant</h2>
		<p>Renvoie les données du relevé de notes d'un étudiant</p>
		<p>Si l'utilisateur est un "personnel", il peut demander les semestres de n'importe quel étudiant.</p>
		<div>
			<h3>Query</h3>
			<pre><code>relevéEtudiant</code></pre>
			<h3>Exemples</h3>
			<pre><code><?php echo $APIRootPath; ?>services/data.php?q=relevéEtudiant&semestre=SEM8871&etudiant=alexandre.aab@uha.fr
<?php echo $APIRootPath; ?>services/data.php?q=relevéEtudiant&semestre=SEM8871</code></pre>
			<h3>Entrée</h3>
			semestre : identifiant Scodoc du semestre
			// Ce paramètre n'est utilisable que si l'utilisateur est reconnu comme "personnel".<br>
			[Optionnel] etudiant : identifiant de l'étudiant (adresse mail) sinon, l'adresse de session est
			automatiquement utilisé.
			<h3>Sortie</h3>
			<pre><code class="language-js">JSON avec les données.</code></pre>
		</div>
	</section>
	<!----------------------------------------->
	<!-- Données de première connexion -->
	<!----------------------------------------->
	<section>
		<h2>API : Données de première connexion</h2>
		<p>Cette requête est une combinaison de l'authentification, la liste étudiant, la liste des semestre et du
			relevé de notes.</p>
		<p>Elle permet d'avoir toutes les données en une seule requête pour plus de performances. De plus, le nombre de
			requêtes à Scodoc est limité grâce à une réutilisation en interne des données.</p>
		<div>
			<h3>Query</h3>
			<pre><code>dataPremièreConnexion</code></pre>
			<h3>Exemples</h3>
			<pre><code><?php echo $APIRootPath; ?>services/data.php?q=dataPremièreConnexion</code></pre>
			<h3>Entrée</h3>
			Pas de paramètre d'entrée.
			<h3>Sortie</h3>
			<pre><code class="language-js">// Si personne identifié comme "personnel"
[
	"auth": Données d'authentification,
	"etudiants": Liste des étudiants
]
</code></pre>
			<b>ou</b>
			<pre><code class="language-js">// Si personne identifié comme "etudiant"
[
	"auth": Données d'authentification (voir API : Authentification aux services),
	"semestres": Liste des semestres (voir API : Liste des semestres d'un étudiant),
	"relevé": JSON du relevé du dernier semestre suivi par l'étudiant
]
</code></pre>
		</div>
	</section>
	<!----------------------------------------->
	<!-- Liste des semestres d'un département -->
	<!----------------------------------------->
	<section>
		<h2>API : Semestres actifs département</h2>
		<p>Liste des semestres actif d'un département.</p>
		<p>Réservé aux utilisateurs avec un statut de "personnel".</p>
		<div>
			<h3>Query</h3>
			<pre><code>semestresDépartement</code></pre>
			<h3>Exemples</h3>
			<pre><code><?php echo $APIRootPath; ?>services/data.php?q=semestresDépartement&dep=MMI</code></pre>
			<h3>Entrée</h3>
			dep: département
			<h3>Sortie</h3>
			<pre><code class="language-js">[
	{
		"titre": "titre du semestre",
		"semestre_id": "code semestre" // exemple : 'SEM8871'
	},
	etc.
]</code></pre>
		</div>
	</section>
	<!----------------------------------------->
	<!-- Liste les étudiants d'un département -->
	<!----------------------------------------->
	<section>
		<h2>API : Etudiants d'un département</h2>
		<p>Liste les étudiants d'un département par semestre et groupes.</p>
		<p>Réservé aux utilisateurs avec un statut de "personnel".</p>
		<div>
			<h3>Query</h3>
			<pre><code>listesEtudiantsDépartement</code></pre>
			<h3>Exemples</h3>
			<pre><code><?php echo $APIRootPath; ?>services/data.php?q=listesEtudiantsDépartement&dep=MMI</code></pre>
			<h3>Entrée</h3>
			dep: département
			<h3>Sortie</h3>
			<pre><code class="language-js">[
	{
		"titre": "Nom du semestre",
		"semestre_id": "SEM8732",
		"groupes": ["groupe 1", "groupe 2", etc.], // Exemple : TP11, TP12, etc.
		"etudiants": [
			{
				"nom": "nom de l'étudiant",
				"prenom": "prenom de l'étudiant",
				"groupe": "groupe 1",
				"num_etudiant" => "numero de l'étudiant",
				"email" => "email UHA de l'étudiant"
			},
			etc.
		]
	},
	etc. avec les autres semestres d'un département, exemple : 1er année, 2ième année, LP, ...
]</code></pre>
		</div>
	</section>
	<!--
	<section>
		<h2 class=show></h2>
		<p>Blaaaa</p>
		<div>
			<h3>Query</h3>
			<pre><code>donnéesAuthentification</code></pre>
			<h3>Exemple</h3>
			<pre><code><?php echo $APIRootPath; ?>services/data.php?q=donnéesAuthentification</code></pre>
			<h3>Entrée</h3>
			Pas de paramètre d'entrée.
			<h3>Sortie</h3>
			<pre><code class="language-js">{
	"redirect": "URL vers le CAS"
}</code></pre>
		</div>
	</section>
	-->
	<script>
		document.querySelectorAll("h2").forEach(
			e => e.addEventListener(
				"click",
				function () {
					this.classList.toggle("show")
				}
			)
		)
	</script>
	<script src="prism/prism.js"></script>
	<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include "$path/config/analytics.php";
	?>
</body>

</html>