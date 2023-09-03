if(localStorage.getItem("theme")) {
	document.body.classList.add(localStorage.getItem("theme"));
} else if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
	document.body.classList.add("dark");
}

function toggleTheme() {
	if (document.body.classList.toggle("dark")) {
		localStorage.setItem("theme", "dark");
	} else {
		localStorage.setItem("theme", "light");
	}
}