#!/bin/bash
# Contribution de Franck Butelle 23/04/2022

SETCOLOR_FAILURE="\\033[1;31m"
SETCOLOR_SUCCESS="\\033[1;32m"
SETCOLOR_WARNING="\\033[1;33m"
SETCOLOR_NORMAL="\\033[0;39m"
success() {
    echo -e "${SETCOLOR_SUCCESS}$*${SETCOLOR_NORMAL}"
}
error() {
    echo -e "${SETCOLOR_FAILURE}$*${SETCOLOR_NORMAL}"
    exit 1
}
warn() {
    echo -e "${SETCOLOR_WARNING}$*${SETCOLOR_NORMAL}"
}

INSTALLDIR=/var/www

if [ $# = 1 ]; then 
	if [ $1 = '-h' ] || [ $1 = '--help' ]; then
		warn "Usage: $0 [repertoire_d_installation]"
		echo "par défaut le répertoire d'installation est $INSTALLDIR"
    	exit 0
	else
		INSTALLDIR="$1"
	fi
fi   

cd "$INSTALLDIR"

echo
success '+----------------------------------------------------------+'
success "| Script d'installation et de mise à jour de la passerelle |"
success '+----------------------------------------------------------+'

if $(which dpkg) -L apt  2>/dev/null | grep -q $(which apt-get); then
	success 'Système Debian ou Ubuntu - compatible avec le script'
else
	error 'Désolé ce script est conçu pour une distribution Debian ou Ubuntu ! Vous pouvez vous inspirer du script et de la documentation pour installer sur un autre système'
fi

echo

warn ' *** Sauvegarde de la favicon et des icones ***'
warn ' *** Mise à jour uniquement des dossiers html, includes et lib ***'

mv html/favicon.ico .
mv html/images/icons .

echo
warn " *** Récupération de l'archive sur git ... ***"
wget -q https://github.com/SebL68/Scodoc_Notes/archive/refs/heads/main.zip
success  '     ==> Fait'

echo
warn " *** Extraction de l'archive ... ***"
unzip -q main.zip
rm -rf html includes lib 
mv Scodoc_Notes-main/html .
mv Scodoc_Notes-main/includes .
mv Scodoc_Notes-main/lib .

success  '     ==> Fait'

echo
warn ' *** Restauration de la favicon et des icones ***'
mv favicon.ico html/favicon.ico
rm -rf html/images/icons
mv icons html/images/icons
success  '     ==> Fait'

echo
success '+------------------------------------------------+'
success '| Mise à jour terminée, toutes nos félicitations |'
success '+------------------------------------------------+'
echo

#Nettoyage
chown -R www-data html includes lib
rm -rf Scodoc_Notes-main main.zip