<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
/* Debug */
	/*error_reporting(E_ALL);
	ini_set('display_errors', '1');*/
	function setAbsence($dep, $semestre, $matiere, $etudiant, $date, $creneaux, $statut){
		global $path;
		
		$dir = "$path/absencesDATA/$dep/$semestre/";
		$file = $dir.$etudiant.'.json';

		if(!is_dir($dir)){
			var_dump(mkdir($dir, 0777, true));
		}

		if(!is_file($file)){
			var_dump(file_put_contents($file, 'ok')); 
		}
	}

	function getAbsence(){

	}
?>