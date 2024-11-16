# Déploiement du Projet Symfony

Suivez les étapes ci-dessous pour déployer votre projet Symfony sur le serveur de production.

## 1. Téléchargez votre code sur le serveur de production
Transférez l'ensemble des fichiers du projet (via FTP, SCP, ou autre méthode) sur votre serveur de production.

## 2. Vérifier les exigences
Installez PHP 8.2 ainsi que Composer, qui est utilisé pour installer des packages PHP.

## 3. Création du fichier `.env`
Créez et modifiez le fichier `.env` à la racine de votre projet pour configurer les variables d'environnement nécessaires. Voici un exemple :

```env
APP_ENV=prod
APP_DEBUG=0

DATABASE_URL="mysql://[username]:[password]@[host]:[port]/[database_name]?serverVersion=mariadb-[version]"
```

## 4 . Installez les dépendances des fournisseurs
Installezz ou mettez à jour les dépendances du projet avec Composer. Cette étape peut être réalisée avant ou après le téléchargement du code sur le serveur.

```
composer install --no-dev --optimize-autoloader
```

## 5. Exécutez les migrations de base de données
Appliquez les migrations pour mettre à jour la structure de la base de données :

```
php bin/console doctrine:migrations:migrate
```

## 6. Videz le cache Symfony
Effacez le cache de votre application Symfony pour le mode production :
```
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
```