<?php
	class Absences {
		/************************************
		* setAbsence
		*	Créé ou modifie le fichier d'absence d'un étudiant
		*
		************************************/
		public static function setAbsence($enseignant, $semestre, $matiere, $matiereComplet, $UE, $etudiant, $date, $debut, $fin, $statut){
			global $path;

			$debut = floatval($debut);
			$fin = floatval($fin);
			
			$dir = "$path/data/absences/$semestre/";
			$file = $dir.$etudiant.'.json';
	
			if(!is_dir($dir)){
				mkdir($dir, 0774, true);
			}
	
			if(!is_file($file)){ // Pas encore de fichier d'absence pour cet étudiant

				$json = [
					$date => [
						self::newAbsence($enseignant, $matiere, $matiereComplet, $UE, $debut, $fin, $statut)
					]
				];

			} else { // Fichier présent

				$json = json_decode(file_get_contents($file), true);

				if (!$json[$date]){	// Date non présente

					$json[$date] = [
						self::newAbsence($enseignant, $matiere, $matiereComplet, $UE, $debut, $fin, $statut)
					];

				} else { // Date présente
					
					$found = false;
					for($i=0 ; $i<count($json[$date]) ; $i++){	// Pour chaque absence de la date

						if ( $json[$date][$i]['debut'] == $debut // Même créneau
							&& $json[$date][$i]['fin'] == $fin ) {
							
							if ($json[$date][$i][$enseignant] == $enseignant) {
								$found = true;

								if($statut == 'unset'){
									unset($json[$date][$i]);
									if(count($json[$date]) == 0){
										unset($json[$date]);
									}
								} else {
									$json[$date][$i]['statut'] = $statut;
								}
								
								break;
							} else {
								return ['problem' => 'Une absence est déjà renseigné sur ce créneau par ' . $json[$date][$i][$enseignant]];
							}

						} elseif ( $json[$date][$i]['debut'] >= $fin	// N'existe pas encore
							|| $json[$date][$i]['fin'] <= $debut ){

							continue;

						} else {	// A cheval

							return ['problem' => 'Le créneau est à cheval avec une autre absence renseignée par ' . $json[$date][$i][$enseignant]];
							
						}
					}

					if(!$found){
						$json[$date][] = self::newAbsence($enseignant, $matiere, $matiereComplet, $UE, $debut, $fin, $statut);
					}
				}
			}

			
			if(count($json) == 0){
				unlink($file);
			}else{
				file_put_contents(
					$file, 
					json_encode($json)
				);
			}

			return ['result' => 'OK'];
		}
	
		private static function newAbsence($enseignant, $matiere, $matiereComplet, $UE, $debut, $fin, $statut){
			return [
				"debut" => $debut,
				"fin" => $fin,
				"statut" => $statut,
				"justifie" => false,
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
		public static function getAbsence($semestre, $etudiant = ''){
			global $path;
			$dir = "$path/data/absences/$semestre/";
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
				if(file_exists($file)){
					$output = json_decode(file_get_contents($file));
				} else {
					$output = '';
				}
			}
			
			return $output;
		}
	}