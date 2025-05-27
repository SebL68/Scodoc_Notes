<?php
	class Absences {
		/************************************
		* setAbsence
		*	Créé ou modifie le fichier d'absence d'un étudiant
		*
		************************************/
		public static function setAbsence($departement, $enseignant, $semestre, $matiere, $matiereComplet, $etudiant, $date, $debut, $fin, $statut, $order, $id, $idMatiere){

			$Scodoc = new Scodoc();

			switch($order){
				case 'ajout':
					$ISODebut = Absences::ISODate($date, floatval($debut));
					$ISOFin = Absences::ISODate($date, floatval($fin));
					
					$data = [
						[
							'date_debut' => $ISODebut,
							'date_fin' => $ISOFin,
							'etat' => $statut,
							'moduleimpl_id' => intval($idMatiere),
							'external_data' => [
								'enseignant' => $enseignant
							]
						]
					];
					$response = $Scodoc->createAbsence($departement, $etudiant, json_encode($data));
					if(isset($response->success[0]->message->assiduite_id)) {
						return [
							'result' => 'OK',
							'id' => $response->success[0]->message->assiduite_id
						];
					} else if(isset($response->errors[0]->message)) {
						return [
							'problem' => $response->errors[0]->message
						];
					}
				break;

				case 'modif':
					$data = [
						'etat' => $statut
					];
					$response = $Scodoc->modifAbsence($id, json_encode($data));
					if(isset($response->OK) && $response->OK == true) {
						return [
							'result' => 'OK'
						];
					}
				break;

				case 'suppr':
					$response = $Scodoc->deleteAbsence("[$id]");
					if(isset($response->success[0]->message)) {
						return [
							'result' => 'OK'
						];
					}
				break;
			}
			
			
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
				"est_just": false,
				"external_data": {du JSON}
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

				$idJustif = [];
				for($j=0 ; $j<count($data[$i]->justificatifs ?? []) ; $j++) {
					$idJustif[] = $data[$i]->justificatifs[$j]->justif_id;
				}
				$temp = [
					'idAbs' => $data[$i]->assiduite_id,
					'idJustif' => $idJustif,
					'debut' => Absences::hoursToFloat(date('G:i', $timestampDebut)),
					'fin' => Absences::hoursToFloat(date('G:i', $timestampFin)),
					'statut' => strtolower($data[$i]->etat),
					'justifie' => $data[$i]->est_just,
					'enseignant' => $data[$i]->external_data->enseignant ?? $data[$i]->user_name ?? 'Non défini',
					'matiereComplet' => $data[$i]->moduleimpl_id ?? 'Non défini',
					'dateFin' => date('Y-m-d', $timestampFin)
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

		private static function floatToHours($time){
			return sprintf('%02d:%02d', (int) $time, fmod($time, 1) * 60);
		}

		private static function ISODate($date, $time){
			return $date . 'T' . Absences::floatToHours($time) . ':00';
		}


	/************************************
	* setJustify
	*	Justification a true ou false
	*
	************************************/
		public static function setJustifie($semestre, $etudiant, $date, $debut, $fin, $justifie, $id){
			$Scodoc = new Scodoc();
			
			if($justifie === 'true') {

				$ISODebut = Absences::ISODate($date, $debut);
				$ISOFin = Absences::ISODate($date, $fin);

				$response = $Scodoc->setJustif($etudiant, $ISODebut, $ISOFin);

				if(isset($response->success[0]->message->justif_id)) {
					return [
						'result' => 'OK',
						'id' => $response->success[0]->message->justif_id
					];
				} 
			} else {
				$response = $Scodoc->unsetJustif("[$id]");
				if(isset($response->success[0]->message) && count($response->errors ?? []) == 0) {
					return ['result' => 'OK'];
				} 
			}
			return ['result' => 'NOK'];
		}

	/************************************
	* getJustify
	*	Renvoie les justificatifs de Scodoc
	*
	************************************/
		public static function getJustifs($nip){
			$Scodoc = new Scodoc();
			return $Scodoc->getJustifs($nip);
		}
	}
