wget https://github.com/SebL68/Scodoc_Notes/archive/refs/heads/main.zip
mv main.zip /var/www
cd /var/www
unzip main.zip
rm main.zip
mv -f Scodoc_Notes-main/html .
mv -f Scodoc_Notes-main/includes .
mv -f Scodoc_Notes-main/lib .

echo 'Mise à jour faite'