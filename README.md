# Scodoc_Notes
Passerelle entre Scodoc et Internet pour l'affichage des notes aux étudiants.

1. [Demonstration](#demonstration)
2. [Auteurs](#auteurs)
3. [Licence](#licence)
4. [Système requis](#système-requis)
5. [Historique des mises à jours](#historique-des-mises-à-jours)
6. [Histoire du projet](#histoire-du-projet)
7. [Présentation du projet](#présentation-du-projet)
8. [Fonctionnement global](fonctionnement-global)
9. [Installation](#installation)
10. [Pour les développeurs](#pour-les-développeurs)

# Demonstration
Vous pouvez voir le résultat à [cette adresse](https://notes.iutmulhouse.uha.fr/?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzZXNzaW9uIjoiQ29tcHRlX0RlbW8udGVzdEB1aGEuZnIiLCJzdGF0dXQiOiJldHVkaWFudCJ9.kHuiNx8X2mWUjv1LAHVOdcLGCu2yQS_i6fxqZZICuEA) - veuillez noter que c'est une démonstration et que le changement de semestre n'est pas opérationnel.

# Auteurs
Ce projet a été réalisé par Sébastien Lehmann sur son temps libre.
Merci à Denis Graef, Alexandre Kieffer et Bruno Colicchio pour leur participation respective.

# Licence
Ce projet est Open Source sous licence MIT. Sentez vous libre de l'utiliser, de le modifier, d'y participer ou d'en faire un fork.  
Si vous appreciez ce projet, un merci fait toujours plaisir à prenom.nom@uha.fr (en changeant pour sebastien lehmann - anti spam activé ;-D)

# Système requis
- PHP version 7+  
- Extension : cURL  
- Activer : rewrite mod  

# Historique des mises à jours
[Lien](https://notes.iutmulhouse.uha.fr/maj.php)

# Histoire du projet
## Scodoc
[Scodoc](https://scodoc.org/) est un outil utilisé dans de nombreuses université qui simplifie la gestion des notes.  
Ce système est un maillon sensible du système informatique car il participe à l'obtention des diplômes.  
Une intrusion par un étudiant permettrait une modification des notes.  

Dès lors, bon nombre d'universités ont décidé de ne pas laisser Scodoc accessible sur le web et sur l'intranet des universités.  
Pour y accéder, il faut être sur une liste blanche d'ordinateurs ou utiliser un VPN.  

## Notes
Jusqu'à présent, les notes étaient affichées sur Moodle ou sur un tableau d'affichage avec la nécessité d'anonymiser les noms à l'aide de leur numéro détudiant.  
Il n'est alors pas aisé pour les étudiants de vérifier leurs notes, de vérifier si l'ensemble des évaluations a été affiché, de connaître leurs moyennes d'UE et générale avant les jurys.  

Il est donc apparu nécessaire d'avoir une interface moderne et sécurisée permettant aux étudiants de consulter les notes qui vont être utilisées pour les jurys.  

# Présentation du projet
Ce projet prend la forme d'un site web accessible aux étudiants et personnels de la composante.

## Pour les étudiants
- Accès aux notes dès qu'elles sont entrées dans Scodoc.
- Affichage automatique des moyennes, coefficients, etc.
- Possibilité de télécharger le relevé au format PDF pour archivage.
- Choix du semestre.
- Pointage des notes.
- Scroll automatique vers les notes non pointées lors de la connexion.
- Affichage / masquage des évaluations sans notes.

## Pour les enseignants
- Accès aux notes de tous les étudiants.

Dès qu'il y a un changement dans les groupes, il est nécessaire de modifier une série de documents XLSX et de les envoyé à l'ensemble des intervenants avec les problèmes de versions que ça peut entraîner. Un système complémentaire à alors été mis en place pour générer automatiquement ces fichiers à partir de l'adresse : https://url_du_server.fr/{nom du département}  
Exemple (uniquement pour les personnels de l'IUT de Mulhouse) : [Lien](https://notes.iutmulhouse.uha.fr/MMI)

- Accès aux groupes d'étudiants des différents semestres :  
`- choix des groupes,`  
`- téléchargement de fichiers XLSX avec les listes d'émargements, les groupes, le retour des notes, les données étudiants.`

- Listes d'émargements : fichier à imprimer et à faire signer par les étudiants lors d'un partiel pour confirmer leur présence.
- Groupes : fichier montrant les étudiants dans chaque groupe.
- Retour notes : fichier pour qu'un intervenant puisse indiquer les notes à intégrer dans Scodoc.
- Données étudiants : fichier contenant des données comme le numéro d'étudiant, l'adresse mail, etc.

## Le site web
Le site est une PWA et fonctionne sur les principes de l'APP Shell.

# Fonctionnement global
Rédaction en cours.

# Installation
## Architecture réseau
Ce projet est à installer sur un serveur qui fait le lien entre l'intranet et internet, il doit donc être sécurisé et configuré pour une utilisation en production.  
Le routeur doit être configuré pour autoriser l'accès entre Scodoc et le serveur.

## Configuration serveur
Il est nécessaire d'utiliser une connexion sécurisé HTTPS, [Let's Encrypt](https://letsencrypt.org/fr/) fera l'affaire.  
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

## Configuation
Il est nécessaire de configurer différents fichiers pour adapter ce projet à votre établissement.

### LDAP
Le LDAP est l'annuaire des étudiants et personnels.  
Un accès direct au LDAP n'a pas été permis, ce projet utilise donc différents exports du LDAP sous la forme d'un fichier texte.  
Vous pouvez trouver un exemple de ces fichiers dans le dossier /LDAP avec un fichier :  
 - pour les étudiants du type  
 `prénom:nom:numéro_étudiant:-:-:-:-:-:-:Code_Formation:-:-:-:-:-:-:-:-:-:-:-:adresse_mail:
 steve:jobs:e212345:-:-:-:-:-:-:3LRHI3:-:-:-:-:-:-:-:-:-:-:-:steve.jobs@uha.fr:`
 - pour les enseignant du type  
 `prénom:nom:numéro_étudiant:adresse_mail:
 stephen:hawking:stephen.hawking@uha.fr:`
 - pour les BIATSS du type  
 `prénom:nom:numéro_étudiant:adresse_mail:
 albert:einstein:albert.einstein@uha.fr:`
  
Il y a également un fichier qui ne vient pas du LDAP pour identifier les vacataires sous la forme d'une liste d'adresses mail.  
Il est recommandé de mettre en place un système automatisé pour récupérer ces exports LDAP.
L'accès aux fichiers du LDAP se fait à partir du fichier `/includes/LDAPData.php`.

### Système d'authentification
Ce projet utilise le CAS de l'UHA pour se connecter. La bibilotèque utilisée est [phpCAS](https://github.com/apereo/phpCAS).  
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
Vous pouvez intégrer dans le fichier `analytics.php` le code de votre service d'analyse des connexions, il sera inclu automatiquement dans les pages.

# Pour les développeurs
## Accès par jeton
A des fins fins de développement, il est possible de se faire passer pour une autre personne avec un statut différent à l'aide d'un jeton JWT.  
Ce jeton est à transmettre à chaque requête en POST aux services de l'API.  

Il est également possible de le passer dans l'URL comme dans la [démonstration du site](https://notes.iutmulhouse.uha.fr/?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzZXNzaW9uIjoiQ29tcHRlX0RlbW8udGVzdEB1aGEuZnIiLCJzdGF0dXQiOiJldHVkaWFudCJ9.kHuiNx8X2mWUjv1LAHVOdcLGCu2yQS_i6fxqZZICuEA).
!!! Attention, pour des raisons de sécurité, cette méthode de passer le jeton dans l'URL est à éviter le plus possible et sera reservé pour une démonstration avec de fausses données ou pour des fins de développement en se faisant passer pour un utilisateur sans passer par le système d'authentification. !!!

Il est possible de créer des jetons JWT avec le fichier `/html/services/createJWT.php`.

## API des sercices
Vous pouvez trouver comment utiliser les services dans la [documentation sur le site](https://notes.iutmulhouse.uha.fr/services/documentation.php).
