# Nomadix

Nomadix est une application web PHP de découverte de destinations touristiques. Elle permet de consulter des destinations, rechercher un lieu, créer un compte, publier des avis, gérer son profil et administrer les utilisateurs, destinations et avis.

## Sommaire

- [Présentation](#présentation)
- [Guide utilisateur](#guide-utilisateur)
- [Guide administrateur](#guide-administrateur)
- [Structure du projet](#structure-du-projet)
- [Base de données](#base-de-données)
- [Sécurité et bonnes pratiques](#sécurité-et-bonnes-pratiques)

## Présentation

L'application propose :

- une page d'accueil avec recherche de destinations ;
- des fiches détaillées par destination ;
- un système d'inscription et de connexion ;
- un espace profil utilisateur ;
- un système d'avis avec note de 1 à 5 ;
- un tableau de bord administrateur ;
- une gestion des utilisateurs, destinations et avis.

## Guide utilisateur

### Accéder à l'accueil

La page d'accueil affiche la liste des destinations disponibles. Chaque destination est présentée avec une image, une ville, un pays et le nom du monument ou lieu.

Actions possibles :

- consulter toutes les destinations ;
- rechercher une destination ;
- ouvrir une fiche destination ;
- se connecter ;
- créer un compte.

### Rechercher une destination

Sur la page d'accueil :

1. saisir un mot dans le champ `Recherche...` ;
2. cliquer sur `Rechercher` ;
3. consulter les résultats.

La recherche porte sur :

- la ville ;
- le pays ;
- le nom de la destination.

Si aucun résultat ne correspond, un message indique qu'aucune destination n'a été trouvée.

### Consulter une destination

Pour ouvrir une destination, cliquer sur sa carte depuis l'accueil.

La fiche destination affiche :

- le nom de la destination ;
- le pays ;
- l'image de couverture ;
- la description ;
- la note moyenne ;
- les avis des voyageurs ;
- des filtres et tris d'avis.

Les avis peuvent être :

- filtrés par note ;
- triés par meilleurs avis ;
- triés par moins bons avis ;
- triés par plus récents ;
- triés par plus anciens.

### Créer un compte

Depuis l'accueil, cliquer sur `S'inscrire`.

Renseigner :

- un pseudo ;
- une adresse e-mail ;
- un mot de passe ;
- la confirmation du mot de passe.

Le mot de passe doit contenir :

- au moins 12 caractères ;
- une majuscule ;
- une minuscule ;
- un chiffre ;
- un symbole.

L'adresse e-mail doit être unique. Après une inscription réussie, l'utilisateur est connecté automatiquement et redirigé vers l'accueil.

### Se connecter

Depuis l'accueil, cliquer sur `Se connecter`.

Renseigner :

- le pseudo ;
- le mot de passe.

Après connexion :

- un utilisateur standard est redirigé vers l'accueil ;
- un administrateur est redirigé vers l'administration.

### Se déconnecter

Une fois connecté, cliquer sur `Déconnexion`. Une confirmation est demandée avant la fermeture de session.

### Laisser un avis

Pour publier un avis :

1. se connecter ;
2. ouvrir une fiche destination ;
3. choisir une note entre 1 et 5 étoiles ;
4. écrire un commentaire ;
5. cliquer sur `Envoyer`.

Un utilisateur ne peut avoir qu'un avis par destination. S'il a déjà publié un avis, le formulaire permet de le modifier ou de le supprimer.

Le commentaire est limité dans l'interface à 500 caractères.

### Modifier un avis

Sur la fiche destination :

1. ouvrir la destination concernée ;
2. modifier la note ou le commentaire dans le formulaire ;
3. cliquer sur `Modifier` ;
4. confirmer l'action.

La date de l'avis est mise à jour lors de la modification.

### Supprimer un avis

Sur la fiche destination :

1. ouvrir la destination concernée ;
2. cliquer sur `Supprimer` dans le formulaire d'avis ;
3. confirmer la suppression.

La suppression est définitive.

### Gérer son profil

Un utilisateur connecté peut ouvrir la page `Profil`.

La page profil affiche :

- l'avatar ;
- le pseudo ;
- l'adresse e-mail ;
- la date de création du compte ;
- le statut utilisateur ou administrateur.

### Changer d'avatar

Dans le profil :

1. choisir un avatar dans la grille proposée ;
2. cliquer sur l'avatar.

L'avatar est immédiatement associé au compte.

### Changer d'adresse e-mail

Dans le profil :

1. saisir la nouvelle adresse e-mail ;
2. saisir le mot de passe actuel ;
3. cliquer sur `Mettre à jour l'e-mail`.

L'adresse doit être valide et ne pas être déjà utilisée par un autre compte.

### Changer de mot de passe

Dans le profil :

1. saisir le mot de passe actuel ;
2. saisir le nouveau mot de passe ;
3. confirmer le nouveau mot de passe ;
4. cliquer sur `Mettre à jour le mot de passe`.

Le nouveau mot de passe doit respecter les mêmes règles que lors de l'inscription.

### Changer de pseudo

Dans le profil :

1. saisir un nouveau pseudo ;
2. cliquer sur `Mettre à jour le pseudo`.

Le pseudo doit être disponible. Il ne peut être modifié qu'une fois tous les 4 mois.

### Supprimer son compte

Dans le profil :

1. saisir le mot de passe actuel ;
2. cliquer sur `Supprimer mon compte` ;
3. confirmer la suppression.

La suppression est définitive. Si le compte est administrateur, la suppression est refusée lorsqu'il s'agit du dernier administrateur restant.

## Guide administrateur

### Accéder à l'administration

Pour accéder au back-office :

```text
http://172.20.0.102/admin
```

ou cliquer sur `Admin` dans l'en-tête après connexion.

Si un utilisateur non administrateur tente d'accéder à cette page, une page `403` est affichée.

### Créer ou attribuer un compte administrateur

Méthode recommandée depuis l'application :

1. se connecter avec un compte administrateur existant ;
2. aller dans `Admin` ;
3. ouvrir `Utilisateurs` ;
4. cliquer sur `Promouvoir admin` pour l'utilisateur souhaité.

### Tableau de bord

La page `Dashboard` affiche les indicateurs suivants :

- nombre total d'utilisateurs ;
- nombre d'administrateurs ;
- nombre total d'avis ;
- nombre total de destinations ;
- note moyenne globale ;
- nouveaux utilisateurs du mois ;
- avis récents non vérifiés ;
- utilisateurs récents.

Depuis le tableau de bord, l'administrateur peut :

- vérifier un avis ;
- supprimer un avis ;
- accéder à la liste des avis ;
- accéder à la liste des utilisateurs.

### Gérer les utilisateurs

Cliquer sur `utilisateurs`.

Fonctions disponibles :

- rechercher un utilisateur par pseudo ou e-mail ;
- consulter l'identifiant, le pseudo, l'e-mail, le rôle et la date de création ;
- promouvoir un utilisateur administrateur ;
- révoquer le rôle administrateur ;
- supprimer un utilisateur.

Restrictions :

- un administrateur ne peut pas modifier son propre statut depuis la liste ;
- un administrateur ne peut pas supprimer son propre compte depuis l'administration ;
- la suppression d'un utilisateur depuis le modèle administrateur supprime aussi ses avis associés.

### Gérer les destinations

Cliquer sur `destinations`.

La page permet :

- de lister les destinations ;
- de rechercher une destination ;
- d'ajouter une destination ;
- de modifier une destination ;
- de supprimer une destination.

Pour ajouter une destination, renseigner :

- nom ;
- pays ;
- ville ;
- description ;
- image de couverture par URL ;
- image locale JPG ou JPEG.

Pour une création de destination, les deux images sont obligatoires :

- `Image de couverture` : URL HTTP ou HTTPS ;
- `Image locale` : fichier `.jpg` ou `.jpeg`.

Pour modifier une destination :

1. cliquer sur `Modifier` ;
2. ajuster les champs ;
3. remplacer l'image URL ou l'image locale si nécessaire ;
4. cliquer sur `Enregistrer`.

Si la ville change, l'application tente de renommer l'image locale associée.

Pour supprimer une destination :

1. cliquer sur `Supprimer` ;
2. confirmer l'action.

La suppression d'une destination supprime aussi les avis liés.

### Gérer les avis

Cliquer sur `avis`.

Fonctions disponibles :

- afficher les avis non vérifiés ;
- afficher tous les avis ;
- rechercher un avis par utilisateur, e-mail, destination ou commentaire ;
- vérifier un avis ;
- repasser un avis en non vérifié ;
- supprimer un avis.

Le tableau de bord met en avant les avis non vérifiés pour faciliter la modération.

### Navigation administrateur

Le menu administrateur contient :

- `Dashboard` ;
- `Utilisateurs` ;
- `Destinations` ;
- `Avis` ;
- `Mon profil` ;
- `Déconnexion`.

### Recommandations d'administration

- Garder au moins deux comptes administrateurs actifs.
- Vérifier régulièrement les avis non modérés.
- Contrôler les images téléversées avant publication.
- Utiliser des mots de passe forts pour tous les comptes administrateurs.
- Ne pas utiliser un compte administrateur pour une navigation quotidienne si ce n'est pas nécessaire.

## Structure du projet

```text
Nomadix/
├── config/
│   ├── config.php
│   └── routes.php
├── controllers/
│   ├── AdminController.php
│   ├── AuthController.php
│   ├── DestinationController.php
│   └── HomeController.php
├── core/
│   └── Router.php
├── models/
│   ├── AdminModel.php
│   ├── Database.php
│   ├── DestinationModel.php
│   └── UserModel.php
├── public/
│   ├── css/
│   ├── images/
│   ├── js/
│   └── profil/
├── storage/
├── views/
├── Nomadix.sql
└── index.php
```

Rôle des dossiers :

- `config` : configuration, sessions, constantes et routes ;
- `controllers` : logique des pages et actions ;
- `models` : accès à la base de données ;
- `views` : affichage HTML ;
- `public` : CSS, JavaScript, images et avatars ;
- `storage` : fichiers de log ou stockage applicatif ;
- `core` : routeur interne.

## Base de données

Tables principales :

### `utilisateurs`

Stocke les comptes utilisateurs.

Champs principaux :

- `id` ;
- `login` ;
- `motDePasse` ;
- `email` ;
- `dateCreation` ;
- `login_changed_at` ;
- `avatar` ;
- `admin`.

### `destinations`

Stocke les destinations affichées dans l'application.

Champs principaux :

- `id` ;
- `nom` ;
- `description` ;
- `pays` ;
- `ville` ;
- `image`.

### `avis`

Stocke les avis des utilisateurs.

Champs principaux :

- `id` ;
- `idUtilisateur` ;
- `idDestination` ;
- `note` ;
- `commentaire` ;
- `dateAvis` ;
- `verified`.

## Sécurité et bonnes pratiques

Le projet utilise déjà plusieurs protections :

- mots de passe hachés avec `password_hash` ;
- vérification avec `password_verify` ;
- requêtes préparées PDO ;
- échappement HTML avec `htmlspecialchars` ;
- nettoyage des entrées utilisateur ;
- jetons CSRF sur plusieurs actions sensibles ;
- contrôle d'accès administrateur ;
- page `403` pour les accès refusés.

Points à surveiller avant une mise en production :

- activer HTTPS ;
- limiter la taille maximale des fichiers téléversés ;
