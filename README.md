# Projet Veliko 

## Installation 

### Etape 1  : Cloner le projet
```bash
git clone git@github.com:ort-montreuil/BTS-SIO-G2-2025-VELIKO-Web.git
```
### Etape 2 :Installation des dépendances 


Installation des dépendances avec composer(vendor) 
```bash
composer install
```

### Etape 3 : Initialisation du fichier

1. Copier le **.env-example** en **.env** pour configurer vos variables locales

2. Modifiez les variables du .env selon votre environnement local

⚠️ Obligatoire : 
   - APP_SECRET
   - DATABASE_URL

### Etape 4 : Installation BDD
Création, lancement des containers
```
docker-compose up -d
```
Exécutez la migration
```
symfony console doctrine:migrations:migrate
```

**Info :** Commande de lancement et d'arret Symfony :

Pour lancer le serveur
```
symfony server:start
```
Pour arreter le serveur
```
symfony server:stop
```
Arreter containers and supprimer containers
``` 
docker-compose down
```
**Mailer :** Acceder a la boite mail via cette url

http://localhost:8025/

### 🎁Bonus : AppFixtures
Si besoin de creer automatiquement **des users et admin** dans la base de donne: (⚠️Avant le lancement du site pour ne pas vider la table station)
```
symfony console doctrine:fixtures:load
```
Si besoin de creer automatiquement **des réservations** pour tout les utilisateurs: (⚠️Apres la creation d'utilisateur et du lancement du site)
```
symfony console d:f:l --group=Reservation --append
```
Si besoin de creer automatiquement **des favoris** pour tout les utilisateurs: (⚠️Apres la creation d'utilisateur et du lancement du site)
```
symfony console d:f:l --group=Favorite --append
```
Test push for webHook 

