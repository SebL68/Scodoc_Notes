<header>
	<h1><?php echo $h1; ?></h1>

	<nav>
		<svg onclick="this.parentElement.parentElement.classList.toggle('ouvert')" xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="var(--primaire-contenu)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>

		<button>
			<svg width="42" height="42" stroke-width="2" viewBox="0 0 24 24" onclick="toggleTheme()" fill="none"
				id="theme-clair" xmlns="http://www.w3.org/2000/svg" stroke="var(--primaire-contenu)" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<path
					d="M12 18a6 6 0 100-12 6 6 0 000 12zM22 12h1M12 2V1M12 23v-1M20 20l-1-1M20 4l-1 1M4 20l1-1M4 4l1 1M1 12h1">
				</path>
			</svg>
			<svg width="42" height="42" stroke-width="2" viewBox="0 0 24 24" onclick="toggleTheme()" fill="none"
				id="theme-sombre" xmlns="http://www.w3.org/2000/svg" stroke="var(--primaire-contenu)" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<path
					d="M3 11.507a9.493 9.493 0 0018 4.219c-8.507 0-12.726-4.22-12.726-12.726A9.494 9.494 0 003 11.507z">
				</path>
			</svg>
		</button>

		<a class="nav" id="notes" href="/">Notes</a>
		<a class="nav" id="documents" href="/services/studentsLists.php">Documents</a>

		<a class="nav" id="absences" href="/absences/">Absences</a>
		<a class="nav" id="gestion" href="/absences/gestion.php">Stats / Justif</a>

		<a class="nav" id="admin" href="/admin/">Comptes</a>
		<a class="nav" id="config" href="/admin/config.php">Config</a>

		<a href=/logout.php>Déconnexion</a>
	</nav>
</header>
