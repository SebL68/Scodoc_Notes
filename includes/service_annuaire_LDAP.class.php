<?php
/****************************/
/*  Class Service_Annuaire
        Cette classe permet l'accès à un annuaire LDAP
        Il est possible d'en créer une nouvelle pour un autre système d'annuaire
        Il faut alors indiquer le fichier utilisé dans config/config.php

        La méthode attendue est :
            - Service_Annuaire::updateLists()

/****************************/

class Service_Annuaire{

/****************************************************/
/* updateLists() 
	Mise à jour des listes d'utilisateurs à partir du serveur LDAP

*/
/****************************************************/
    public static function updateLists(){
        global $path;

        echo "Enregistrement des listes dans : $path/data/annuaires/<br>\n";

        $STUDENTS_PATH = "$path/data/annuaires/liste_etu.txt";
        $TEACHERS_PATH = "$path/data/annuaires/liste_ens.txt";
        $BIATSS_PATH = "$path/data/annuaires/liste_biat.txt";
        
        if ($id_LDAP = self::openLDAP()) {
            self::updateList($id_LDAP, $STUDENTS_PATH, "(&(".$Config->LDAP_filtre_statut_etudiant.")(".$Config->LDAP_filtre_ufr."))", [$Config->LDAP_uid, $Config->LDAP_mail]);
            self::updateList($id_LDAP, $TEACHERS_PATH, "(&(".$Config->LDAP_filtre_enseignant.")(".$Config->LDAP_filtre_ufr."))",      [$Config->LDAP_mail]);
            self::updateList($id_LDAP, $BIATSS_PATH,   "(&(".$Config->LDAP_filtre_biatss.")(".$Config->LDAP_filtre_ufr."))",          [$Config->LDAP_mail]);
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
    private static function updateList($ds, $file, $filter, $data){

        if(!$id_file = fopen($file, "w"))
            exit("Impossible d'ouvrir le fichier $file");

        if(!flock($id_file, LOCK_EX))
            exit("Impossible de verrouiller le fichier $file");

        $id_result = ldap_search($ds, $Config->LDAP_dn, $filter);
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
        
        return ['result' => "OK"];
    }

    /****************************************************/
    /* openLDAP() 
        Se connecte et s'authentifie sur le serveur LDAP 

        Sortie :
            [ressource] - Connexion vers le serveur LDAP
    */
    /****************************************************/
    private static function openLDAP(){

        $ds=ldap_connect($Config->LDAP_url);
        if ($ds===FALSE)
            exit("Connexion au serveur LDAP impossible");
    
        // ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
        // ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (!ldap_start_tls($ds))
            exit("Connexion TLS au serveur LDAP impossible");
        
        // Authentification sur le serveur LDAP
        if (ldap_bind($ds, $Config->LDAP_user, $Config->LDAP_password))
            return $ds;
        else
            exit("Authentification sur le serveur LDAP impossible");
    }
};
?>
