/* Module par Seb. L. */
class releveBUT extends HTMLElement {
	constructor(){
		super();
		this.shadow = this.attachShadow({mode: 'open'});

		/* Config par defaut */
		this.config = {
			showURL: true
		};
		
		/* Template du module */
		this.shadow.innerHTML = this.template();
		
		/* Style du module */
		const styles = document.createElement('link');
		styles.setAttribute('rel', 'stylesheet');
		styles.setAttribute('href', '/assets/styles/releve-but.css');

		this.shadow.appendChild(styles);	
	}
	listeOnOff() {
		this.parentElement.parentElement.classList.toggle("listeOff");
		this.parentElement.parentElement.querySelectorAll(".moduleOnOff").forEach(e=>{
			e.classList.remove("moduleOnOff")
		})
	}
	moduleOnOff(){
		this.parentElement.classList.toggle("moduleOnOff");
	}

	set setConfig(config){
		this.config.showURL = config.showURL ?? this.config.showURL;
	}

	set showData(data) {	
		this.showInformations(data);
		this.showSemestre(data);
		this.showSynthese(data);
		this.showEvaluations(data);

		this.setOptions(data.options);

		this.shadow.querySelectorAll(".CTA_Liste").forEach(e => {
			e.addEventListener("click", this.listeOnOff)
		})	
		this.shadow.querySelectorAll(".ue, .module").forEach(e => {
			e.addEventListener("click", this.moduleOnOff)
		})

		this.shadow.children[0].classList.add("ready");
	}

	template(){
		return `
<div>	
	<div class="wait"></div>
	<main class="releve">
		<!--------------------------->
		<!-- Info. étudiant        -->
		<!--------------------------->
		<section class=etudiant>
			<img class=studentPic src="" alt="Photo de l'étudiant" width=100 height=120>
			<div class=infoEtudiant></div>
		</section>
		<!--------------------------->
		<!-- Semestre              -->
		<!--------------------------->
		<section>
			<h2>Semestre </h2>
			<div class=dateInscription>Inscrit le </div>
			<em>Les moyennes servent à situer l'étudiant dans la promotion et ne correspondent pas à des validations de
				compétences ou d'UE.</em>
			<div class=infoSemestre></div>
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
					Liste <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
						stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
					Liste <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
						stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
					Liste <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
						stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
		this.shadow.querySelector(".studentPic").src = data.etudiant.photo_url || "default_Student.svg";

		let output = `
			<div class=info_etudiant>
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
					Numéro étudiant : ${data.etudiant.code_nip} - 
					Code INE : ${data.etudiant.code_ine}
				</div>
				<div>${data.formation.titre}</div>
			</div>
		`;

		this.shadow.querySelector(".infoEtudiant").innerHTML = output;
	}

	/*******************************/
	/* Information sur le semestre */
	/*******************************/
	showSemestre(data) {
		this.shadow.querySelector("h2").innerHTML += data.semestre.numero;
		this.shadow.querySelector(".dateInscription").innerHTML += this.ISOToDate(data.semestre.inscription);
		let output = `
			<div>
				<div class=enteteSemestre>Moyenne</div><div class=enteteSemestre>${data.semestre.notes.value}</div>
				<div class=rang>Rang :</div><div class=rang>${data.semestre.rang.value} / ${data.semestre.rang.total}</div>
				<div>Max. promo. :</div><div>${data.semestre.notes.max}</div>
				<div>Moy. promo. :</div><div>${data.semestre.notes.moy}</div>
				<div>Min. promo. :</div><div>${data.semestre.notes.min}</div>
			</div>
			${data.semestre.groupes.map(groupe => {
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
		this.shadow.querySelector(".infoSemestre").innerHTML = output;
	}

	/*******************************/
	/* Synthèse                    */
	/*******************************/
	showSynthese(data) {
		let output = ``;
		Object.entries(data.ues).forEach(([ue, dataUE]) => {
			output += `
				<div>
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
							<div>Abs&nbsp;N.J.</div><div>${dataUE.absences?.injustifie || 0}</div>
							<div>Total</div><div>${dataUE.absences?.total || 0}</div>
						</div>
					</div>
					${this.synthese(data, dataUE.ressources)}
					${this.synthese(data, dataUE.saes)}
				</div>
			`;
		});
		this.shadow.querySelector(".synthese").innerHTML = output;
	}
	synthese(data, modules) {
		let output = "";
		Object.entries(modules).forEach(([module, dataModule]) => {
			let titre = data.ressources[module]?.titre || data.saes[module]?.titre;
			let url = data.ressources[module]?.url || data.saes[module]?.url;
			output += `
				<div class=syntheseModule>
					<div>${this.URL(url, `${module}&nbsp;- ${titre}`)}</div>
					<div>
						${dataModule.moyenne}
						<em>Coef.&nbsp;${dataModule.coef}</em>
					</div>
				</div>
			`;
		})
		return output;
	}
	
	/*******************************/
	/* Evaluations                 */
	/*******************************/
	showEvaluations(data) {
		this.shadow.querySelector(".evaluations").innerHTML = this.module(data.ressources);
		this.shadow.querySelector(".sae").innerHTML += this.module(data.saes);
	}
	module(module) {
		let output = "";
		Object.entries(module).forEach(([numero, content]) => {
			output += `
				<div>
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
			output += `
				<div class=eval>
					<div>${this.URL(evaluation.url, evaluation.description)}</div>
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
				document.body.classList.add(option.replace("show", "hide"))
			}
		})
	}


	/********************/
	/* Fonctions d'aide */
	/********************/
	URL(href, content){
		if(this.config.showURL){
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