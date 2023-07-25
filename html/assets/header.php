<header>
	<h1><?php echo $h1; ?></h1>

	<nav>
		<svg onclick="this.parentElement.parentElement.classList.toggle('ouvert')" xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="var(--primaire-contenu)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>

		<a class="nav" id="notes" href="/">Notes</a>
		<a class="nav" id="documents" href="/services/studentsLists.php">Documents</a>

		<a class="nav" id="absences" href="/absences/">Absences</a>
		<a class="nav" id="gestion" href="/absences/gestion.php">Stats / Justif</a>

		<a class="nav" id="admin" href="/admin/">Comptes</a>
		<a class="nav" id="config" href="/admin/config.php">Config</a>

		<a href=/logout.php>Déconnexion</a>
	</nav>

	<div class="theme" onclick="toggleTheme()">
		<div class=cercle></div>
		<div class=logo>
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0b0b0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
			
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0b0b0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
		</div>
	</div>
</header>
