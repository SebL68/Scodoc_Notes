<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
/* Debug */
	/*error_reporting(E_ALL);
	ini_set('display_errors', '1');*/
	function setAbsence($enseignant, $dep, $semestre, $matiere, $etudiant, $date, $creneau, $statut){
		global $path;
		global $authData;
		
		$dir = "$path/absencesDATA/$dep/$semestre/";
		$file = $dir.$etudiant.'.json';

		if(!is_dir($dir)){
			mkdir($dir, 0777, true);
		}

		if(!is_file($file)){
			file_put_contents(
				$file, 
				json_encode(
					[
						newAbsence($enseignant, $matiere, $date, $creneau, $statut)
					]
				)
			); 
		} else {
			$json = json_decode(file_get_contents($file));
			$trouve = false;
			foreach ($json as $key => &$absence) {
				if($absence->date == $date && $absence->creneau == $creneau){
					if($statut == 'présent'){
						unset($json[$key]);
					}else{
						$absence->statut = $statut;
					}
					$trouve = true;
					break;
				}
			}
			if(!$trouve){
				$json[] = newAbsence($enseignant, $matiere, $date, $creneau, $statut);
			}
			file_put_contents(
				$file, 
				json_encode($json)
			);
		}
	}

	function newAbsence($enseignant, $matiere, $date, $creneau, $statut){
		return [
			"date" => $date,
			"creneau" => $creneau,
			"statut" => $statut,
			"enseignant" => $enseignant,
			"matiere" => $matiere
		];
	}

	function getAbsence(){

	}
?>