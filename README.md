## Installation

Utilisez [composer](https://getcomposer.org/) pour installer MyWishList.
adresse du site webetu: https://webetu.iutnc.univ-lorraine.fr/www/chagras1u/mywishlist/
```bash
git clone https://github.com/Flachag/mywishlist.git
cd MyWishList
composer install
```

Il faut créer un fichier de configuration pour la base de donnée nommé **config.ini** dans le répertoire /mywishlist/app/config.
En insérant:

| Paramètre     | Valeur d'exemple | Description               |
| :------------:|:----------------:|:-------------------------:|
| driver        | mysql            | Driver de votre SGBD      |
| host          | localhost        | Hôte de votre BDD         |
| database      | wishlist         | Nom de votre BDD          |
| username      | root             | Nom d'user de votre BDD   |
| password      | root             | Mot de passe de votre BDD |
| charset       | utf8             | Méthode d'encodage        |
| collation     | utf8_unicode_ci  | Collation de la BDD       |

## Fonctionnalités
####Participant
- [X] ~~**1. Afficher une liste de souhaits _[FAIT @Flavien]_**~~
- [X] ~~**2. Afficher un item d'une liste _[FAIT @Flavien]_**~~
- [X] ~~**3. Réserver un item d'une liste _[FAIT @Flavien]_**~~
- [X] ~~**4. Ajouter un message avec sa réservation _[FAIT @Flavien]_**~~
- [X] ~~**5. Ajouter un message dans une liste _[FAIT @Flavien]_**~~
####Créateur
- [X] ~~**6. Créer une liste _[FAIT @Flavien]_**~~
- [X] ~~**7. Modifier les informations générales d'une de ses listes _[FAIT @Lucas & @Flavien]_**~~
- [X] ~~**8. Ajouter des items _[FAIT @Flavien]_**~~
- [X] ~~**9. Modifier un item _[FAIT @Lucas]_**~~
- [X] ~~**10. Supprimer un item _[FAIT @Lucas]_**~~
- [X] ~~**11. Rajouter une image à un item _[FAIT @Lucas]_**~~
- [X] ~~**12. Modifier une image d'un item _[FAIT @Lucas]_**~~
- [X] ~~**13. Supprimer une image d'un item _[FAIT @Lucas]_**~~
- [X] ~~**14. Partager une liste _[FAIT @Flavien]_**~~
- [X] ~~**15. Consulter les réservations d'une de ses listes avant échéance _[FAIT @Flavien]_**~~
- [X] ~~**16. Consulter les réservations et messages d'une de ses listes après échéance _[FAIT @Flavien]_**~~
####Extensions
- [X] ~~**17. Créer un compte _[FAIT @Flavien]_**~~
- [X] ~~**18. S'authentifier _[FAIT @Flavien]_**~~
- [X] ~~**19. Modifier son compte _[FAIT @Lucas]_**~~
- [X] ~~**20. Rendre une liste publique _[FAIT @Flavien]_**~~
- [X] ~~**21. Afficher les listes de souhaits publiques _[FAIT @Flavien]_**~~
- [X] ~~**26. Afficher la liste des créateurs _[FAIT @Lucas]_**~~
- [X] ~~**27 Supprimer son compte _[FAIT @Lucas]_**~~