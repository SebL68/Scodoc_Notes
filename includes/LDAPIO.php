<?php
/****************************************************/
/* setCron() 
	Configuration de CRON pour la mise à jour automatique des listes d'utilisateurs à partir du serveur LDAP

*/
/****************************************************/
function setCron(){
    global $argv;
    global $PHP_cmd;
    global $tmp_dir;
    global $CRON_delay;
    
    if (isset($argv))
        $path = dirname(dirname(realpath($argv[0])));           // Exécution par CLI
    else
        $path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');    // Exécution par serveur web

    // Ancienne configuration de CRON
    $output = shell_exec('crontab -l');
    echo "Ancienne commande dans crontab : <br>\n".$output."<br>\n";

    $cron_cmd = $CRON_delay." ".$PHP_cmd." ".$path."/includes/CmdUpdateLists.php";
    echo "Nouvelle commande CRON : ".$cron_cmd."<br>\n";
    
    file_put_contents("$tmp_dir/crontab.txt", $cron_cmd.PHP_EOL);
    echo exec("crontab $tmp_dir/crontab.txt");

    // Vérification de la nouvelle configuration
    $output = shell_exec('crontab -l');
    if (trim($output) != $cron_cmd)
        echo "Impossible de programmer CRON : $output<br>\n";
    else {
        echo "Configuration de CRON réussie<br>\n";
        return ['result' => "OK"];
    }
}

/****************************************************/
/* updateLists() 
	Mise à jour des listes d'utilisateurs à partir du serveur LDAP

*/
/****************************************************/
function updateLists(){
    global $path;
    global $LDAP_filtre_ufr;
    global $LDAP_filtre_statut_etudiant;
    global $LDAP_filtre_enseignant;
    global $LDAP_filtre_biatss;
    global $LDAP_uid;
    global $LDAP_mail;

    echo "Enregistrement des listes dans : $path/LDAP/<br>\n";

    $STUDENTS_PATH = "$path/LDAP/liste_etu_iutmulhouse.txt";
    $TEACHERS_PATH = "$path/LDAP/liste_ens_iutmulhouse.txt";
    $BIATSS_PATH = "$path/LDAP/liste_biat_iutmulhouse.txt";
    
    if ($id_LDAP = openLDAP()) {
        updateList($id_LDAP, $STUDENTS_PATH, "(&($LDAP_filtre_statut_etudiant)($LDAP_filtre_ufr))", [$LDAP_uid, $LDAP_mail]);
        updateList($id_LDAP, $TEACHERS_PATH, "(&($LDAP_filtre_enseignant)($LDAP_filtre_ufr))",      [$LDAP_mail]);
        updateList($id_LDAP, $BIATSS_PATH,   "(&($LDAP_filtre_biatss)($LDAP_filtre_ufr))",          [$LDAP_mail]);
    }
    else
        exit("Pas de connexion au serveur LDAP");
    
    ldap_close($id_LDAP);
    echo "Listes des utilisateurs mises à jour<br>\n";
    return ['result' => "OK"];
}

/****************************************************/
/* updateList() 
	Mise à jour du fichier liste d'utilisateurs
    
    Entrée :
        $ds: [ressource] - Connexion au serveur LDAP
        $file: [string] - Nom du fichier à mettre à jour
        $filter: [string] - Filtre LDAP des utilisateurs
        $data: [array] - Liste des entrées LDAP à enregistrer dans la liste

	Sortie :
		[ressource] - Connexion vers le serveur LDAP
*/
/****************************************************/
function updateList($ds, $file, $filter, $data){
    global $LDAP_dn;
    global $webServerUser;
    global $webServerGroup;

    if(!$id_file = fopen($file, "w"))
        exit("Impossible d'ouvrir le fichier $file");

    if(!flock($id_file, LOCK_EX))
        exit("Impossible de verrouiller le fichier $file");

    $id_result = ldap_search($ds, $LDAP_dn, $filter);
    $result = ldap_get_entries($ds, $id_result);
    $nb = ldap_count_entries($ds, $id_result);

    echo "$nb lignes dans la liste $file\n";
    
    for ($i=0; $i<$nb; $i++){
        $ligne="";
        foreach($data as $entry){
            $ligne = ($ligne=="") ? $result[$i][$entry][0] : $ligne.":".$result[$i][$entry][0];
        }
        //echo $ligne."\n";
        if (fwrite($id_file, $ligne."\n") === FALSE)
            exit("Impossible d'écrire dans le fichier $file");
    }

    flock($id_file, LOCK_UN);
    fclose($id_file);
    chmod($file, 0664);
    chown($file, $webServerUser);
    chgrp($file, $webServerGroup);
    
    return ['result' => "OK"];
}

/****************************************************/
/* openLDAP() 
	Se connecte et s'authentifie sur le serveur LDAP 

	Sortie :
		[ressource] - Connexion vers le serveur LDAP
*/
/****************************************************/
function openLDAP(){
	global $LDAP_url;
	global $LDAP_user;
	global $LDAP_password;

	$ds=ldap_connect($LDAP_url);
	if ($ds===FALSE)
        exit("Connexion au serveur LDAP impossible");
  
	// ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
	// ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

	if (!ldap_start_tls($ds))
        exit("Connexion TLS au serveur LDAP impossible");
	
	// Authentification sur le serveur LDAP
	if (ldap_bind($ds, $LDAP_user, $LDAP_password))
	 	return $ds;
	else
        exit("Authentification sur le serveur LDAP impossible");
}
?>
