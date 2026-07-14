# GREEN GOODIES
# Option B, site fictifs
# Principales informations du projet :
#
# Controlleurs :
## Main : Route : app_home 
###  - Affichage de la page d'accueil (Section Hero / Section Values / Liste de produits)
## User : Route : app_account_ (register|login|logout|index|api|delete)
###  - Utilisation des methodes présentes dans Services/UserService.php
###  - Inscription / Connexion (login) / Deconnexion (Logout)
###  - Page "Mon Compte" (Liste des commandes / Parametrage Accès API / Suppression de compte)
## Product : Route : app_product_show
###  - Utilisation des methodes présentes dans Service/ProductService.php
###  - Affichage du detail d'un produit, formulaire d'ajout dans le panier
## Cart : Route : app_cart_ (index|add|remove|add_from_product|delete|empty)
###  - Utilisation des methodes presentes dans Service/CartService.php
###  - Affichage du panier
###  - Gestion du panier / Creation de la commande
## API : 
###  - Gestion pre authorisation (Security/ApiUserChecker.php) pour empecher l'accès API aux utilisateurs supprimés (logiquement) et aux ###    utilisateurs qui n'ont pas l'accès API activé.
###  - Gestion des autorisations avec le composant Lexik, en principe uniquement ceux qui ont l'accès API actif arrivent ici, le composant ###    lexik fournit les jetons d'autorisations afin de pouvoir utiliser l'API
### Route : api_produtcs
### - retourne la liste des produits, lors d'un appel API autorisé (existance d'un jeton valide)
#
# Base de donnees MySQL
# parametrage acces BDD dans env.test
# symfony console doctrine:dabatabase:create (creation de la base de données)
# symfony console make:migation (recuperation de la structures des tables associees)
# symfony console doctrine:migrations:migrate (creation/mise a jour des tables)
# symfony console doctrine:fixtures:load (chargment d'un jeu de donnees, ici produits et utilisateur avec differentes configurations)
#
# Utilisation de Asset Mapper pour la gestion des CSS / JS / Images
##  - Regeneration des fichiers dans public/asset par symfony console asset-map:compile
#
# Rajout des composants suivants (en plus de l'installation par defaut, avec --webapp): 
##  - Asset Mapper
##  - FixturesBundle, pour pour ajouter des donnees dans la base de données
##  - Lexik, pour gerer les acces APi avec les jetons JWT