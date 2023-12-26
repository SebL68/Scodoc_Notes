class releveDUT extends HTMLElement {
	constructor(){
		super();
		this.shadow = this.attachShadow({mode: 'open'});
		
		/* Style du module */
		const styles = document.createElement('link');
		styles.setAttribute('rel', 'stylesheet');
		styles.setAttribute('href', 'assets/styles/releve-dut.css');
		this.shadow.appendChild(styles);		
	}

	set showData([data, semestre, etudiant]) {
		this.feedReportCards(data, semestre, etudiant)
	}

	set hidePDF(statut){
		this.shadow.querySelector("form").style.display = "none";
	}

	feedReportCards(data, semestre, etudiant){
		let output = `
			<main>
				<form action=services/bulletin_PDF.php?type=DUT&sem_id=${semestre}&etudiant=${etudiant} target=_blank method=post>
					<button type=submit>Télécharger le relevé au format PDF</button>
				</form>
			${this.showInformations(data)}
		`;

		if(data.rang_group[0]?.group_name){
			output += `<div class="total">Groupe ${data.rang_group[0].group_name}</div>`;
		}

		let decision = data.situation?.split(". ") || [];
		if(decision[1]){
			decision = "<b>"+decision[1] + ". " + decision[2]+"</b><br>";
		}else{
			decision = "";
		}
		output += `
				<div class="total">
					${decision}
					Moyenne semestre : ${data.note.value} <br>
					Rang : ${data.rang.value || "Attente"} / ${data.rang.ninscrits} <br>
					<span>Classe : ${data.note.moy} - Max : ${data.note.max} - Min : ${data.note.min}</span>
				</div>
				${this.ue(data.ue)}
				<div class=button>Montrer les évaluations sans note</div>
			</main>`;

		this.shadow.innerHTML += output;
		this.shadow.querySelector(".button").addEventListener("click", this.ShowEmpty);
		this.shadow.querySelectorAll(".ue").forEach(ue=>{
			ue.addEventListener("click", this.openClose);
		})

		/*if(document.querySelector("body").classList.contains('etudiant')){
			set_checked();
		}*/
	}

/********************************/
/* Informations sur l'étudiant  */
/********************************/
	showInformations(data) {
		let output = `
			<section class=etudiant>
				<img class=studentPic src="services/data.php?q=getStudentPic&nip=${data.etudiant.code_nip}" alt="Photo de l'étudiant" width=100 height=120>
				<div class=infoEtudiant>
					<div class=civilite>
						${data.etudiant.sexe}
						${data.etudiant.nom}
						${data.etudiant.prenom}
					</div>
					<div class=numerosEtudiant>
						Numéro étudiant : ${data.etudiant.code_nip || "~"} - 
						Code INE : ${data.etudiant.code_ine || "~"}
					</div>
				</div>
			</section>
		`;	
		
		return output;
	}

/**************************/
/* Création des bloques UE
/**************************/
	ue(ue){
		let output = "";
		ue.forEach(e=>{
			output += `
				<div class=ue data-id="${e.acronyme}">
					<div>${e.acronyme} - ${e.titre}</div>
					<div>
						Moyenne&nbsp;:&nbsp;${e.note.value}<br>Rang&nbsp;:&nbsp;${e.rang}
					</div>
				</div>
				${this.module(e.module)}`;
		})
		return output;
	}

/**************************/
/* Création des bloques modules
/**************************/
	module(module){
		let output = "";
		module.forEach(e=>{
			output += `
				<div class=module data-id="${e.code}">
					<div>
						<div>${e.titre}<span class=coef>Coef ${e.coefficient}</span></div>
						<div>
							Moyenne&nbsp;:&nbsp;${e.note.value} - Rang&nbsp;:&nbsp;${e.rang?.value || "-"}<br>
							<span>
								Classe&nbsp;:&nbsp;${e.note.moy} - Max&nbsp;:&nbsp;${e.note.max} - Min&nbsp;:&nbsp;${e.note.min}
							</span>
						</div>
					</div>
					
					${this.evaluation(e.evaluation)}
				</div>`;
		})
		return output;
	}

/**************************/
/* Création des evaluations
/**************************/
	evaluation(evaluation){
		let output = "";
		evaluation.forEach(e=>{
			output += `
				<div class=eval data-id="${e.description.replaceAll("&apos;", "")}" data-note=${e.note}>
					<div>${e.description}</div>
					<div>${e.note ?? "~"}&nbsp;<span class=coef>Coef&nbsp;${e.coefficient}</span></div>
				</div>`;
		})
		return output;
	}

/**************************/
/* Ouvrir / fermer les UE
/**************************/
	openClose(){
		let element = this;
		while(element.nextElementSibling && element.nextElementSibling.classList.contains("module")){
			element = element.nextElementSibling;
			element.classList.toggle("hide");
		}
	}

/**************************/
/* Afficher / masquer les évaluations sans notes
/**************************/
	ShowEmpty(){
		this.parentElement.classList.toggle("ShowEmpty");
	}

/********************/
/* Fonctions d'aide */
/********************/
	ISOToDate(ISO) {
		return ISO.split("-").reverse().join("/");
	}

}
customElements.define('releve-dut', releveDUT);
