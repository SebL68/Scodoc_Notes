<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
/* Debug */
	/*error_reporting(E_ALL);
	ini_set('display_errors', '1');*/

/************************************/
/* setAbsence
	Créé ou modifie le fichier d'absence d'un étudiant
*/
/************************************/
	function setAbsence($enseignant, $dep, $semestre, $matiere, $etudiant, $date, $creneau, $creneauxIndex, $statut){
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
						newAbsence($enseignant, $matiere, $date, $creneau, $creneauxIndex, $statut)
					]
				)
			);
		} else {
			$json = json_decode(file_get_contents($file));
			$trouve = false;
			foreach ($json as $key => &$absence) {
				if($absence->date == $date && $absence->creneau == $creneau){
					if($statut == 'présent'){
						array_splice($json, $key, 1);
					}else{
						$absence->statut = $statut;
					}
					$trouve = true;
					break;
				}
			}
			if(!$trouve){
				$json[] = newAbsence($enseignant, $matiere, $date, $creneau, $creneauxIndex, $statut);
			}
			if(count($json) == 0){
				unlink($file);
			}else{
				file_put_contents(
					$file, 
					json_encode($json)
				);
			}
			
		}
	}

	function newAbsence($enseignant, $matiere, $date, $creneau, $creneauxIndex, $statut){
		return [
			"date" => $date,
			"creneau" => $creneau,
			"creneauxIndex" => $creneauxIndex,
			"statut" => $statut,
			"enseignant" => $enseignant,
			"matiere" => $matiere
		];
	}
/************************************/
/* getAbsence
	Récupère les absences d'un semestre ou d'un étudiant en fonction de si le paramètre $etudiant est défini ou non

	Retour : 
		[assoc. array] absences d'un étudiant
		[array][assic. array] liste des absences d'un étudiant
*/
/************************************/
	function getAbsence($dep, $semestre, $etudiant = ''){
		global $path;
		$dir = "$path/absencesDATA/$dep/$semestre/";
		if($etudiant == ''){
			$output = [];
			$listFiles = scandir($dir);
			
			foreach($listFiles as $file){
				if($file != "." && $file != ".."){
					$output[substr($file, 0, -5)] = json_decode(file_get_contents($dir.$file));
				}
			}
		} else {
			$file = $dir.$etudiant.'.json';
			$output = json_decode(file_get_contents($file));
		}
		
		return $output;
	}
?>