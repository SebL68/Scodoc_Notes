class ref_competences extends HTMLElement {
	constructor() {
		super();
		this.shadow = this.attachShadow({ mode: 'open' });

		/* Tempalate de base */
		this.shadow.innerHTML = `
			<h1></h1>
			<div class=parcours></div>
			<div class=competences></div>
			<div class=ACs></div>
		`;

		/* Style du module */
		const styles = document.createElement('link');
		styles.setAttribute('rel', 'stylesheet');
		if (location.href.split("/")[3] == "ScoDoc") {
			styles.setAttribute('href', '/ScoDoc/static/css/ref-competences.css');
		} else {
			styles.setAttribute('href', 'ref-competences.css');
		}

		this.shadow.appendChild(styles);
	}

	set setData(data) {
		this.data = data;
		this.shadow.querySelector("h1").innerText = "BUT " + data.specialite_long;
		this.parcours();
	}

	parcours() {
		let parcoursDIV = this.shadow.querySelector(".parcours");
		Object.entries(this.data.parcours).forEach(([cle, parcours]) => {
			let div = document.createElement("div");
			div.innerText = parcours.libelle;
			div.addEventListener("click", (event) => { this.competences(event, cle) })
			parcoursDIV.appendChild(div);
		})
		this.initCompetences();
	}

	initCompetences(){
		this.competencesNumber = {};
		let gridTemplate = "";
		let i = 1;
		Object.keys(this.data.competences).forEach(competence=>{
			gridTemplate += `[${competence}] 1fr`;
			this.competencesNumber[competence] = i++;
		})
		this.shadow.querySelector(".competences").style.gridTemplateColumns = gridTemplate;
	}

	competences(event, cle){
		this.shadow.querySelector(".parcours>.focus")?.classList.remove("focus");
		event.currentTarget.classList.add("focus");
		let divCompetences = this.shadow.querySelector(".competences");

		this.shadow.querySelector(".competences").innerHTML = "";

		Object.entries(this.data.parcours[cle].annees).forEach(([annee, dataAnnee])=>{
			Object.entries(dataAnnee.competences).forEach(([competence, niveauCle])=>{
				let numComp = this.competencesNumber[competence];
				let divCompetence = document.createElement("div");
				divCompetence.innerText = `${competence} ${niveauCle.niveau}`;
				divCompetence.style.gridRowStart = annee;
				divCompetence.style.gridColumnStart = competence;
				divCompetence.className = "comp" + numComp;
				divCompetence.dataset.competence = `${competence} ${niveauCle.niveau}`;
				divCompetence.addEventListener("click", (event) => { this.AC(event, competence, niveauCle.niveau, annee, numComp) })
				divCompetences.appendChild(divCompetence);
			})
		})

		/* Réaffectation des focus */
		this.shadow.querySelectorAll(".AC").forEach(ac => {
			this.shadow.querySelector(`[data-competence="${ac.dataset.competence}"]`).classList.add("focus");
		});
	}

	AC(event, competence, niveau, annee, numComp){
		event.currentTarget.classList.toggle("focus");
		if (this.shadow.querySelector(`.ACs [data-competence="${competence} ${niveau}"]`)) {
			this.shadow.querySelector(`.ACs [data-competence="${competence} ${niveau}"]`).remove();
		} else {
			let output = `
				<ul class=AC data-competence="${competence} ${niveau}">
					<h2 class=comp${numComp}>${competence} ${niveau}</h2>
			`;
			Object.entries(this.data.competences[competence].niveaux["BUT" + annee].app_critiques).forEach(([num, contenu]) => {
				output += `<li><div class=comp${numComp}>${num}</div><div>${contenu.libelle}</div></li>`;
			})
			this.shadow.querySelector(".ACs").innerHTML += output + "</ul>";
		}
	}
	ACSave(event, competence, numComp, annee) {
		event.currentTarget.classList.toggle("focus");
		if (this.shadow.querySelector(`.ACs [data-competence="${competence} ${annee}"]`)) {
			this.shadow.querySelector(`.ACs [data-competence="${competence} ${annee}"]`).remove();
		} else {
			let output = `
				<ul class=AC data-competence="${competence} ${annee}">
					<h2 class=comp${numComp}>${competence} ${annee}</h2>
			`;
			Object.entries(this.data.competences[competence].niveaux["BUT" + annee].app_critiques).forEach(([num, contenu]) => {
				output += `<li><div class=comp${numComp}>${num}</div><div>${contenu.libelle}</div></li>`;
			})
			this.shadow.querySelector(".ACs").innerHTML += output + "</ul>";
		}

	}
}
customElements.define('ref-competences', ref_competences);