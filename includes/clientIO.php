<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	include_once "$path/includes/default_config.php";

  /************************************************/
  /* Définition des constantes JS liées au statut */
  /************************************************/

  foreach($CONSTANTES as $const => $val) {
    echo "const $const = $val;";
  }

  /**************************************************************/
  /* Définition des constantes JS liées au format des ID et Nom */
	/* des comptes utilisateurs dans la partie Administration     */
  /**************************************************************/
  echo "const IDREG = '" . $Config->idReg . "';";
  echo "const IDPH = '" . $Config->idPlaceHolder . "';";
  echo "const IDINFO = '" . $Config->idInfo . "';";
  echo "const NAMEREG = '" . $Config->nameReg . "';";
  echo "const NAMEPH = '" . $Config->namePlaceHolder . "';";
  echo "const NAMEINFO = '" . $Config->nameInfo . "';";
?>
/*********************************************/
/* Fonction de communication avec le serveur
Gère la déconnexion et les messages d'erreur
/*********************************************/
function fetchData(query){
	document.querySelector(".wait").style.display = "block";
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
}