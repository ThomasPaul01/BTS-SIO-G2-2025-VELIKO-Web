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

 1. Transformer le **.env-example** en **.env** pour configurer vos variables locales

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
### 🎁Bonus : AppFixtures
Si besoin de creer automatiquement des users dans la base de donne :
```
symfony console doctrine:fixtures:load
```