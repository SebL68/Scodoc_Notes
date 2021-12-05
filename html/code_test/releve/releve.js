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
		</div>
	`;
	document.querySelector(".infoEtudiant").innerHTML = output;

/*******************************/
/* Information sur le semestre */
/*******************************/
	document.querySelector("h2").innerHTML += data.semestre.numero;	// Numéro du titre semestre.
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
/* Synthèse                    */
/*******************************/	
	output = ``;
	Object.entries(data.ue).forEach(([ue, dataUE])=>{
		output += `
			<div class=ue>
				<h3>
					${(dataUE.competence) ? dataUE.competence + " - " : ""}${ue}
				</h3>
				<div>
					<div class=moyenne>Moyenne&nbsp;:&nbsp;${dataUE.moyenne.value}</div>
					<div class=info>
						Bonus&nbsp;:&nbsp;${dataUE.bonus}&nbsp;- 
						Malus&nbsp;:&nbsp;${dataUE.malus}&nbsp;-
						ECTS&nbsp;:&nbsp;${dataUE.ECTS.acquis}&nbsp;/&nbsp;${dataUE.ECTS.total}
					</div>
				</div>
				<div class=absences>
					<div>Abs&nbsp;inj.</div><div>${dataUE.absences.injustifie}</div>
					<div>Total</div><div>${dataUE.absences.total}</div>
				</div>
			</div>
			${synthese(dataUE.ressources)}
			${synthese(dataUE.sae)}
		`;
	});
	document.querySelector(".synthese").innerHTML = output;

	function synthese(modules){
		let output = "";
		Object.entries(modules).forEach(([module, dataModule])=>{
			let titre = data.ressources[module]?.texte || data.sae[module]?.texte;
			output += `
				<div class=syntheseModule>
					<div>${module}&nbsp;- ${titre}</div>
					<div>
						${dataModule.moyenne}
						<em>Coef. ${dataModule.coef}</em>
					</div>
				</div>
			`;
		})
		return output;
	}

/*******************************/
/* Evaluations                 */
/*******************************/	
	document.querySelector(".evaluations").innerHTML = module(data.ressources);
	document.querySelector(".sae").innerHTML += module(data.sae);

	function module(module){
		let output = "";
		Object.entries(module).forEach(([numero, content])=>{
			output += `
				<div>
					<div class=module>
						<h3>${numero} - ${content.texte}</h3>
						<div>
							<div class=moyenne>Moyenne&nbsp;indicative&nbsp;:&nbsp;${content.moyenne.value}</div>
							<div class=info>
								Classe&nbsp;:&nbsp;${content.moyenne.moy}&nbsp;- 
								Max&nbsp;:&nbsp;${content.moyenne.max}&nbsp;-
								Min&nbsp;:&nbsp;${content.moyenne.min} 
							</div>
						</div>
						<div class=absences>
							<div>Abs&nbsp;inj.</div><div>${content.absences.injustifie}</div>
							<div>Total</div><div>${content.absences.total}</div>
						</div>
					</div>
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
					<div>${eval.texte}</div>
					<div>${eval.note.value}</div>
					<div class=complement>
						<div>Coef</div><div>${eval.coef}</div>
						<div>Max. promo.</div><div>${eval.note.max}</div>
						<div>Moy. promo.</div><div>${eval.note.moy}</div>
						<div>Min. promo.</div><div>${eval.note.min}</div>
						${Object.entries(eval.poids).map(([UE, poids])=>{
							return `
								<div>Poids ${UE}</div>
								<div>${poids}</div>
							`;
						}).join("")}
					</div>
				</div>
			`;
		})
		return output;
	}

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