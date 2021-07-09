<?php
$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
/* Debug */
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

/************************************/
/* estAdministrateur
    Vérifie si un utilisateur est administrateur d'un département
    
    Entrée :
        $dep (string) : département
        $utilisateur (string) : email à vérifier

    Retour :
	    [bool] TRUE si $utilisateur est administrateur de $dep, FALSE sinon
*/
/************************************/
function estAdministrateur($dep, $utilisateur)
{
    global $path;

    $file = "$path/LDAP/administrateurs.json";

    $json = json_decode(file_get_contents($file));
    if (!isset($json->$dep))
        return false;
        
    if (in_array($utilisateur, $json->$dep))
        return true;
    else
        return false;
}

/************************************/
/* listeAdministrateurs
    Créé le fichier "administrateurs.json" s'il n'existe pas
    Ajoute la liste d'un département si elle n'existe pas
    Récupère la liste des administrateurs d'un département

    Retour :
	    [array] liste des administrateurs
*/
/************************************/
function listeAdministrateurs($dep)
{
    global $path;

    $file = "$path/LDAP/administrateurs.json";
    if (!file_exists($file)) {
        touch($file);

        $json = [
            $dep => []
        ];

        file_put_contents(
            $file,
            json_encode($json) //, JSON_PRETTY_PRINT)
        );
    }

    $json = json_decode(file_get_contents($file));
    if (!isset($json->$dep)) {
        $json->$dep = [];

        file_put_contents(
            $file,
            json_encode($json) //, JSON_PRETTY_PRINT)
        );
    }

    return $json->$dep;
}

/************************************/
/* modifAdministrateurs
    Enregistre un nouveau administrateur ou modifie un administrateur existant dans un département
 	
    Entrée :
        $ancien (string) : vide pour ajouter un nouveau administrateur ou ancien email d'un administrateur à modifier
        $nouveau (string) : email du nouveau administrateur ou nouvel email en cas de modification
*/
/************************************/
function modifAdministrateur($dep, $ancien, $nouveau)
{
    global $path;

    $file = "$path/LDAP/administrateurs.json";

    $json = json_decode(file_get_contents($file));
    $adm = $json->$dep;
    if (!empty($ancien) && in_array($ancien, $adm) === FALSE)
        return ['result' => "Erreur : Administrateur $ancien inconnu dans le département $dep"];
    if (in_array($nouveau, $adm) === TRUE)
        return ['result' => "Erreur : Administrateur $nouveau déjà enregistré dans le département $dep"];

    if (empty($ancien))        // Enregistrement d'un nouveau administrateur
        array_push($adm, $nouveau);
    else                      // Modification d'un administrateur existant
        $adm[array_search($ancien, $adm)] = $nouveau;
    usort($adm, "tri");
    $json->$dep = array_values($adm);

    file_put_contents(
        $file,
        json_encode($json) //, JSON_PRETTY_PRINT)
    );

    return ['result' => "OK"];
}

/************************************/
/* supAdministrateurs
    Supprime un administrateur dans un département
 	
    Entrée :
        $email (string) : email de l'administrateur à supprimer
*/
/************************************/
function supAdministrateur($dep, $email)
{
    global $path;

    $file = "$path/LDAP/administrateurs.json";

    $json = json_decode(file_get_contents($file));
    $adm = $json->$dep;
    if (array_search($email, $adm) === FALSE)
        return ['result' => "Erreur : Administrateur $ancien inconnu dans le département $dep"];

    unset($adm[array_search($email, $adm)]);
    $json->$dep = array_values($adm);

    file_put_contents(
        $file,
        json_encode($json) //, JSON_PRETTY_PRINT)
    );

    return ['result' => "OK"];
}

/************************************/
/* listeVacataires
    Créé le fichier "vacataires.json" s'il n'existe pas
    Ajoute la liste d'un département si elle n'existe pas
    Récupère la liste des vacataires d'un département

    Retour :
	    [array] liste des vacataires
*/
/************************************/
function listeVacataires($dep)
{
    global $path;

    $file = "$path/LDAP/vacataires.json";
    if (!file_exists($file)) {
        touch($file);

        $json = [
            $dep => [
                "vacataires" => []
            ]
        ];

        file_put_contents(
            $file,
            json_encode($json) //, JSON_PRETTY_PRINT)
        );
    }

    $json = json_decode(file_get_contents($file));
    if (!isset($json->$dep)) {
        $json->$dep = (object)["vacataires" => []];

        file_put_contents(
            $file,
            json_encode($json) //, JSON_PRETTY_PRINT)
        );
    }

    return $json->$dep->vacataires;
}

/************************************/
/* modifVacataires
    Enregistre un nouveau vacataire ou modifie un vacataire existant dans un département
 	
    Entrée :
        $ancien (string) : vide pour ajouter un nouveau vacataire ou ancien email d'un vacataire à modifier
        $nouveau (string) : email du nouveau vacataire ou nouvel email en cas de modification
*/
/************************************/
function modifVacataire($dep, $ancien, $nouveau)
{
    global $path;

    $file = "$path/LDAP/vacataires.json";

    $json = json_decode(file_get_contents($file));
    $vac = $json->$dep->vacataires;
    if (!empty($ancien) && in_array($ancien, $vac) === FALSE)
        return ['result' => "Erreur : Vacataire $ancien inconnu dans le département $dep"];
    if (in_array($nouveau, $vac) === TRUE)
        return ['result' => "Erreur : Vacataire $nouveau déjà enregistré dans le département $dep"];

    if (empty($ancien))        // Enregistrement d'un nouveau vacataire
        array_push($vac, $nouveau);
    else                      // Modification d'un vacataire existant
        $vac[array_search($ancien, $vac)] = $nouveau;
    usort($vac, "tri");
    $json->$dep->vacataires = array_values($vac);

    file_put_contents(
        $file,
        json_encode($json) //, JSON_PRETTY_PRINT)
    );

    return ['result' => "OK"];
}

/************************************/
/* supVacataires
    Supprime un vacataire dans un département
 	
    Entrée :
        $email (string) : email du vacatire à supprimer
*/
/************************************/
function supVacataire($dep, $email)
{
    global $path;

    $file = "$path/LDAP/vacataires.json";

    $json = json_decode(file_get_contents($file));
    $vac = $json->$dep->vacataires;
    if (array_search($email, $vac) === FALSE)
        return ['result' => "Erreur : Vacataire $ancien inconnu dans le département $dep"];

    unset($vac[array_search($email, $vac)]);
    $json->$dep->vacataires = array_values($vac);

    file_put_contents(
        $file,
        json_encode($json) //, JSON_PRETTY_PRINT)
    );

    return ['result' => "OK"];
}

/************************************/
/* tri
    Compare les noms et prénoms de deux emails pour les classer par ordre alphabétique
 	
    Entrée :
        $a, $b (string) : les deux emails à comparer
    Sortie :
        1 : si $a doit être classé après $b
        -1 : si $b doit être classé après $a
*/
/************************************/
function tri($a, $b)
{
    $taba = explode(".", $a);
    $tabb = explode(".", $b);
    if ($taba[1] > $tabb[1]) return 1;
    if ($taba[1] < $tabb[1]) return -1;
    if ($taba[0] > $tabb[0]) return 1;
    return -1;
}
