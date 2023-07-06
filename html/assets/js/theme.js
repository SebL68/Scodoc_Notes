let theme = localStorage.getItem("theme");

function setTheme(theme) {
	localStorage.setItem("theme", theme);
	document.documentElement.setAttribute("data-theme", theme);
}

function toggleTheme() {
	if (theme === "light") {
		setTheme("dark");
	} else {
		setTheme("light");
	}
}

if (!theme) {
	if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
		theme = "dark";
		localStorage.setItem("theme", theme);
		document.documentElement.setAttribute("data-theme", theme);
	} else {
		theme = "light";
		localStorage.setItem("theme", theme);
		document.documentElement.setAttribute("data-theme", theme);
	}
} else {
	document.documentElement.setAttribute("data-theme", theme);
}