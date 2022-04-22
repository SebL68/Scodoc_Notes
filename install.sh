#!/bin/bash

if [ $USER != root ]; then
        echo "merci de faire sudo $0"
        exit 1
fi

apt -y install openssl apache2 wget unzip links
apt -y install php php-curl php-xml php-ldap

wget https://github.com/SebL68/Scodoc_Notes/archive/refs/heads/main.zip
mv main.zip /var/www
cd /var/www
unzip main.zip
rm main.zip
\rm -rf html
mv Scodoc_Notes-main/html .
#rm html/index.html
mv Scodoc_Notes-main/includes .
mv Scodoc_Notes-main/lib .
mv Scodoc_Notes-main/data .
mv Scodoc_Notes-main/config .
a2enmod ssl
phpenmod ldap
systemctl restart apache2

echo '- Modifiez config/config.php suivant vos paramètres...'
echo '- redémarrez le serveur web par :   sudo systemctl restart apache2'
echo '- ensuite vous pouvez tester en mode texte avec :  '
echo '     links https://localhost/services/diagnostic.php'

echo '- ATTENTION Il est fortement conseillé de changer le certificat auto-signé pour un vrai! Contactez votre DSI'
echo 'Au passage, merci à Franck Butelle pour ce script :)'