/*********************************************/
/* Définition des constantes liées au statut
/*********************************************/
const INCONNU = 0;
const ETUDIANT = 10;
const PERSONNEL = 20;
const ADMINISTRATEUR = 30;

/*********************************************/
/* Fonction de communication avec le serveur
Gère la déconnexion et les messages d'erreur
/*********************************************/
function fetchData(query){
	document.querySelector(".wait").style.display = "block";
	let token = (window.location.search.match(/token=([a-zA-Z0-9._-]+)/)?.[1] || ""); // Récupération d'un token GET pour le passer au service
	if(token){
		var postData = new FormData();
		postData.append('token', token);
	}
	return fetch(
		"/services/data.php?q="+query, 
		{
			method: "post",
			body: token ? postData : ""
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
			return data;
		}
	})
}

function displayError(message){
	let auth = document.querySelector(".auth");
	auth.style.opacity = "1";
	auth.style.pointerEvents = "initial";
	auth.innerHTML = message;
}