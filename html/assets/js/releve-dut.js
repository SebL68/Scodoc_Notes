class releveDUT extends HTMLElement {
	constructor(){
		super();
		this.shadow = this.attachShadow({mode: 'open'});
		
		/* Style du module */
		const styles = document.createElement('link');
		styles.setAttribute('rel', 'stylesheet');
		styles.setAttribute('href', '/assets/styles/releve-dut.css');
		this.shadow.appendChild(styles);		
	}

	set showData([data, semestre, etudiant]) {
		this.feedReportCards(data, semestre, etudiant)
	}

	set hidePDF(statut){
		this.shadow.querySelector("form").style.display = "none";
	}

	feedReportCards(data, semestre, etudiant){
		this.shadow.innerHTML += `
			<form action=services/bulletin_PDF.php?sem_id=${semestre}&etudiant=${etudiant} target=_blank method=post>
				<button type=submit>Télécharger le relevé au format PDF</button>
			</form>
		`;

		if(data.rang_group[0]?.group_name){
			this.shadow.innerHTML += `<div class="total">Groupe ${data.rang_group[0].group_name}</div>`;
		}
		

		let decision = data.situation?.split(". ") || [];
		if(decision[1]){
			decision = "<b>"+decision[1] + ". " + decision[2]+"</b><br>";
		}else{
			decision = "";
		}
		this.shadow.innerHTML += `
			<div class="total">
				${decision}
				Moyenne semestre : ${data.note.value} <br>
				Rang : ${data.rang.value || "Attente"} / ${data.rang.ninscrits} <br>
				<span>Classe : ${data.note.moy} - Max : ${data.note.max} - Min : ${data.note.min}</span>
			</div>
			${this.ue(data.ue)}`;

		/*if(document.querySelector("body").classList.contains('etudiant')){
			set_checked();
		}*/
	}

/**************************/
/* Création des bloques UE
/**************************/
	ue(ue){
		let output = "";
		ue.forEach(e=>{
			output += `
				<div class=ue data-id="${e.acronyme}" onclick="openClose(this)">
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
				<div class=eval onclick="check_eval(this)" data-id="${e.description.replaceAll("&apos;", "")}" data-note=${e.note}>
					<div>${e.description}</div>
					<div>${e.note}&nbsp;<span class=coef>Coef&nbsp;${e.coefficient}</span></div>
				</div>`;
		})
		return output;
	}

}
customElements.define('releve-dut', releveDUT);