#!/bin/bash

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

echo
echo '+-----------------------------------------------------+'
echo "| Script d'installation de la passerelle Scodoc_Notes |"
echo '+-----------------------------------------------------+'
echo

if [ $USER != root ]; then
        error "merci de faire sudo $0"
fi

warn '   Attention ce script est fourni SANS garantie'

if $(which dpkg) -L apt  2>/dev/null | grep -q $(which apt-get); then
	echo ' -- Ok on est bien sur une debian ou ubuntu ou similaire'
else
	error 'Désolé ce script est conçu pour une distribution Debian ou Ubuntu !'
fi

if [ -d /var/www/config ]; then
	warn 'Une installation de Scodoc_Notes semble déjà présente'
	echo -n 'o: pour la supprimer, n: pour la mettre à jour :'
	read rep
	if [ "A$rep" = "Ao" ] || [ "A$rep" = "AO" ]; then 
		echo "choix d'écraser l'installation existante"
		doinstall=1
	fi
else
	doinstall=1
fi

success "Récupération de l'archive"
wget -q https://github.com/SebL68/Scodoc_Notes/archive/refs/heads/main.zip
mv main.zip /var/www
cd /var/www

success "extraction de l'archive"
unzip -q main.zip
rm -rf html includes lib 
mv  Scodoc_Notes-main/html .
mv  Scodoc_Notes-main/includes .
mv  Scodoc_Notes-main/lib .

if [ $doinstall ]; then
	rm -rf data config
	mv Scodoc_Notes-main/data .
	mv Scodoc_Notes-main/config .

	success 'Patience, recherche et installation des packages nécessaires'
	
	apt -yq update
	apt -yq install openssl apache2 wget unzip links
	apt -yq install php php-curl php-xml php-ldap
	a2enmod ssl
	phpenmod ldap
	systemctl restart apache2

	chgrp -R www-data config data
	chmod -R o-rx config data
	chmod g+w data
	cat << FIN 
- Modifiez config/config.php suivant vos paramètres...
- Puis redémarrez le serveur web par : sudo systemctl restart apache2
- ensuite vous pouvez tester en mode texte avec :  
FIN
     	success 'links https://localhost/services/diagnostic.php'
	echo
	warn 'ATTENTION Il est fortement conseillé de changer le certificat auto-signé pour un vrai! Contactez votre DSI'
else
	echo 'Terminé.'
fi

#Nettoyage
rm -rf Scodoc_Notes-main main.zip

