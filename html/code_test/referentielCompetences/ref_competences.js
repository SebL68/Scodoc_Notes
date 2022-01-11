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

	}

	competences(event, cle) {
		this.shadow.querySelector(".parcours>.focus")?.classList.remove("focus");
		event.currentTarget.classList.add("focus");

		/* Récupère une liste plate de toutes les compentences de toutes les années */
		let bucketCompetences = [
			Object.keys(this.data.parcours[
				Object.keys(this.data.parcours)[0]
			].annees[1].competences)
			, Object.keys(this.data.parcours[cle].annees[2].competences)
			, Object.keys(this.data.parcours[cle].annees[3].competences)
		].flat();

		/* Compte le nombre d'occurence de chaque compétence */
		let competences = {};
		bucketCompetences.forEach(competence => {
			competences[competence] = ++competences[competence] || 1
		})

		/* Affichage */
		let numComp = 1;
		this.shadow.querySelector(".competences").innerHTML = "";
		Object.entries(competences).forEach(([competence, nb]) => {
			var divCompetence3ans = document.createElement("div");
			divCompetence3ans.className = "competence";
			let numTmp = numComp;
			for (let i = 0; i < nb; i++) {
				let divCompetence = document.createElement("div");
				divCompetence.innerText = `${competence} ${i + 1}`;
				divCompetence.className = "comp" + numTmp;
				divCompetence.dataset.competence = `${competence} ${i + 1}`;
				divCompetence.addEventListener("click", (event) => { this.AC(event, competence, numTmp, i + 1) })
				divCompetence3ans.appendChild(divCompetence);
			}
			this.shadow.querySelector(".competences").appendChild(divCompetence3ans);
			numComp++;
		})

		this.shadow.querySelectorAll(".AC").forEach(ac => {
			this.shadow.querySelector(`[data-competence="${ac.dataset.competence}"]`).classList.add("focus");
		});
	}

	AC(event, competence, numComp, annee) {
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