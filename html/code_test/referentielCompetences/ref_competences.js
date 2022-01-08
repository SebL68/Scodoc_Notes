class ref_competences extends HTMLElement {
	constructor(){
		super();
		this.shadow = this.attachShadow({mode: 'open'});
		
		/* Tempalate de base */
		this.shadow.innerHTML = `
			<div class=parcours></div>
			<div class=competences></div>
		`;

		/* Style du module */
		const styles = document.createElement('link');
		styles.setAttribute('rel', 'stylesheet');
		styles.setAttribute('href', 'ref-competences.css');

		this.shadow.appendChild(styles);
	}
	
	set setData(data){
		this.parcours(data);
	}

	parcours(data){
		let parcoursDIV = this.shadow.querySelector(".parcours");
		Object.entries(data.parcours).forEach(([cle, parcours])=>{
			let div = document.createElement("div");
			div.innerText = parcours.libelle;
			div.addEventListener("click", ()=>{this.competences(data, cle)})
			parcoursDIV.appendChild(div);
		})
		
	}

	competences(data, cle){
		let output = "";

		Object.values(data.parcours[cle].annees).forEach(annee=>{
			output += `<div class=annee>`;

			Object.keys(annee.competences).forEach(competence=>{
				output += `<div>${competence}</div>`;
			});

			output += `</div>`;
		})


		this.shadow.querySelector(".competences").innerHTML = output;
	}

}
customElements.define('ref-competences', ref_competences);