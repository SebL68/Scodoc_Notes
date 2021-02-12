<?php
$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
/* Debug */
/*error_reporting(E_ALL);
  ini_set('display_errors', '1');*/

/************************************/
/* listeVacataires
    Récupère la liste des vacataire d'un département

    Retour :
	    [array] liste des vacataires
*/
/************************************/
function listeVacataires($dep)
{
    global $path;

    $file = "$path\\LDAP\\vacataires.json";

    $json = json_decode(file_get_contents($file));
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

    $file = "$path\\LDAP\\vacataires.json";

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
        json_encode($json)
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

    $file = "$path\\LDAP\\vacataires.json";

    $json = json_decode(file_get_contents($file));
    $vac = $json->$dep->vacataires;
    if (array_search($email, $vac) === FALSE)
        return ['result' => "Erreur : Vacataire $ancien inconnu dans le département $dep"];

    unset($vac[array_search($email, $vac)]);
    $json->$dep->vacataires = array_values($vac);

    file_put_contents(
        $file,
        json_encode($json)
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
