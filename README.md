Pour suivre les évolutions, l'idéal est d'aller voir sur le Discord (voir ci-dessous), sinon il y a ce lien qui est mis à jour moins souvent : https://notes.iutmulhouse.uha.fr/maj.php

Les utilisateurs actuels sont :
 - IUT de Mulhouse => accès complet
 - IUT de Ville d'Avray => accès étudiant
 - IUT de Chartres => accès étudiant + enseignant
 - IUT de Lyon 1 => accès étudiant + enseignant
 - IUT de Tours => accès étudiant
 - IUT Lannion => accès étudiant
 - IUT de Lille => accès étudiant + enseignant
 - IUT de Bordeaux => accès étudiant + enseignant
 - IUT d'Orsay => accès étudiant + enseignent
 - IUT du Havre => accès étudiant + enseignant
 - IUT de Poitiers ?
 - IUT du Littoral Côte d'Opale ?
 - IUT de La Rochelle => accès étudiant
 - IUT Le Mans => accès étudiant
 - IUT de Mantes-En-Yvelines => accès étudiant
 - IUT de Saint-Malo => accès étudiant
 - IUT de la Roche-sur-Yon => accès étudiant
 - IUT de Vélizy => accès étudiant
 - IUT de Roanne ?
 - IUT de Kourou => accès complet
 - IUT de Brest => accès étudiant
 - IUT d'Annecy => accès étudiant + enseignant
 - IUT de Nantes => accès étudiant + enseignant
 - IUT de Blagnac => accès étudiant
 - IUT de Vannes ?
 - IUT Lorient & Pontivy ?
 - IUT d'Orléans => accès étudiant
 - IUT de Rennes => accès étudiant
 - IUT de Perpignan sites de Narbonne et Carcassonne => accès étudiant + enseignant
 - IUT de Villetaneuse => accès étudiant + enseignant
 - IUT de Cergy-Pontoise => accès étudiant
 - IUT de Mantes ?
 - IUT de Béziers => accès étudiant
 - IUT de Saint Denis => A venir
 - IUT d'Amiens => accès étudiant
 - IUT de Caen (IFS) => A venir
 - IUT de Saint-Nazaire => accès étudiant + enseignant
 - IUT Montpellier-Sète => accès étudiant
 - IUT Cachan => accès étudiant
 - IUT Nord Franche-Comté => accès étudiant
 - IUT de Montreuil => accès étudiant
 - IUT de Schiltigheim => A venir
 - Ecole de chimie de Mulhouse (ENSCMu) => accès complet
 - IUT d'Evry => accès étudiant + enseignant
 - IUT de Béthune => accès étudiant
  
Vous utilisez aussi ce projet ? N'hésitez pas à m'en informer pour être également dans cette liste : sebastien.lehmann (at) uha.fr :-)   
  
Vous pouvez utiliser le projet en entier pour plus de simplicité ou utiliser des parties pour coder votre propre plateforme.  
  
L'assistance et les discussions par rapport à cette passerelle se font sur le Discord Assistance de Scodoc : https://discord.gg/FgMNZ4SdD4
  
# A quoi sert cette passerelle ?
La passerelle Scodoc-Notes est un projet permettant de faire le lien entre Scodoc et les étudiants.  
Les étudiants peuvent consulter en ligne leurs notes. La passerelle gère automatiquement l'affichage des relevés pour les DUT et pour les BUT que ce soit sur mobile ou sur ordinateur.  
  
Il est possible de configurer un accès "enseignant". Cet accès permet aux utilisateurs reconnus de :
 - consulter le relevé de n'importe quel étudiant,
 - récupérer la liste des groupes, des fiches d'émargement, de quoi renvoyer les notes, etc. : ces fichiers sont synchronisés avec Scodoc.
  
Cet accès est notamment utile pour les vacataires qui n'ont pas accès à Scodoc, ils peuvent alors récupérer des fichiers à jour dès qu'ils en ont besoin.  

Enfin, la passerelle permet de saisir et justifier les absences de manière autonome ou en stockant les données dans Scodoc.  
  
# Guide d'installation
## Système requis  
  
 - Il est recommandé d'avoir un système Debian ou Ubuntu, pour pouvoir utiliser l'installeur automatique, mais ça peut fonctionner avec d'autres systèmes.
 - Il est nécessaire d'avoir PHP version 7.3 ou plus.
 - Il est recommandé d'utiliser Apache, mais ça fonctionne avec Nginx - il faudra juste un peu de config manuelle.
 - Les dépendances sont installées automatiquement.
 - Le serveur doit être reconnu et autorisé par le CAS.  
 - Le serveur doit pouvoir communiquer avec Scodoc.  
  
## Installation automatique (recommandé)
  
Le script : `/installOrUpdate.sh`  permet d'installer et de mettre à jour la passerelle.
Ce script est compatible Ubuntu et Debian, il permet lors d'une première installation d'installer tout le nécessaire sur le serveur, il reste alors à configurer les fichiers `/config/*`  
  
Lorsque le serveur est déjà opérationnel, il permet de faire une mise à jour de /html, /includes et /lib.
Pour des raisons de sécurité, le fichier installOrUpdate.sh ne se met pas automatiquement à jour.  
  
Le virtual host doit mener vers le dossier `/var/www/html`. Les fichiers à sécuriser sont installés dans `/var/www`, les fichiers publics sont dans `/var/www/html`.  
Pour des raisons de sécurité, si le virtual host est mal configuré, la passerelle ne fonctionnera pas.  
Il est possible de configurer un virtual host vers un autre dossier en respectant le principe : `path` fichiers non accessibles, `path/html` fichiers publics.  
  
Télécharger et ajouter le fichier `installOrUpdate.sh` dans le répertoire `/var/www` en tant que ROOT :  
```
cd /var/www
wget -q https://raw.githubusercontent.com/SebL68/Scodoc_Notes/main/installOrUpdate.sh
chmod +x installOrUpdate.sh

# puis pour installer
./installOrUpdate.sh
```
  
Procédure de mise à jour par la suite :  
```
cd /var/www
./installOrUpdate.sh
```  
  
[Option]  
Par défaut, la mise à jour se fait dans `/var/www/`.  
Le script accepte comme paramètre un chemin différent afin de permettre la mise à jour pour ceux qui ont configuré des Virtual Hosts.  
`./installOrUpdate.sh cheminVersLaPasserelle`  
  
Voir "Diagnostic" et "Configuration" pour la suite.  
  
# Principe de la passerelle
L'identification de la personne se fait le CAS.  
Le CAS renvoie soit :
 - le numéro d'étudiant,
 - une variante du numéro d'étudiant qu'il est possible de transformer dans le fichier config,
 - un autre identifiant, comme l'adresse mail,
 - il est aussi possible de récupérer le numéro s'il est dans un autre champs renvoyé par le CAS.
  
C'est le numéro d'étudiant qui est nécessaire pour communiquer avec Scodoc, s'il n'est pas possible de récupérer le numéro d'étuidant ou une variante dans un des champs du CAS, il faut mettre en place un système de correspondance.  
Cette correspondance est faite dans les fichiers /data/annuaires/liste_etu.txt

Il est possible d'automatiser la génération de ces fichiers à partir du LDAP (voir ci-après).  
  
## Diagnostic
Pour vous aider dans la configuration de votre serveur, un système de diagnostic a été mis en place : /html/sercices/diagnostic.php?-no-sw  
  
Exemple : https://notes.iutmulhouse.uha.fr/services/diagnostic.php?-no-sw
  
Lors de l'utilisation de la passerelle, il est également possible d'activer les messages d'erreur dans /html/services/data.php --> Options de debug  
La passerelle communique via un système d'API, il faut donc voir les réponses dans l'inspecteur (F12) --> Network  
 
## Configuration
### Configuration Scodoc
Il est nécessaire d'avoir un utilisateur avec les droits de lecture pour l'API Scodoc : https://scodoc.org/ScoDoc9API/#configuration-de-scodoc-pour-utiliser-lapi

### Configuration Passerelle
  
L'ensemble des fichiers à configurer se trouvent dans "/config/".
Il est à minima nécessaire de configurer :
  - cas.pem (recommandé pour des raisons de sécurité : https://www.php.net/manual/fr/function.curl-setopt.php#110457),
  - cas_config.php,
  - config.php 
  
Il est recommandé d'avoir un super-administrateur en incluant son identifiant CAS dans le fichier /data/annuaires/super_admin.txt (enlevez le _DEMO).
  
### Configuration de l'authentification : CAS
Complétez cas_config.php.  
Pour vous aider à tester, vous pouvez utiliser le système de diagnostique.  
Vous pouvez également utiliser le fichier /code_test/testCAS.php pour comprendre ce qui est renvoyé par le CAS.  
  
Il est nécessaire d'obtenir un numéro d'étudiant tel qu'il est dans Scodoc, plusieurs cas de figures :  
 - l'identifiant au CAS est le numéro d'étudiant : il n'y a rien à faire,
 - l'identifiant est un variant du numéro d'étudiant, il peut y avoir une lettre qui change ou autres, dans ce cas il est possible d'opérer une transfromation avec la fonction nipModifier() dans le fichier config,
 - l'identifiant est disponible dans un autre attribut renvoyé par le CAS (voir /code_test/testCAS.php) : il est possible d'indiquer quelle clé utiliser dans le fichier config : public static $CAS_nip_key = 'cle';
 - l'identifiant n'est pas accessible, il faut donc utiliser le LDAP pour avoir une correspondance entre l'idCAS et le nip.
  
L'utilisation du LDAP n'est pas obligatoire si le CAS renvoie le nip. Si par exemple le CAS renvoie l'adresse mail, il faut alors mettre en place le système qui permet de convertir les mails en nip. Dans /data/annuaires, il y a les fichiers pour cette conversion. Différentes fonctions permettent de remplir ces fichiers automatiquement à partir du LDAP (voir ci-après).  
  
Pour tester la connexion avec Scodoc, il est possible de forcer un utilisateur (étudiant) dans /config/config.php => nipModifier().
  
Il est possible de s'authentifier de manière forcée en utilisant les jetons JWT.  
Ces jetons peuvent être créés dans le fichier /html/services/createJWT.php (à modifier).  
Ces jetons sont notamment utiles au début pour bypasser le CAS pour des premiers tests.  

### Option - configuration du LDAP
Complétez les paramètres LDAP dans /config/config.php  
La mise à jour forcée du LDAP (pour les tests) se fait en exécutant le fichier /includes/CmdUpdateLists.php en CLI.  
La mise en route du crontab se fait avec le fichier /includes/CmdSetUpdateLists.php suivant le même principe.  
  
## A noter
  
Par défaut, ce site ne diffuse que les relevés de notes aux étudiants.  
Il est possible d'activer d'autres options prévus pour les enseignants comme :
 - la possibilité de visualiser les relevés de n'importe quel étudiant,
 - récupérer des documents xls pratiques, automatiquement générés en fonction des listes Scodoc,
 - gérer les absences entièrement depuis la passerelle, avec des créneaux prédéfinis (sans utiliser Scodoc).  
 
Les super-admin ont un onglet supplémentaire pour configurer la passerelle en ligne.  
Ils peuvent également attribuer les rôles admin ou personnel à des idCAS.  
  
Si le mode enseignant est activé, les idCAS reconnus comme "personnel" pourront avoir accès aux relevés de tous les étudiants et récupérer les listes au format xlsx.  
Si le mode absences est activé, la passerelle permet de réaliser les absences par les personnels, un admin pourra justifier ces absences - a noter que le module absence n'est pas connecté à Scodoc pour le moment - ces absences sont automatiquement affichées aux étudiants.  
  
Il est également possible d'utiliser le LDAP pour remplir automatiquement les listes des personnels.  
  
_______________  
  
Les étudiants peuvent modifier leur "avatar" sur la passerelle, il faut vérifier que l'utiliseur www-data puisse bien modifier les fichiers du répertoire /data/studentPic  
   
# Comment connaître la version de la passerelle ?
La version est notée dans le fichier /html/sw.js  
Il est également possible de la voir en bas de la page d'accueil de la passerelle.

# Procédures de mise à jour
Les dossiers /config et /data sont des données locales qui permettent de faire fonctionner la passerelle dans votre environnement.  
Ils ne sont (sauf cas exceptionnels) pas modifiés.  
Utilisez alors le script `installOrUpdate.sh`

# Indications pour les développeurs
La passerelle utilise un système de cache côté client utilisant un service worker.  
Il y faut alors le prendre en compte de cette manière :
 - certains fichiers, comme l'index, ne sont pas mis à jour tant que la version du SW n'est pas mis à jour,
 - les autres fichiers sont mis à jour après le chargement de la page.

Il faut donc faire un double rafraîchissement pour voir la dernière version de ces derniers fichiers.  
  
Je vous conseille alors, pour le développement, de "bypasser" le service worker - sous Chrome : F12 -> Applications -> Service Worker  

_________________________

Pour des développements locaux et des commits, il est nécessaire de ne pas prendre en compte les modifications de certains fichiers, il est alors possible de ne pas les ajouter à l'arbre GIT avec :  
`git update-index --skip-worktree config/config.php`
  
  
# Considérations de sécurité
La passerelle a fait l'objet d'une attention particulière aux problèmes de sécurité et plusieurs personnes ont audité le code : il n'y a aucune faille connue.  
