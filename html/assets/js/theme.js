if (window.matchMedia("(prefers-color-scheme: dark)").matches || localStorage.getItem("theme") == "dark") {
	document.body.classList.add("dark");
}

function toggleTheme() {
	if (document.body.classList.toggle("dark")) {
		localStorage.setItem("theme", "dark");
	} else {
		localStorage.removeItem("theme");
	}
}