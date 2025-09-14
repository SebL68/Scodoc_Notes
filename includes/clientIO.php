<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/default_config.php";

  /************************************************/
  /* Définition des constantes JS liées au statut */
  /************************************************/

  foreach($CONSTANTES as $const => $val) {
    echo "const $const = $val;";
  }
?>
/*********************************************/
/* Fonction de communication avec le serveur
Gère la déconnexion et les messages d'erreur
/*********************************************/
let config;
function fetchData(query){
	document.querySelector(".wait").style.display = "flex";
	let token = (window.location.search.match(/token=([a-zA-Z0-9._-]+)/)?.[1] || ""); // Récupération d'un token GET pour le passer au service

	return fetch(
		"/services/data.php?q="+query, 
		{
			method: "post",
			headers: {
				"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
				"Authorization": token ? "Bearer " + token : ""
			}
		}
	)
	.then(res => { return res.json() })
	.then(function(data) {
		document.querySelector(".wait").style.display = "none";
		if(data.redirect){
			// Utilisateur non authentifié, redirection vers une page d'authentification pour le CAS.
			// Passage de l'URL courant au CAS pour redirection après authentification
			window.location.href = data.redirect + "?href="+encodeURIComponent(window.location.href);
		}
		if(data.erreur){
			// Il y a une erreur pour la récupération des données - affichage d'un message explicatif.
			displayError(data.erreur);
		}else{
			if(data.config) {
				displayFromOptions(data.config);
			}
			return data;
		}
	})
	.catch(error => {
		document.querySelector(".wait").style.display = "none";
		displayError("Une erreur s'est produite lors du transfert des données.");
		throw 'Fin du script - données invalides';
	})
}

function displayError(message){
	let auth = document.querySelector(".auth");
	auth.style.opacity = "1";
	auth.style.pointerEvents = "initial";
	auth.innerHTML = message;
	auth.addEventListener("click", ()=>{
		auth.style.opacity = "0";
		auth.style.pointerEvents = "none";
	}, { once: true })
}

function displayFromOptions(options){
	config = options;
	document.querySelector(".nom").innerText = config.name;

	if(config.statut >= ETUDIANT) document.querySelector("body").classList.add('etudiant');
	if(config.statut >= PERSONNEL) document.querySelector("body").classList.add('personnel');
	if(config.statut >= ADMINISTRATEUR) document.querySelector("body").classList.add('admin');
	if(config.statut >= SUPERADMINISTRATEUR) document.querySelector("body").classList.add('superadmin');

	if(config.module_absences) document.querySelector("body").classList.add('moduleAbsences');
}

<?php 
	if($Config->multi_scodoc) {
?>
// Changement de composante
document.querySelector("header select").addEventListener("change", function() {
	window.localStorage.composante = this.value;
	document.cookie = "composante=" + window.localStorage.composante + "; path=/";
	document.querySelector("header").classList.remove("ouvert", "selectComposante");
	window.location.reload();
})

if(window.localStorage.composante) {
	document.querySelector("header select").value = window.localStorage.composante;
	document.cookie = "composante=" + window.localStorage.composante + "; path=/";
} else {
	document.querySelector("header").classList.add("ouvert", "selectComposante");
	displayError("Veuillez choisir une composante.");
	throw "Fin du script - composante à définir";
}
<?php } ?>