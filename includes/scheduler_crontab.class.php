<?php
/****************************/
/*  Class Scheduler
        Cette classe permet de planifier l'exécution de tâches périodiques avec crontab
        Il est possible d'en créer une nouvelle pour un autre système de gestionnaire de tâches
        Il faut alors indiquer le fichier utilisé dans config/config.php

        La méthode attendue est :
            - Scheduler::setUpdateLists()

/****************************/

class Scheduler{

/****************************************************/
/* setUpdateLists() 
	Configuration de crontab pour la mise à jour périodique des listes d'utilisateurs

*/
/****************************************************/
    public static function setUpdateLists(){
      global $argv;
	  global $Config;
      
      if (isset($argv))
          $path = dirname(dirname(realpath($argv[0])));           // Exécution par CLI
      else
          $path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');    // Exécution par serveur web

      // Ancienne configuration de CRON
      $output = shell_exec('crontab -l');
      echo "Ancienne commande dans crontab : <br>\n".$output."<br>\n";

      $cron_cmd = $Config->CRON_delay." ".$Config->PHP_cmd." ".$path."/includes/CmdUpdateLists.php";
      echo "Nouvelle commande CRON : ".$cron_cmd."<br>\n";
      
      file_put_contents($Config->tmp_dir . "/crontab.txt", $cron_cmd.PHP_EOL);
      echo exec("crontab " . $Config->tmp_dir . "/crontab.txt");

      // Vérification de la nouvelle configuration
      $output = shell_exec('crontab -l');
      if (trim($output) != $cron_cmd)
          echo "Impossible de programmer CRON : $output<br>\n";
      else {
          echo "Configuration de CRON réussie<br>\n";
          return ['result' => "OK"];
      }
    }
};
?>