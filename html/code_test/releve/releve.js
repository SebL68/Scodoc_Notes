/*******************************/
/* Informations sur l'étudiant */
/*******************************/
	document.querySelector(".studentPic").src = data.etudiant.photo_url || "default_Student.svg";

	let output = `
		<div class=info_etudiant>
			<div class=civilite>
				${civilite(data.etudiant.civilite)}
				${data.etudiant.nom}
				${data.etudiant.prenom}
				né${(data.etudiant.civilite == "F") ? "e" : ""} le ${ISOToDate(data.etudiant.date_naissance)}
			</div>
			<div class=numerosEtudiant>
				Numéro étudiant : ${data.etudiant.code_nip}
				Code INE : ${data.etudiant.code_ine}
			</div>
		</div>
	`;
/*********************************/
/* Informations sur la formation */
/*********************************/
	output += `
		<div class=info_releve>
			${data.formation.acronyme}
			- semestre ${data.semestre.numero}
		</div>
	`;
	document.querySelector(".infoEtudiant").innerHTML = output;

/*******************************/
/* Information sur le semestre */
/*******************************/	
	output = `
		<div>
			<div class=enteteSemestre>Moyenne</div><div class=enteteSemestre>${data.semestre.notes.value}</div>
			<div class=rang>Rang :</div><div class=rang>${data.semestre.rang.value} / ${data.semestre.rang.total}</div>
			<div>Max. promo. :</div><div>${data.semestre.notes.max}</div>
			<div>Moy. promo. :</div><div>${data.semestre.notes.moy}</div>
			<div>Min. promo. :</div><div>${data.semestre.notes.min}</div>
		</div>
		${
			data.semestre.groupes.map(groupe=>{
				return `
					<div>
						<div class=enteteSemestre>Groupe</div><div class=enteteSemestre>${groupe.nom}</div>
						<div class=rang>Rang :</div><div class=rang>${groupe.rang.value} / ${groupe.rang.total}</div>
						<div>Max. groupe :</div><div>${groupe.notes.max}</div>
						<div>Moy. groupe :</div><div>${groupe.notes.min}</div>
						<div>Min. groupe :</div><div>${groupe.notes.min}</div>
					</div>
				`;
			}).join("")
		}
	`;
	document.querySelector(".infoSemestre").innerHTML = output;

/*******************************/
/* Evaluations                 */
/*******************************/	
	document.querySelector(".evaluations").innerHTML = module(data.ressources);
	document.querySelector(".evaluations").innerHTML += module(data.sae);

	function module(module){
		let output = "";
		Object.entries(module).forEach(([numero, content])=>{
			output += `
				<div>
					${numero} - ${content.texte}
					${evaluation(content.evaluations)}
				</div>
			`;
		})
		return output;
	}

	function evaluation(evaluations){
		let output = "";
		evaluations.forEach(eval=>{
			output += `
				<div class=eval>
					${eval.texte} - ${eval.note.value}
				</div>
			`;
		})
		return output;
	}
/*******************************/
/* Synthèse                    */
/*******************************/	
	output = `
		<div>

		</div>
	`;
	document.querySelector(".synthese").innerHTML = output;

/********************/
/* Fonctions d'aide */
/********************/
	function civilite(txt){
		switch(txt){
			case "M": return "M.";
			case "F": return "Mme";
			default: return "";
		}
	}

	function ISOToDate(ISO){
		return ISO.split("-").reverse().join("/");
	}