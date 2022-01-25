Pour suivre les évolutions : https://notes.iutmulhouse.uha.fr/maj.php

Travaux en cours :
 - faire en sorte qu'on puisse gérer autre chose que les mails dans l'onglet "comptes"
 - permettre d'attaquer directement le LDAP sans avoir besoin de faire de listes
 - faire en sorte que le choix des étudiants sur la page relevé permette de choisir directement le nom / prenom plutôt qu'un mail ou nip
 - refonte du système d'absences : gérer par plages plutôt que créneaux + interconnexion avec Scodoc
 - lien pour les photos entre Scodoc et la passerelle  
  
Les utilisateurs actuels sont :
 - IUT de Mulhouse => accès complet,
 - IUT de Ville d'Avray => accès étudiant,
 - IUT de Chartres => accès étudiant + enseignant,
 - IUT de Lyon 1 => accès étudiant + enseignant,
 - IUT de Tours ?
 - IUT Lannion ?
 - IUT de Lille => accès étudiant + enseignant,
 - IUT de Bordeaux => accès étudiant,
 - IUT d'Orsey ?
 - IUT du Havre => accès étudiant + enseignant,
 - IUT de Poitiers ?
 - IUT du Littoral Côte d'Opale ?
 - IUT de La Rochelle => accès étudiant,
 - IUT Le Mans ?
 - IUT de Mantes-En-Yvelines => accès étudiant,
 - IUT de Saint-Malo => accès étudiant,
 - IUT de la Roche-sur-Yon ?
 - IUT de Vélizy ?
 - IUT de Roanne ?
 - IUT de Kourou ?
 - IUT de Brest ?
  
Vous utilisez aussi ce projet ? N'hésitez pas à m'en informer pour être également dans cette liste : sebastien.lehmann (at) uha.fr :-)   
  
Vous pouvez utiliser le projet en entier pour plus de simplicité ou utiliser des parties pour coder votre propre plateforme.  
  
L'assistance et les discussions par rapport à cette passerelle se font sur le Discord Assistance de Scodoc : https://discord.gg/FgMNZ4SdD4
  
# A quoi sert cette passerelle ?
La passerelle Scodoc-Notes est un projet permettant de faire le lien entre Scodoc et les étudiants.  
Les étudiants peuvent consulter en ligne leurs notes. La passerelle gère automatiquement l'affichage des relevés pour les DUT et pour les BUT que ce soit sur mobile ou sur ordinateur.  
  
Il est possible de configurer un accès "enseignant". Cet accès permet aux utilisateurs reconnus de :
 - consulté le relevé de n'importe quel étudiant,
 - récupérer la liste des groupes, des fiches d'émargement, de quoi renvoyer les notes, etc. : ces fichiers sont synchronisés avec Scodoc.
  
Cet accès est notamment utile pour les vacataires qui n'ont pas accès à Scodoc, ils peuvent alors récupérer des fichiers à jour dès qu'ils en ont besoin.  
  
# Principe de la passerelle
L'identification de la personne se fait le CAS.  
Le CAS renvoie soit :
 - le numéro d'étudiant,
 - une variante du numéro d'étudiant qu'il est possible de transformer dans le fichier config,
 - un autre identifiant, comme l'adresse mail.
  
C'est le numéro d'étudiant qui est nécessaire pour communiquer avec Scodoc, si votre CAS renvoie un autre identifiant, il faut mettre en place un système de correspondance.  
Cette correspondance est faite dans les fichiers /data/annuaires/liste_*.php

Il est possible d'automatiser la génération de ces fichiers à partir du LDAP (voir ci-après).

# Guide rapide d'installation
## Diagnostic
Pour vous aider dans la configuration de votre serveur, un système de diagnostic a été mis en place : /html/sercices/diagnostic.php  
Il est également possible d'activer les messages d'erreur dans /html/services/data.php --> Options de debug  
La passerelle communique via un système d'API, il faut donc voir les réponses dans l'inspecter (F12) --> Network  
  
## Fichiers
Le dossier "html" doit être la racine du site.  
Les autres dossiers doivent être dans le dossier parent et donc inaccessible depuis le net.  
Ceci a été fait pour des raisons de sécurité : ces dossiers ne doivent pas être accessible en dehors du serveur car ils contiennent des données et fonctions sensibles (mot de passe, certificats, etc.). Le seul dossier accessible doit être "html".  
Il est alors possible de les placer comme il faut par rapport au www ou de configurer dans Apache le fichier httpd-vhosts.conf :
```
DocumentRoot "${INSTALL_DIR}/www/html/"
<Directory "${INSTALL_DIR}/www/html/">
```

Si besoin, le dossier "html" peut être nommé différemment, il peut par exemple être nommé "www".

## Configuration
Le serveur passerelle doit avoir accès au serveur Scodoc.

L'ensemble des fichiers à configurer se trouvent dans "/config/".
Il est à minima nécessaire de configurer :
  - cas.pem (recommandé pour des raisons de sécurité : https://www.php.net/manual/fr/function.curl-setopt.php#110457),
  - cas_config.php,
  - config.php :
    -  $departements,
    -  $scodoc_url,
    -  $scodoc_login

Pour simplifier le fichier de configuration pour les nouveaux admin, toutes les options ne sont pas dedans.  
La liste de toutes les options est disponible dans le fichier /includes/default_config.php  
Il faut alors ajouter, si nécessaire, l'option désirée comme pour les options déjà existantes.  
  
CAS nécessite des dépendances : https://apereo.atlassian.net/wiki/spaces/CASC/pages/103252625/phpCAS+requirements
 - CURL,
 - SSL,
 - DOM

L'utilisation du LDAP n'est pas obligatoire si le CAS renvoie le nip. Si le CAS renvoie l'adresse mail, il faut alors mettre en place le système qui permet de convertir les mails en nip. Dans /data/annuaires, il y a les fichiers pour cette conversion. Différentes fonctions permettent de remplir ces fichiers automatiquement à partir du LDAP (voir ci-après).  
  
Il est possible de s'authentifier de manière forcée en utilisant les jetons JWT.  
Ces jetons peuvent être créés dans le fichier /html/services/createJWT.php (à modifier).  
Ces jetons sont notamment utile au début pour bypasser le CAS pour des premiers tests, ils servent également à utiliser un statut de SUPERADMIN. Ce statut permet de mettre en route le crontab pour la mise à jour du LDAP depuis le navigateur.  
La mise à jour forcée du LDAP (pour les tests) se fait en exécutant le fichier /includes/CmdUpdateLists.php avec le statut SUPERADMIN ou en CLI ou en le déplaçant dans /html.  
La mise en route du crontab se fait avec le fichier /includes/CmdSetUpdateLists.php suivant le même principe.
  
Par défaut, ce site ne diffuse que les relevés de notes aux étudiants.  
Il est possible d'activer d'autres options prévus pour les enseignants comme :
 - la possibilité de visualiser les relevés de n'importe quel étudiant,
 - récupérer des documents xls pratiques, automatiquement générés en fonction des listes Scodoc,
 - gérer les absences entièrement depuis la passerelle, avec des créneaux prédéfinis (sans utiliser Scodoc).
  
# Comment connaitre la version de la passerelle ?
La version est notée dans le fichier /html/sw.js

# Procédures de mise à jour
Les dossiers /config et /data sont des données locales qui permettent de faire fonctionner la passerelle dans votre environnement.  
Ils ne sont (sauf cas exceptionnels) pas modifiés.  
Si le fichier /config/config.php devait subir une modification important, un message s'afficherait sur la passerelle indiquant qu'il faut utiliser une nouvelle version de ce dernier.  

Pour réaliser la mise à jour, il faut alors copier et coller sur votre passerelle les dossiers :
 - /html
 - /includes
 - /lib

*** Expérimental et non approuvé pour le moment ***  
Il devrait être possible de configurer un git pull de manière périodique pour une mise à jour automatique.

# Indications pour les développeurs
La passerelle utilise un système de cache côté client utilisant un service worker.  
Il y faut alors le prendre en compte de cette manière :
 - certains fichiers, comme l'index, ne sont pas mis à jour tant que la version du SW n'est pas mis à jour,
 - les autres fichiers sont mis à jour après le chargement de la page.

Il faut donc faire un double rafraichissement pour voir la dernière version de ces derniers fichiers.  
  
Je vous conseille alors, pour le développement, de "bypasser" le service worker - sous Chrome : F12 -> Applications -> Service Worker  

_________________________

Pour des développements locaux et des commits, il est nécessaire de ne pas prendre en compte les modifications de certains fichiers, il est alors possible de ne pas les ajouter à l'arbre GIT avec :  
`git update-index --skip-worktree config/config.php`
  
# Ne pas utiliser la suite de cette documentation, ce n'est plus à jour !
# !!! Nouvelle documentation en cours de rédaction !!!

net start WinFSP.launcher

# Scodoc_Notes

Passerelle entre Scodoc et Internet pour l'affichage des notes aux étudiants.

1. [Démonstration](#demonstration)
2. [Auteurs](#auteurs)
3. [Licence](#licence)
4. [Système requis](#système-requis)
5. [Historique des mises à jours](#historique-des-mises-à-jours)
6. [Histoire du projet](#histoire-du-projet)
7. [Présentation du projet](#présentation-du-projet)
8. [Fonctionnement global](fonctionnement-global)
9. [Installation](#installation)
10. [Pour les développeurs](#pour-les-développeurs)

-----------------

# Démonstration
Vous pouvez voir un exemple du projet à [cette adresse](https://notes.iutmulhouse.uha.fr/?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzZXNzaW9uIjoiQ29tcHRlX0RlbW8udGVzdEB1aGEuZnIiLCJzdGF0dXQiOiJldHVkaWFudCJ9.kHuiNx8X2mWUjv1LAHVOdcLGCu2yQS_i6fxqZZICuEA). Veuillez noter que c'est une démonstration avec des données fictives et que le changement de semestre n'est pas opérationnel.

# Auteurs
Ce projet a été créé par Sébastien Lehmann.
Denis Greaf fait parti des contributeurs.
Merci à Alexandre Kieffer et Bruno Colicchio pour leurs contributions à la réussite de ce projet.

# Licence
Ce projet est Open Source sous licence MIT. Sentez vous libre de l'utiliser, de le modifier, d'y participer ou d'en faire un fork.  
Si vous appréciez ce projet, un merci fait toujours plaisir à prenom.nom@uha.fr (en changeant pour sebastien lehmann - anti spam activé ;-D)

# Système requis
- PHP version 7+  
- Extensions : 
  - cURL,
  - LDAP 

Pas besoin de base de données.

# Historique des mises à jours
[Lien](https://notes.iutmulhouse.uha.fr/maj.php) - !!! Historique non mis à jour !!!

-----------------

# Histoire du projet
## Scodoc
[Scodoc](https://scodoc.org/) est un outil utilisé dans de nombreuses universités qui simplifie la gestion des notes.  
Ce système est un maillon sensible du système informatique car il participe à l'obtention des diplômes.  
Une intrusion par un étudiant permettrait une modification des notes.  

Dès lors, bon nombre d'universités ont décidé de ne pas laisser Scodoc accessible sur le web et sur l'intranet des universités.  
Pour y accéder, il faut être sur une liste blanche d'ordinateurs ou utiliser un VPN.  

## Notes
Jusqu'à présent, les notes étaient affichées sur Moodle ou sur un tableau d'affichage avec la nécessité d'anonymiser les noms à l'aide de leur numéro d'étudiant.  
Il n'est alors pas aisé pour les étudiants de vérifier leurs notes, de vérifier si l'ensemble des évaluations a été affiché, de connaître leurs moyennes d'UE et générale avant les jurys.  

Il est donc apparu nécessaire d'avoir une interface moderne et sécurisée permettant aux étudiants de consulter les notes qui vont être utilisées pour les jurys.  

-----------------

# Présentation du projet
Ce projet prend la forme d'un site web accessible aux étudiants et personnels de la composante.
Ce site permet :
- d'afficher les notes,
- de gérer et afficher les absences,
- d'obtenir les trombinoscopes ou les listes pour le retour des notes, les groupes, etc.

## Pour les étudiants

### Relevé de notes
- Accès aux notes dès qu'elles sont entrées dans Scodoc.
- Affichage automatique des moyennes, coefficients, etc.
- Possibilité de télécharger le relevé au format PDF pour archivage.
- Choix du semestre.
- Pointage des notes.
- Scroll automatique vers les notes non pointées lors de la connexion.
- Affichage / masquage des évaluations sans notes.


### Avatar
- L'étudiant a la possibilité d'ajouter une photo de profil, cette photo est utilisée pour les trombinoscopes et les absences.

### Absences
- Les absences et les statuts (justifié / absent) de l'étudiant sont affichés avec la date, le créneau, la matière et l'enseignant.
Le but étant que les étudiants puissent vérifier les absences à tout moment.

Ce système est interne au site.
Pour le moment, les absences ne sont pas reportées dans Scodoc car Scodoc ne permet pas de gérer des créneaux plus petit qu'une demi journée.

Il est envisagé dans une future évolution du site de permettre à l'étudiant d'ajouter ses justificatifs d'absence directement en ligne.

## Pour les enseignants
- Accès aux notes de tous les étudiants.  
- Accès aux groupes d'étudiants des différents semestres : 
Dès qu'il y a un changement dans les groupes, il est nécessaire de modifier une série de documents XLSX et de les envoyer à l'ensemble des intervenants avec les problèmes de versions que ça peut entraîner. Un système complémentaire a alors été mis en place pour générer automatiquement ces fichiers à partir de l'adresse : `https://url_du_server.fr/{nom du département}`  
`- choix des groupes,`  
`- téléchargement de fichiers XLSX avec les listes d'émargements, les groupes, le retour des notes, les données étudiants.`  
- Listes d'émargements : fichier à imprimer et à faire signer par les étudiants lors d'un partiel pour confirmer leur présence.
- Groupes : fichier montrant les étudiants dans chaque groupe.
- Retour notes : fichier pour qu'un intervenant puisse indiquer les notes à intégrer dans Scodoc.
- Données étudiants : fichier contenant des données comme le numéro d'étudiant, l'adresse mail, etc.  

Exemple (lien uniquement pour les personnels de l'IUT de Mulhouse) : [Lien](https://notes.iutmulhouse.uha.fr/MMI)
![Listes étudiants](/documents%20README.md/Listes_Etudiants_censored.jpg?raw=true)

## Le site web
Le site est une PWA et fonctionne sur les principes de l'APP Shell.
Il est ainsi possible de l'installer comme une application sur un smartphone.

-----------------

# Fonctionnement global
Lors de la première connexion, un processus d'authentification avec le CAS est mis en place.  
Le site fonctionne sur le principe de l'APP Shell en mettant en place une PWA et un Service Worker utilisant l'API Cache.  
Ainsi, toutes les données non changeantes sont enregistrées pour une future connexion ou une connexion hors ligne.  
  
![Fonctionnement projet](/documents%20README.md/Scodoc_notes_fonctionnement.svg?raw=true&sanitize=true)

-----------------

# Installation
## Architecture réseau
Ce projet est à installer sur un serveur qui fait le lien entre l'intranet et internet, il doit donc être sécurisé et configuré pour une utilisation en production.  
Le routeur doit être configuré pour autoriser l'accès entre Scodoc et le serveur.

## Configuration serveur
Il est nécessaire d'utiliser une connexion sécurisée HTTPS, [Let's Encrypt](https://letsencrypt.org/fr/) fera l'affaire.  
Pour des raisons de performance, il est recommandé :
- d'utiliser HTTP/2 ou /3,
- d'activer le mod_deflate.
  
Pour des raisons de sécurité, une partie des dossiers ne doit pas être accessible en dehors du serveur. Le seul dossier accessible doit être "html".  
Il est alors possible de les placer comme il faut dans le www ou de configurer dans Apache le fichier httpd-vhosts.conf :
```
DocumentRoot "${INSTALL_DIR}/www/html/"
<Directory "${INSTALL_DIR}/www/html/">
```

## Fichiers
Il n'est pas requis d'avoir de base de données.  
!!! Le dossier HTML doit être à la racine du site !!!  
!!! Les autres dossiers doivent être dans le dossier parent et donc inaccessible depuis le net !!!

## Configuration
Il est nécessaire de configurer différents fichiers pour adapter ce projet à votre établissement.

### LDAP
Le LDAP est l'annuaire des étudiants et personnels.  
Un accès direct au LDAP n'a pas été permis, ce projet utilise donc différents exports du LDAP sous la forme d'un fichier texte.  
Vous pouvez trouver un exemple de ces fichiers dans le dossier /LDAP avec un fichier :  
 - pour les étudiants du type  
 `prénom:nom:numéro_étudiant:-:-:-:-:-:-:Code_Formation:-:-:-:-:-:-:-:-:-:-:-:adresse_mail:
 steve:jobs:e212345:-:-:-:-:-:-:3LRHI3:-:-:-:-:-:-:-:-:-:-:-:steve.jobs@uha.fr:`
 - pour les enseignants du type  
 `prénom:nom:numéro_étudiant:adresse_mail:  
 stephen:hawking:stephen.hawking@uha.fr:`
 - pour les BIATSS du type  
 `prénom:nom:numéro_étudiant:adresse_mail:  
 albert:einstein:albert.einstein@uha.fr:`
  
Il y a également un fichier qui ne vient pas du LDAP pour identifier les vacataires sous la forme d'une liste d'adresses mail.  
Il est recommandé de mettre en place un système automatisé pour récupérer ces exports LDAP.
L'accès aux fichiers du LDAP se fait à partir du fichier `/includes/LDAPData.php`.

### Système d'authentification
Ce projet utilise le CAS de l'UHA pour se connecter. La bibliothèque utilisée est [phpCAS](https://github.com/apereo/phpCAS).  
Ajoutez votre propre fichier pour la librairie d'authentification.  
L'ensemble du service d'authentification se fait à partir des fichiers :
 - `/includes/auth.php` pour vérifier si l'utilisateur est connecté
 - `/html/services/doAuth.php` pour connecter l'utilisateur via le CAS

### Clé JWT
Un système de jeton JWT est mis en place. Ce système permet à d'autres serveurs de se connecter aux différents services proposés par l'API.  
Il est donc nécessaire de définir une clé de cryptage dans le fichier `/includes/JWT/key.php`.

### Accès Scodoc
Il est nécessaire de configurer le fichier `/includes/loginScodoc.php` pour que le serveur puisse accéder à Scodoc.  
Il faut y compléter :
 - l'URL vers le server Scodoc,
 - l'identifiant vers un compte de type "secrétariat",
 - un mot de passe.

Chaque compte est unique dans Scodoc, il est donc nécessaire de l'identifier pour chaque département de cette manière :  
`un_compte_de_votre_choix_$dep` $dep étant le nom du département, exemple : `scodoc_acces_MMI`.  
  
Pour des raisons de sécurité, il est recommandé de mettre le compte en secrétariat, il ne sera ainsi qu'en lecture.  

### Système analytics
Vous pouvez intégrer dans le fichier `/includes/analytics.php` le code de votre service d'analyse des connexions, il sera inclus automatiquement dans les pages.

### Redirection
Le fichier `/html/.htaccess` vous permettra de configurer les départements qui auront accès aux listing XLSX d'étudiants.

-----------------

# Pour les développeurs
## Accès par jeton
A des fins de développement, il est possible de se faire passer pour une autre personne avec un statut différent à l'aide d'un jeton JWT.  
Ce jeton est à transmettre à chaque requête en POST aux services de l'API.  

Il est également possible de le passer dans l'URL comme dans la [démonstration du site](https://notes.iutmulhouse.uha.fr/?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzZXNzaW9uIjoiQ29tcHRlX0RlbW8udGVzdEB1aGEuZnIiLCJzdGF0dXQiOiJldHVkaWFudCJ9.kHuiNx8X2mWUjv1LAHVOdcLGCu2yQS_i6fxqZZICuEA).
!!! Attention, pour des raisons de sécurité, cette méthode de passer le jeton dans l'URL est à éviter le plus possible et sera réservée pour une démonstration avec de fausses données ou pour des fins de développement en se faisant passer pour un utilisateur sans passer par le système d'authentification. !!!

Il est possible de créer des jetons JWT avec le fichier `/html/services/createJWT.php`.

## API des services
Vous pouvez trouver comment utiliser les services dans la [documentation sur le site](https://notes.iutmulhouse.uha.fr/services/documentation.php).
