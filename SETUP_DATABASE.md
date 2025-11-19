# Configuration de la Base de Données - Digitalfy

## 🎯 Situation Actuelle

Votre environnement PHP dispose de :
- ✅ **PDO MySQL** (compatible MariaDB)
- ✅ **PDO PostgreSQL**
- ❌ **PDO SQLite** (non installé)

## 📦 Options de Base de Données

### Option 1 : MariaDB avec Docker (RECOMMANDÉ) ✨

C'est l'option la plus simple et la plus proche de la production.

#### 1. Démarrer le conteneur MariaDB

```bash
docker compose up -d database
```

#### 2. Vérifier que le conteneur fonctionne

```bash
docker compose ps
```

Vous devriez voir :
```
NAME                      STATUS
digitalfy-database-1      Up (healthy)
```

#### 3. Configurer la connexion (déjà fait dans .env)

Le fichier `.env` est déjà configuré pour MariaDB Docker. Si vous devez le changer, créez un `.env.local` :

```env
# .env.local
DATABASE_URL="mysql://digitalfy:digitalfy_password@127.0.0.1:3306/digitalfy_db?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```

#### 4. Créer la base de données et exécuter les migrations

```bash
# Créer la base de données (si nécessaire)
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les données de test
php bin/console doctrine:fixtures:load
```

#### 5. Arrêter le conteneur (quand vous ne travaillez plus)

```bash
docker compose down
# ou pour conserver les données :
docker compose stop
```

---

### Option 2 : Installer PDO SQLite

Si vous préférez vraiment utiliser SQLite, vous devez installer l'extension PHP.

#### Sur Ubuntu/Debian :
```bash
sudo apt-get update
sudo apt-get install php8.4-sqlite3
# Redémarrer PHP-FPM si nécessaire
sudo service php8.4-fpm restart
```

#### Sur macOS (avec Homebrew) :
```bash
brew install php
# SQLite est généralement inclus
```

#### Puis modifier `.env` :
```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_dev.db"
```

---

### Option 3 : MariaDB/MySQL local

Si vous avez déjà MariaDB ou MySQL installé localement :

#### 1. Créer la base de données

```bash
mysql -u root -p
CREATE DATABASE digitalfy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'digitalfy'@'localhost' IDENTIFIED BY 'digitalfy_password';
GRANT ALL PRIVILEGES ON digitalfy_db.* TO 'digitalfy'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 2. Configurer `.env.local`

```env
DATABASE_URL="mysql://digitalfy:digitalfy_password@127.0.0.1:3306/digitalfy_db?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```

#### 3. Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

---

## 🚀 Démarrage Rapide avec Docker (Recommandé)

Voici la séquence complète pour démarrer :

```bash
# 1. Démarrer MariaDB
docker compose up -d database

# 2. Attendre que le conteneur soit "healthy" (environ 30 secondes)
docker compose ps

# 3. Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# 4. Charger les données de test
php bin/console doctrine:fixtures:load --no-interaction

# 5. Vérifier que tout fonctionne
php bin/console doctrine:query:sql "SELECT COUNT(*) FROM category"
```

---

## 🔄 Basculer entre les Configurations

### Utiliser Docker MariaDB
```env
# Dans .env.local
DATABASE_URL="mysql://digitalfy:digitalfy_password@127.0.0.1:3306/digitalfy_db?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```

### Utiliser SQLite (si installé)
```env
# Dans .env.local
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_dev.db"
```

---

## 🛠️ Commandes Utiles

### Docker

```bash
# Démarrer
docker compose up -d database

# Voir les logs
docker compose logs -f database

# Se connecter à la base
docker compose exec database mysql -u digitalfy -pdigitalfy_password digitalfy_db

# Arrêter (garde les données)
docker compose stop

# Arrêter et supprimer (perd les données)
docker compose down

# Supprimer aussi les volumes (réinitialisation totale)
docker compose down -v
```

### Doctrine

```bash
# Créer la base
php bin/console doctrine:database:create

# Supprimer la base
php bin/console doctrine:database:drop --force

# Voir le statut des migrations
php bin/console doctrine:migrations:status

# Créer une nouvelle migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures
php bin/console doctrine:fixtures:load

# Vider et recharger la base
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

---

## ⚙️ Configuration Docker Compose

Le fichier `compose.yaml` est configuré avec :

- **Image** : MariaDB 10.11
- **Port** : 3306 (accessible depuis l'hôte)
- **Base de données** : digitalfy_db
- **Utilisateur** : digitalfy
- **Mot de passe** : digitalfy_password
- **Mot de passe root** : root

Variables personnalisables dans `.env.local` :
```env
MARIADB_VERSION=10.11
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=digitalfy_db
MYSQL_USER=digitalfy
MYSQL_PASSWORD=digitalfy_password
```

---

## 📝 Notes

- **Développement** : Docker MariaDB est recommandé (facile à démarrer/arrêter, proche de la production)
- **Tests rapides** : SQLite serait idéal mais nécessite l'installation de l'extension PHP
- **Production** : Utilisez MariaDB ou PostgreSQL sur un serveur dédié

---

## ❓ Problèmes Courants

### "could not find driver"
→ Le driver PDO nécessaire n'est pas installé. Utilisez Docker MariaDB ou installez l'extension PHP manquante.

### "Connection refused" avec Docker
→ Le conteneur n'est peut-être pas démarré ou pas encore "healthy".
```bash
docker compose up -d database
docker compose ps  # Vérifier le statut
```

### "Access denied for user"
→ Vérifiez vos identifiants dans `DATABASE_URL`.

### Les migrations échouent
→ Vérifiez que la base de données existe :
```bash
php bin/console doctrine:database:create
```

---

*Document créé le 2025-11-18*
