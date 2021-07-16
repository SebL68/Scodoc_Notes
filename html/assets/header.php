<header>
	<h1><?php echo $h1; ?></h1>

	<nav>
		<svg onclick="this.parentElement.parentElement.classList.toggle('ouvert')" xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>

		<a class="nav" id="notes" href="/">Notes</a>
		<a class="nav" id="documents" href="/services/studentsLists.php">Documents</a>
		<a class="nav" id="absences" href="/absences/">Absences</a>
		<a class="nav" id="gestion" href="/absences/gestion.php">Stats / Justif</a>
		<a class="nav" id="admin" href="/admin/">Comptes</a>
		<a href=/logout.php>Déconnexion</a>
	</nav>
</header>