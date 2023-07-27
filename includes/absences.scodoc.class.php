<?php
	class Absences {
		/************************************
		* setAbsence
		*	Créé ou modifie le fichier d'absence d'un étudiant
		*
		************************************/
		public static function setAbsence($enseignant, $semestre, $matiere, $matiereComplet, $etudiant, $date, $debut, $fin, $statut){
			global $path;

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
		public static function getAbsence($semestre, $etudiant = ''){
			global $path;

			
			return $output;
		}

	/************************************
	* setJustify
	*	Justification a true ou false
	*
	************************************/
		public static function setJustifie($semestre, $etudiant, $date, $debut, $justifie){
			global $path;

			return ['result' => 'OK'];
		}
	}
