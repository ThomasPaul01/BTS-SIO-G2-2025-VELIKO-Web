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

### Etape 3 : Installation BDD
creation, lancement des containers
```
docker-compose up -d
```
Executez la migration
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
Arreter containers and supprime containers
```
docker-compose down
```
