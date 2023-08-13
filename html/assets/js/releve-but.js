/* Module par Seb. L. */
class releveBUT extends HTMLElement {
	constructor() {
		super();
		this.shadow = this.attachShadow({ mode: 'open' });

		/* Config par defaut */
		this.config = {
			showURL: true
		};

		/* Template du module */
		this.shadow.innerHTML = this.template();

		/* Style du module */
		const styles = document.createElement('link');
		styles.setAttribute('rel', 'stylesheet');
		if (location.href.split("/")[3] == "ScoDoc") {
			styles.setAttribute('href', '/ScoDoc/static/css/releve-but.css');	// Scodoc
		} else {
			styles.setAttribute('href', '/assets/styles/releve-but.css');		// Passerelle
		}
		this.shadow.appendChild(styles);
	}
	listeOnOff() {
		this.parentElement.parentElement.classList.toggle("listeOff");
		this.parentElement.parentElement.querySelectorAll(".moduleOnOff").forEach(e => {
			e.classList.remove("moduleOnOff")
		})
	}
	moduleOnOff() {
		this.parentElement.classList.toggle("moduleOnOff");
	}
	goTo() {
		let module = this.dataset.module;
		this.parentElement.parentElement.parentElement.parentElement.querySelector("#Module_" + module).scrollIntoView();
	}

	set setConfig(config) {
		this.config.showURL = config.showURL ?? this.config.showURL;
	}

	set showData(data) {
		this.showInformations(data);
		this.showSemestre(data);
		this.showSynthese(data);
		this.showEvaluations(data);
		
		this.showCustom(data);

		this.setOptions(data.options);

		this.shadow.querySelectorAll(".CTA_Liste").forEach(e => {
			e.addEventListener("click", this.listeOnOff)
		})
		this.shadow.querySelectorAll(".ue, .module").forEach(e => {
			e.addEventListener("click", this.moduleOnOff)
		})
		this.shadow.querySelectorAll(":not(.ueBonus)+.syntheseModule").forEach(e => {
			e.addEventListener("click", this.goTo)
		})

		this.shadow.children[0].classList.add("ready");
	}

	template() {
		return `
<div>	
	<main class="releve">
		<!--------------------------->
		<!-- Info. étudiant        -->
		<!--------------------------->
		<section class=etudiant>
			<img class=studentPic src="" alt="Photo de l'étudiant" width=100 height=120>
			<div class=infoEtudiant></div>
		</section>

		<!--------------------------------------------------------------------------------------->
		<!-- Zone spéciale pour que les IUT puisse ajouter des infos locales sur la passerelle -->
		<!--------------------------------------------------------------------------------------->
		<section class=custom></section>

		<!--------------------------->
		<!-- Semestre              -->
		<!--------------------------->
		<section>
			<h2>Semestre </h2>
			<div class=flex>
				<div class=infoSemestre></div>
				<div>
					<div class=decision_annee></div>
					<div class=decision></div>
					<div class="ects" id="ects_tot"></div>
					<div class=dateInscription>Inscrit le </div>
					<em>Les moyennes servent à situer l'étudiant dans la promotion et ne correspondent pas à des validations de compétences ou d'UE.</em>
				</div>
			</div>
			
		</section>

		<!--------------------------->
		<!-- Synthèse              -->
		<!--------------------------->
		<section>
			<div>
				<div>
					<h2>Synthèse</h2>
					<em>La moyenne des ressources dans une UE dépend des poids donnés aux évaluations.</em>
				</div>
				<div class=CTA_Liste>
					Liste <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--contenu-inverse)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M18 15l-6-6-6 6" />
					</svg>
				</div>
			</div>
			<div class=synthese></div>
		</section>

		<!--------------------------->
		<!-- Evaluations           -->
		<!--------------------------->
		<section>
			<div>
				<h2>Ressources</h2>
				<div class=CTA_Liste>
					Liste <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--contenu-inverse)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M18 15l-6-6-6 6" />
					</svg>
				</div>
			</div>
			<div class=evaluations></div>
		</section>

		<section>
			<div>
				<h2>SAÉ</h2>
				<div class=CTA_Liste>
					Liste <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--contenu-inverse)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M18 15l-6-6-6 6" />
					</svg>
				</div>
			</div>
			<div class=sae></div>
		</section>

	</main>
</div>`;
	}

	/********************************/
	/* Informations sur l'étudiant  */
	/********************************/
	showInformations(data) {
		//this.shadow.querySelector(".studentPic").src = data.etudiant.photo_url || "default_Student.svg";

		let output = '';

		if (this.config.showURL) {
			output += `<a href="${data.etudiant.fiche_url}" class=info_etudiant>`;
		} else {
			output += `<div class=info_etudiant>`;
		}

		output += `
				<div class=civilite>
					${this.civilite(data.etudiant.civilite)}
					${data.etudiant.nom}
					${data.etudiant.prenom}`;

		if (data.etudiant.date_naissance) {
			output += ` <div class=dateNaissance>né${(data.etudiant.civilite == "F") ? "e" : ""} le ${this.ISOToDate(data.etudiant.date_naissance)}</div>`;
		}

		output += `
				</div>
				<div class=numerosEtudiant>
					Numéro étudiant : ${data.etudiant.code_nip || "~"} - 
					Code INE : ${data.etudiant.code_ine || "~"}
				</div>
				<div>${data.formation.titre}</div>
		`;
		if (this.config.showURL) {
			output += `</a>`;
		} else {
			output += `</div>`;
		}

		this.shadow.querySelector(".infoEtudiant").innerHTML = output;
	}

	/*******************************/
	/* Affichage local             */
	/*******************************/
	showCustom(data) {
		this.shadow.querySelector(".custom").innerHTML = data.custom || "";
	}

	/*******************************/
	/* Information sur le semestre */
	/*******************************/
	showSemestre(data) {
		let correspondanceCodes = {
			"ADM": "Admis",
			"ADJ": "Admis par décision de jury",
			"PASD": "Passage de droit : tout n'est pas validé, mais d'après les règles du BUT, vous passez",
			"PAS1NCI": "Vous passez par décision de jury mais attention, vous n'avez pas partout le niveau suffisant",
			"RED": "Ajourné mais autorisé à redoubler",
			"NAR": "Non admis et non autorisé à redoubler : réorientation",
			"DEM": "Démission",
			"ABAN": "Abandon constaté sans lettre de démission",
			"RAT": "En attente d'un rattrapage",
			"EXCLU": "Exclusion dans le cadre d'une décision disciplinaire",
			"DEF": "Défaillance : non évalué par manque d'assiduité",
			"ABL": "Année blanche"
		}


		this.shadow.querySelector("h2").innerHTML += data.semestre.numero + " - " + data.semestre.groupes[0]?.group_name || "";
		this.shadow.querySelector(".dateInscription").innerHTML += this.ISOToDate(data.semestre.inscription);
		let output = `
			<div>
				<div class=enteteSemestre>Moyenne</div><div class=enteteSemestre>${data.semestre.notes.value}</div>
				<div class=rang>Rang :</div><div class=rang>${data.semestre.rang.value} / ${data.semestre.rang.total}</div>
				<div>Max. promo. :</div><div>${data.semestre.notes.max}</div>
				<div>Moy. promo. :</div><div>${data.semestre.notes.moy}</div>
				<div>Min. promo. :</div><div>${data.semestre.notes.min}</div>
			</div>
			${(() => {
				if ((!data.semestre.rang.groupes) ||
					(Object.keys(data.semestre.rang.groupes).length == 0)) {
					return "";
				}
				let output = "";
				let [idGroupe, dataGroupe] = Object.entries(data.semestre.rang.groupes)[0];
				output += `<div>
					<div class=enteteSemestre>${data.semestre.groupes[0]?.group_name}</div><div></div>
					<div class=rang>Rang :</div><div class=rang>${dataGroupe.value} / ${dataGroupe.total}</div>
					<!--<div>Max. groupe :</div><div>${dataGroupe.max || "-"}</div>
					<div>Moy. groupe :</div><div>${dataGroupe.moy || "-"}</div>
					<div>Min. groupe :</div><div>${dataGroupe.min || "-"}</div>-->
				</div>`;
				return output;
			})()}
			<div class=absencesRecap>
				<div class=enteteSemestre>Absences</div><div class=enteteSemestre>1/2 jour.</div>
				<div class=abs>Non justifiées</div>
				<div>${data.semestre.absences?.injustifie ?? "-"}</div>
				<div class=abs>Total</div><div>${data.semestre.absences?.total ?? "-"}</div>
			</div>`;
		if (data.semestre.decision_rcue?.length) {
			output += `
			<div>
				<div class=enteteSemestre>RCUE</div><div></div>
				${(() => {
					let output = "";
					data.semestre.decision_rcue.forEach(competence => {
						output += `<div class=competence>${competence.niveau.competence.titre}</div><div>${competence.code}</div>`;
					})
					return output;
				})()}
				</div>
			</div>`
		}
		if (data.semestre.decision_ue?.length) {
			output += `
			<div>
				<div class=enteteSemestre>UE</div><div></div>
				${(() => {
					let output = "";
					data.semestre.decision_ue.forEach(ue => {
						output += `<div class=competence>${ue.acronyme}</div><div>${ue.code}</div>`;
					})
					return output;
				})()}
				</div>
			</div>`
		}
		/*${data.semestre.groupes.map(groupe => {
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
		}*/
		this.shadow.querySelector(".infoSemestre").innerHTML = output;

		if (data.semestre.decision_annee?.code) {
			this.shadow.querySelector(".decision_annee").innerHTML = "Décision année : " + data.semestre.decision_annee.code + " - " + correspondanceCodes[data.semestre.decision_annee.code];
		}

		this.shadow.querySelector(".decision").innerHTML = data.semestre.situation || "";
		/*if (data.semestre.decision?.code) {
			this.shadow.querySelector(".decision").innerHTML = "Décision jury: " + (data.semestre.decision?.code || "");
		}*/
		this.shadow.querySelector("#ects_tot").innerHTML = "ECTS&nbsp;:&nbsp;" + (data.semestre.ECTS?.acquis ?? "-") + "&nbsp;/&nbsp;" + (data.semestre.ECTS?.total ?? "-");
	}

	/*******************************/
	/* Synthèse                    */
	/*******************************/
	showSynthese(data) {
		let output = ``;
		/* Fusion et tri des UE et UE capitalisées */
		let fusionUE = [
			...Object.entries(data.ues),
			...Object.entries(data.ues_capitalisees)
		].sort((a, b) => {
			return a[1].numero - b[1].numero
		});

		/* Affichage */
		fusionUE.forEach(([ue, dataUE]) => {
			if (dataUE.type == 1) {		// UE Sport / Bonus
				output += `
					<div>
						<div class="ue ueBonus">
							<h3>Bonus</h3>
							<div>${dataUE.bonus_description}</div>
						</div>
						${this.ueSport(dataUE.modules)}
					</div>
				`;
			} else {
				output += `
					<div>
						<div class="ue ${dataUE.date_capitalisation?"capitalisee":""}">
							<h3>
								${ue}${(dataUE.titre) ? " - " + dataUE.titre : ""}
							</h3>
							<div>
								<div class=moyenne>Moyenne&nbsp;:&nbsp;${dataUE.moyenne?.value || dataUE.moyenne || "-"}</div>
								<div class=ue_rang>Rang&nbsp;:&nbsp;${dataUE.moyenne?.rang}&nbsp;/&nbsp;${dataUE.moyenne?.total}</div>
								<div class=info>`;
				if(!dataUE.date_capitalisation){		
					output += `		Bonus&nbsp;:&nbsp;${dataUE.bonus || 0}&nbsp;- `;
					if(dataUE.malus >= 0) {
						output += `Malus&nbsp;:&nbsp;${dataUE.malus || 0}`;
					} else {
						output += `Bonus&nbsp;complémentaire&nbsp;:&nbsp;${-dataUE.malus || 0}`;
					}
									
				} else {
					output += `		le ${this.ISOToDate(dataUE.date_capitalisation.split("T")[0])} <a href="${dataUE.bul_orig_url}">dans ce semestre</a>`;
				}

				output += `			<span class=ects>&nbsp;-
										ECTS&nbsp;:&nbsp;${dataUE.ECTS?.acquis ?? "-"}&nbsp;/&nbsp;${dataUE.ECTS?.total ?? "-"}
									</span>
								</div>
							</div>`;
				/*<div class=absences>
					<div>Abs&nbsp;N.J.</div><div>${dataUE.absences?.injustifie || 0}</div>
					<div>Total</div><div>${dataUE.absences?.total || 0}</div>
				</div>*/
				output += "</div>";

				if(!dataUE.date_capitalisation){
					output += 
						this.synthese(data, dataUE.ressources) +
						this.synthese(data, dataUE.saes);
				}
						
				output += "</div>";
			}
		});
		this.shadow.querySelector(".synthese").innerHTML = output;
	}
	synthese(data, modules) {
		let output = "";
		Object.entries(modules).forEach(([module, dataModule]) => {
			let titre = data.ressources[module]?.titre || data.saes[module]?.titre;
			//let url = data.ressources[module]?.url || data.saes[module]?.url;
			output += `
				<div class=syntheseModule data-module="${module.replace(/[^a-zA-Z0-9]/g, "")}">
					<div>${module}&nbsp;- ${titre}</div>
					<div>
						${dataModule.moyenne}
						<em>Coef.&nbsp;${dataModule.coef}</em>
					</div>
				</div>
			`;
		})
		return output;
	}
	ueSport(modules) {
		let output = "";
		Object.values(modules).forEach((module) => {
			Object.values(module.evaluations).forEach((evaluation) => {
				output += `
					<div class=syntheseModule>
						<div>${module.titre} - ${evaluation.description || "Note"}</div>
						<div>
							${evaluation.note.value ?? "-"}
							<em>Coef.&nbsp;${evaluation.coef}</em>
						</div>
					</div>
				`;
			})
		})
		return output;
	}

	/*******************************/
	/* Evaluations                 */
	/*******************************/
	showEvaluations(data) {
		this.shadow.querySelector(".evaluations").innerHTML = this.module(data.ressources);
		this.shadow.querySelector(".sae").innerHTML += this.module(data.saes);

		const newEvals = this.shadow.querySelectorAll(".new-eval");
		newEvals.forEach(el => {
			el.addEventListener("click", () => this.addSeenEvaluation(el));
		});
	}

	getSeenEvaluations()
	{		
		const seenEvaluations = localStorage.getItem("seenEvaluations");
		if(seenEvaluations !== null){
			const seenEvaluationsParsed = JSON.parse(seenEvaluations);
			if(Array.isArray(seenEvaluationsParsed)){
				return seenEvaluationsParsed;
			}
		}
		return [];
	}

	addSeenEvaluation(el)
	{
		const seenEvaluations = this.getSeenEvaluations();
		seenEvaluations.push({
			id: el.dataset.id,
			note: el.dataset.note
		});
		localStorage.setItem("seenEvaluations", JSON.stringify(seenEvaluations));
		el.classList.remove("new-eval");
	}

	removeSeenEvaluation(index){
		const seenEvaluations = this.getSeenEvaluations();
		seenEvaluations.splice(index, 1);
		localStorage.setItem("seenEvaluations", JSON.stringify(seenEvaluations));
	}

	isNewEvaluation(evaluation)
	{
		const seenEvaluations = this.getSeenEvaluations();
		const index = seenEvaluations.findIndex(e => parseInt(e.id) === evaluation.id);
		if(index === -1){
			return true;
		}
		if(seenEvaluations[index].note !== evaluation.note.value){
			this.removeSeenEvaluation(index);
			return true;
		}
		return false;
	}

	module(module) {
		let output = "";
		Object.entries(module).forEach(([numero, content]) => {
			output += `
				<div id="Module_${numero.replace(/[^a-zA-Z0-9]/g, "")}">
					<div class=module>
						<h3>${this.URL(content.url, `${numero} - ${content.titre}`)}</h3>
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
					${this.evaluation(content.evaluations)}
				</div>
			`;
		})
		return output;
	}

	evaluation(evaluations) {
		let output = "";
		evaluations.forEach((evaluation) => {
			const isNewEvaluation = this.isNewEvaluation(evaluation);
			output += `
				<div class="eval ${isNewEvaluation ? "new-eval" : ""}" data-id="${evaluation.id}" data-note="${evaluation.note.value}">
					<div>${this.URL(evaluation.url, evaluation.description || "Évaluation")}</div>
					<div>
						${evaluation.note.value}
						<em>Coef.&nbsp;${evaluation.coef}</em>
					</div>
					<div class=complement>
						<div>Coef</div><div>${evaluation.coef}</div>
						<div>Max. promo.</div><div>${evaluation.note.max}</div>
						<div>Moy. promo.</div><div>${evaluation.note.moy}</div>
						<div>Min. promo.</div><div>${evaluation.note.min}</div>
						${Object.entries(evaluation.poids).map(([UE, poids]) => {
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
	/* Options          */
	/********************/
	setOptions(options) {
		Object.entries(options).forEach(([option, value]) => {
			if (value === false) {
				this.shadow.children[0].classList.add(option.replace("show", "hide"))
			}
		})
	}


	/********************/
	/* Fonctions d'aide */
	/********************/
	URL(href, content) {
		if (this.config.showURL) {
			return `<a href=${href}>${content}</a>`;
		} else {
			return content;
		}
	}
	civilite(txt) {
		switch (txt) {
			case "M": return "M.";
			case "F": return "Mme";
			default: return "";
		}
	}

	ISOToDate(ISO) {
		return ISO.split("-").reverse().join("/");
	}

}
customElements.define('releve-but', releveBUT);
