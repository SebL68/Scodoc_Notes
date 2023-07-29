<?php
	class Absences {
		/************************************
		* setAbsence
		*	Créé ou modifie le fichier d'absence d'un étudiant
		*
		************************************/
		public static function setAbsence($enseignant, $semestre, $matiere, $matiereComplet, $etudiant, $date, $debut, $fin, $statut){

			$debut = floatval($debut);
			$fin = floatval($fin);
			


			return ['result' => 'OK'];
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

		/*
		// From Scodoc :
		[
			{
				"assiduite_id": 38649,
				"etudid": 5167,
				"moduleimpl_id": null,
				"date_debut": "2022-11-27T08:00:00+0100",
				"date_fin": "2022-11-27T10:00:00+0100",
				"etat": "ABSENT",
				"desc": null,
				"entry_date": "2023-07-27T15:02:57+0200",
				"user_id": "N. Acces",
				"est_just": false
			},etc.
		]

		// To passerelle :
		{
			"2023-06-16": [
				{
					"debut": 10,
					"fin": 12,
					"statut": "absent",
					"justifie": false,
					"enseignant": "Jean Bon",
					"matiere": "SA\u00c9202",
					"matiereComplet": "SA\u00c9202 - Concevoir un produit ou un service et sa communication"
				}, etc.
			], etc.
		}
 		
	*/
		public static function getAbsence($semestre, $etudiant = ''){
			$Scodoc = new Scodoc();
			if($etudiant == '') {
				// On récupère les absences de tous les étudiants du semestre
				$data = $Scodoc->getSemesterAbsences($semestre);
				return Absences::scoAbsDataToPasserelle($data, true);
			} else {
				// Sinon les absences d'un étudiant lors de ce semestre
				$data = $Scodoc->getStudentAbsences($semestre, $etudiant);
				return Absences::scoAbsDataToPasserelle($data, false);
			}
		}

		private static function scoAbsDataToPasserelle($data, $groupNip) {
			$output = [];
				for($i=0 ; $i<count($data) ; $i++){

					$timestampDebut = strtotime(explode('+', $data[$i]->date_debut)[0]);
					$timestampFin = strtotime(explode('+', $data[$i]->date_fin)[0]);

					$temp = [
						'id' => $data[$i]->assiduite_id,
						'debut' => Absences::hoursToFloat(date('G:i', $timestampDebut)),
						'fin' => Absences::hoursToFloat(date('G:i', $timestampFin)),
						'statut' => strtolower($data[$i]->etat),
						'justifie' => $data[$i]->est_just,
						'enseignant' => $data[$i]->user_id,			// Accepte n'importe quel nom, même s'il n'existe pas ?
						'matiereComplet' => $data[$i]->moduleimpl_id // Faudrait en plus le texte du module
					];

					if($groupNip) {
						$output[$data[$i]->code_nip][date('Y-m-d', $timestampDebut)][] = $temp;
					} else {
						$output[date('Y-m-d', $timestampDebut)][] = $temp;
					}
				}
			return $output;
		}

		private static function hoursToFloat($val){
			$parts = explode(':', $val);
			return $parts[0] + $parts[1]/60;
		}

	/************************************
	* setJustify
	*	Justification a true ou false
	*
	************************************/
		public static function setJustifie($semestre, $etudiant, $date, $debut, $justifie){

			return ['result' => 'OK'];
		}
	}
