<?php
	class Absences {
		/************************************
		* setAbsence
		*	Créé ou modifie le fichier d'absence d'un étudiant
		*
		************************************/
		public static function setAbsence($enseignant, $dep, $semestre, $matiere, $matiereComplet, $UE, $etudiant, $date, $creneau, $creneauxIndex, $statut){
			global $path;
			global $authData;
			
			$dir = "$path/data/absencesDATA/$dep/$semestre/";
			$file = $dir.$etudiant.'.json';
	
			if(!is_dir($dir)){
				mkdir($dir, 0774, true);
			}
	
			if(!is_file($file)){ // Pas encore de fichier d'absence pour cet étudiant
				file_put_contents(
					$file, 
					json_encode(
						[
							$date => [
								$creneau => self::newAbsence($enseignant, $matiere, $matiereComplet, $UE, $creneauxIndex, $statut)
							]
						]
					)
				);
				chmod($file, 0774);
			} else { // Fichier d'absence présent pour cet étudiant
				$json = json_decode(file_get_contents($file), true);
				if(isset($json[$date][$creneau])){ // Déjà une absence sur cette date / créneau
					if($statut == 'présent'){ // Suppression de l'absence
						unset($json[$date][$creneau]);
						if(count($json[$date]) == 0){
							unset($json[$date]);
						}
					}else{ // Changement de statut
						$json[$date][$creneau]['statut'] = $statut;
					}
				} else { // Pas encore d'absence sur le créneau : on en créé une nouvelle
					$json[$date][$creneau] = self::newAbsence($enseignant, $matiere, $matiereComplet, $UE, $creneauxIndex, $statut);
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
	
		private static function newAbsence($enseignant, $matiere, $matiereComplet, $UE, $creneauxIndex, $statut){
			return [
				"creneauxIndex" => $creneauxIndex,
				"statut" => $statut,
				"enseignant" => $enseignant,
				"matiere" => $matiere,
				"matiereComplet" => $matiereComplet,
				"UE" => $UE
			];
		}
	/************************************
	* getAbsence
	*	Récupère les absences d'un semestre ou d'un étudiant en fonction de si le paramètre $etudiant est défini ou non
	*
	*	Retour : 
	*		[assoc. array] absences d'un étudiant
	*		[array][assic. array] liste des absences d'un étudiant
	*
	************************************/
		public static function getAbsence($dep, $semestre, $etudiant = ''){
			global $path;
			$dir = "$path/data/absencesDATA/$dep/$semestre/";
			if($etudiant == ''){
				$output = [];
				$listFiles = [];
				if(is_dir($dir)){
					$listFiles = scandir($dir);
				}
				
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
	}
?>