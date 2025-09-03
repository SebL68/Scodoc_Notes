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
		global $Config;

		if(!$Config->multi_scodoc) { 
			self::setupLDAP(
				'', // Composante
				[
					'url' => $Config->LDAP_url,
					'user' => $Config->LDAP_user,
					'password' => $Config->LDAP_password,
					'dn' => $Config->LDAP_dn,
					'uid' => $Config->LDAP_uid,
					'idCAS' => $Config->LDAP_idCAS,
					'filtre_ufr' => $Config->LDAP_filtre_ufr,
					'filtre_statut_etudiant' => $Config->LDAP_filtre_statut_etudiant,
					'filtre_enseignant' => $Config->LDAP_filtre_enseignant,
					'filtre_biatss' => $Config->LDAP_filtre_biatss
				]
			);
		} else {
			forEach($Config->LDAP_instances as $composante => $LDAP_instance) {
				self::setupLDAP($composante, $LDAP_instance);
			}
		}

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
    private static function updateList($ds, $file, $filter, $data, $dn){

        $id_result = ldap_search($ds, $dn, $filter);
        $result = ldap_get_entries($ds, $id_result);
        $nb = ldap_count_entries($ds, $id_result);

        echo "$nb entrées LDAP pour la liste $file\n";
        
		$output = '';

        for ($i=0; $i<$nb; $i++){
            $ligne="";
            foreach($data as $entry){
				if(!isset($result[$i][$entry][0])) {
					echo 'Problème avec l\'utilisateur : ' . $result[$i]['uid'][0] . "\n";
					$ligne = false;
					break;
				} else {
					$ligne = ($ligne=="") ? $result[$i][$entry][0] : $ligne.":".$result[$i][$entry][0];
				}
            }

			if($ligne){
				$output .= $ligne."\n";
			}
        }

		if(file_put_contents(
			$file, 
			$output
		) === false
		) {
			returnError("Fichier non enregistré");
		}
        
        return ['result' => "OK"];
    }

	/****************************************************/
    /* setupLDAP() 
        Configurer la connexion au serveur LDAP

		Entrée : 
			$UFR: [string] - Nom de l'UFR (pour le nom du fichier)
			$LDAP_instance: [array] - Configuration de l'instance LDAP
        Sortie :
			[void]
            
    */
    /****************************************************/
		private static function setupLDAP($composante, $LDAP_instance){
			global $path;

			if($composante != '') {
				$STUDENTS_PATH = $path.'data/annuaires/'.$composante.'_liste_etu.txt';
				$TEACHERS_PATH = $path.'data/annuaires/'.$composante.'_liste_ens.txt';
				$BIATSS_PATH = $path.'/data/annuaires/'.$composante.'_liste_biat.txt';
			} else {
				$STUDENTS_PATH = $path.'/data/annuaires/liste_etu.txt';
				$TEACHERS_PATH = $path.'/data/annuaires/liste_ens.txt';
				$BIATSS_PATH = $path.'/data/annuaires/liste_biat.txt';
			}
			

			if ($id_LDAP = self::openLDAP($LDAP_instance)) {
				if ($LDAP_instance['filtre_ufr'] != '') {
					self::updateList($id_LDAP, $STUDENTS_PATH, "(&(".$LDAP_instance['filtre_statut_etudiant'].")(".$LDAP_instance['filtre_ufr']."))", [$LDAP_instance['uid'], $LDAP_instance['idCAS']], $LDAP_instance['dn']);
					self::updateList($id_LDAP, $TEACHERS_PATH, "(&(".$LDAP_instance['filtre_enseignant'].")(".$LDAP_instance['filtre_ufr']."))",      [$LDAP_instance['idCAS']], $LDAP_instance['dn']);
					self::updateList($id_LDAP, $BIATSS_PATH,   "(&(".$LDAP_instance['filtre_biatss'].")(".$LDAP_instance['filtre_ufr']."))",          [$LDAP_instance['idCAS']], $LDAP_instance['dn']);
				} else {
					self::updateList($id_LDAP, $STUDENTS_PATH, "(".$LDAP_instance['filtre_statut_etudiant'].")", [$LDAP_instance['uid'], $LDAP_instance['idCAS']], $LDAP_instance['dn']);
					self::updateList($id_LDAP, $TEACHERS_PATH, "(".$LDAP_instance['filtre_enseignant'].")",      [$LDAP_instance['idCAS']], $LDAP_instance['dn']);
					self::updateList($id_LDAP, $BIATSS_PATH,   "(".$LDAP_instance['filtre_biatss'].")",          [$LDAP_instance['idCAS']], $LDAP_instance['dn']);
				}
			}
			else
				exit("Pas de connexion au serveur LDAP");
			
			ldap_close($id_LDAP);
			echo "Listes des utilisateurs mises à jour<br>\n";
		}

    /****************************************************/
    /* openLDAP() 
        Se connecte et s'authentifie sur le serveur LDAP 

        Sortie :
            [ressource] - Connexion vers le serveur LDAP
    */
    /****************************************************/
    private static function openLDAP($LDAP_instance){
		global $Config;
        $ds=ldap_connect($LDAP_instance['url']);
        if ($ds===FALSE)
            exit("Connexion au serveur LDAP impossible");
    
        if($Config->LDAP_protocol_3){
            ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        }
        
        if($Config->LDAP_verify_TLS == true){
            if (!ldap_start_tls($ds))
                exit("Connexion TLS au serveur LDAP impossible");
        }
        
        // Authentification sur le serveur LDAP
        if (ldap_bind($ds, $LDAP_instance['user'], $LDAP_instance['password']))
            return $ds;
        else
            exit("Authentification sur le serveur LDAP impossible");
    }
};