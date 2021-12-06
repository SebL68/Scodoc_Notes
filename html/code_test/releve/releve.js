/* Il manque :
	- rangs
	- Synthèse : moyenne UE
	- Synthèse : min, max, moy classe
	- Synthèse : absences
	- Eval : absences

Moi : 
	"show_codemodules" :true,
	"show_minmax": true, 
	"show_minmax_eval": true, 
	"show_minmax_mod": false, 
	"show_mod_rangs": false, 
	"show_moypromo": true, 
	"show_rangs": true, 
	"show_ue_cap_current": true, 
	"show_ue_cap_details": true, 
	"show_ue_rangs": true, 
	"show_uevalid": true,
*/
/*****************************/
/* Gestionnaire d'événements */
/*****************************/
document.querySelectorAll(".CTA_Liste").forEach(e=>{
	e.addEventListener("click", listeOnOff)
})

function listeOnOff(){
	this.parentElement.parentElement.classList.toggle("listeOff")
}
/*****************************/
/* Recupération et affichage */
/*****************************/

fetch(dataSrc)
.then(r=>{return r.json()})
.then(json=>showData(json))

function showData(data){
	showInformations(data);
	showSemestre(data);
	showSynthese(data);
	showEvaluations(data);

	setOptions(data.options);

	document.body.classList.add("ready");
}

/********************************/
/* Informations sur l'étudiant  */
/********************************/
function showInformations(data){
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
			<div>${data.formation.titre}</div>
			
		</div>
	`;

	document.querySelector(".infoEtudiant").innerHTML = output;
}

/*******************************/
/* Information sur le semestre */
/*******************************/
function showSemestre(data){
	document.querySelector("h2").innerHTML += data.semestre.numero;
	document.querySelector(".dateInscription").innerHTML += ISOToDate(data.semestre.inscription);
	let output = `
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
}

/*******************************/
/* Synthèse                    */
/*******************************/
function showSynthese(data){
	let output = ``;
	Object.entries(data.ues).forEach(([ue, dataUE])=>{
		output += `
			<div class=ue>
				<h3>
					${(dataUE.competence) ? dataUE.competence + " - " : ""}${ue}
				</h3>
				<div>
					<div class=moyenne>Moyenne&nbsp;:&nbsp;${dataUE.moyenne?.value || "-"}</div>
					<div class=info>
						Bonus&nbsp;:&nbsp;${dataUE.bonus || 0}&nbsp;- 
						Malus&nbsp;:&nbsp;${dataUE.malus || 0}
						<span class=ects>&nbsp;-
							ECTS&nbsp;:&nbsp;${dataUE.ECTS.acquis}&nbsp;/&nbsp;${dataUE.ECTS.total}
						</span>
					</div>
				</div>
				<div class=absences>
					<div>Abs&nbsp;inj.</div><div>${dataUE.absences?.injustifie || 0}</div>
					<div>Total</div><div>${dataUE.absences?.total || 0}</div>
				</div>
			</div>
			${synthese(dataUE.ressources)}
			${synthese(dataUE.saes)}
		`;
	});
	document.querySelector(".synthese").innerHTML = output;

	function synthese(modules){
		let output = "";
		Object.entries(modules).forEach(([module, dataModule])=>{
			let titre = data.ressources[module]?.titre || data.saes[module]?.titre;
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
}
/*******************************/
/* Evaluations                 */
/*******************************/
function showEvaluations(data){
	document.querySelector(".evaluations").innerHTML = module(data.ressources);
	document.querySelector(".sae").innerHTML += module(data.saes);

	function module(module){
		let output = "";
		Object.entries(module).forEach(([numero, content])=>{
			output += `
				<div>
					<div class=module>
						<h3>${numero} - ${content.titre}</h3>
						<div>
							<div class=moyenne>Moyenne&nbsp;indicative&nbsp;:&nbsp;${content.moyenne.value}</div>
							<div class=info>
								Classe&nbsp;:&nbsp;${content.moyenne.moy}&nbsp;- 
								Max&nbsp;:&nbsp;${content.moyenne.max}&nbsp;-
								Min&nbsp;:&nbsp;${content.moyenne.min} 
							</div>
						</div>
						<div class=absences>
							<div>Abs&nbsp;inj.</div><div>${content.absences?.injustifie || 0}</div>
							<div>Total</div><div>${content.absences?.total || 0}</div>
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
					<div>${eval.description}</div>
					<div>
						${eval.note.value}
						<em>Coef. ${eval.coef}</em>
					</div>
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
}

/********************/
/* Options          */
/********************/
function setOptions(options){
	Object.entries(options).forEach(([option, value])=>{
		if(value === false){
			document.body.classList.add(option.replace("show", "hide"))
		}
	})
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