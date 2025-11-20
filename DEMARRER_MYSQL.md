# 🚀 Guide de démarrage MySQL pour les tests

## ⚠️ Problème actuel

Les tests ne fonctionnent pas car **MySQL n'est pas démarré** sur votre système.

## 🔍 Diagnostic

Vérifiez si MySQL est en cours d'exécution :

```bash
# Vérifier les processus MySQL
ps aux | grep mysql

# Vérifier le port 3306
sudo netstat -tuln | grep 3306
# ou
sudo ss -tuln | grep 3306
```

---

## ✅ Solutions selon votre environnement

### Option 1 : XAMPP (recommandé si installé)

#### Sur Linux

```bash
# Démarrer tous les services XAMPP
sudo /opt/lampp/lampp start

# Démarrer uniquement MySQL
sudo /opt/lampp/lampp startmysql

# Vérifier le statut
sudo /opt/lampp/lampp status

# Interface graphique
sudo /opt/lampp/manager-linux-x64.run
```

#### Sur Windows

1. Ouvrez le **XAMPP Control Panel**
2. Cliquez sur le bouton **Start** à côté de **MySQL**
3. Attendez que le statut devienne vert

Ou en ligne de commande :
```cmd
cd C:\xampp
xampp_start.exe
```

#### Sur macOS

```bash
# Démarrer XAMPP
sudo /Applications/XAMPP/xamppfiles/xampp start

# Démarrer uniquement MySQL
sudo /Applications/XAMPP/xamppfiles/xampp startmysql
```

---

### Option 2 : MySQL système

Si vous avez MySQL/MariaDB installé en tant que service :

#### Ubuntu / Debian

```bash
# Démarrer MySQL
sudo systemctl start mysql

# Vérifier le statut
sudo systemctl status mysql

# Démarrer au boot (optionnel)
sudo systemctl enable mysql

# Alternative avec service
sudo service mysql start
sudo service mysql status
```

#### CentOS / RHEL / Fedora

```bash
# Démarrer MariaDB
sudo systemctl start mariadb

# Vérifier le statut
sudo systemctl status mariadb
```

#### macOS (Homebrew)

```bash
# Démarrer MySQL
brew services start mysql

# Vérifier le statut
brew services list

# Arrêter (si besoin)
brew services stop mysql
```

#### Windows (service)

```cmd
# Démarrer le service MySQL
net start MySQL

# Vérifier le statut
sc query MySQL
```

---

### Option 3 : Docker (si vous préférez)

```bash
# Démarrer le conteneur Docker
docker compose up -d database

# Vérifier que le conteneur est démarré
docker compose ps

# Voir les logs
docker compose logs database

# Arrêter (quand vous avez fini)
docker compose down
```

---

## 🔐 Configuration de la base de données

Une fois MySQL démarré, créez l'utilisateur et la base de données de test :

```bash
# Se connecter à MySQL en tant que root
mysql -u root -p
# ou avec XAMPP (souvent pas de mot de passe)
mysql -u root
```

Puis exécutez ces commandes SQL :

```sql
-- Créer l'utilisateur (si pas déjà fait)
CREATE USER IF NOT EXISTS 'digitalfy'@'localhost' IDENTIFIED BY 'digitalfy_password';

-- Créer la base de données de test
CREATE DATABASE IF NOT EXISTS digitalfy_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Donner les droits
GRANT ALL PRIVILEGES ON digitalfy_test.* TO 'digitalfy'@'localhost';
GRANT ALL PRIVILEGES ON digitalfy_db.* TO 'digitalfy'@'localhost';

-- Appliquer les changements
FLUSH PRIVILEGES;

-- Vérifier
SHOW DATABASES LIKE 'digitalfy%';

-- Quitter
EXIT;
```

---

## 🧪 Initialiser la base de données de test

Une fois MySQL démarré et l'utilisateur créé :

```bash
# Se placer dans le dossier du projet
cd /home/user/digitalfy-freelance

# Créer la base de données de test
php bin/console doctrine:database:create --env=test --if-not-exists

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --env=test --no-interaction

# Charger les fixtures de test
php bin/console doctrine:fixtures:load --env=test --no-interaction
```

---

## ✨ Lancer les tests

Une fois tout configuré, utilisez le nouveau script simplifié :

```bash
# Script interactif (recommandé)
./bin/test-simple.sh

# Ou directement avec PHPUnit
php bin/phpunit --testdox

# Un test spécifique
php bin/phpunit tests/Functional/NavigationTest.php --testdox
```

---

## 🔧 Dépannage

### Erreur "Connection refused"

MySQL n'est pas démarré. Suivez les instructions ci-dessus pour démarrer MySQL.

### Erreur "Access denied for user 'digitalfy'"

L'utilisateur n'existe pas ou le mot de passe est incorrect.

**Solution** :
```bash
mysql -u root -p
```

Puis :
```sql
DROP USER IF EXISTS 'digitalfy'@'localhost';
CREATE USER 'digitalfy'@'localhost' IDENTIFIED BY 'digitalfy_password';
GRANT ALL PRIVILEGES ON *.* TO 'digitalfy'@'localhost';
FLUSH PRIVILEGES;
```

### Erreur "Unknown database 'digitalfy_test'"

La base de données n'existe pas.

**Solution** :
```bash
php bin/console doctrine:database:create --env=test
```

### XAMPP MySQL ne démarre pas

**Causes possibles** :
1. Port 3306 déjà utilisé par un autre service
2. Fichiers de log corrompus
3. Permissions incorrectes

**Solutions** :

```bash
# Vérifier quel processus utilise le port 3306
sudo lsof -i :3306

# Changer le port dans XAMPP (dans my.cnf)
sudo nano /opt/lampp/etc/my.cnf
# Modifier : port = 3307

# Nettoyer les logs XAMPP
sudo rm -rf /opt/lampp/var/mysql/*.err

# Réinitialiser les permissions
sudo chown -R nobody:nogroup /opt/lampp/var/mysql
```

### Vérifier la configuration

```bash
# Afficher la configuration actuelle
cat .env.test.local

# Tester la connexion PHP
php -r "
\$pdo = new PDO('mysql:host=localhost', 'digitalfy', 'digitalfy_password');
echo 'Connexion OK\n';
echo 'Version: ' . \$pdo->query('SELECT VERSION()')->fetchColumn() . '\n';
"
```

---

## 📝 Configuration pour XAMPP

Si vous utilisez XAMPP, votre fichier `.env.test.local` devrait contenir :

```env
# Avec localhost (recommandé pour XAMPP)
DATABASE_URL="mysql://digitalfy:digitalfy_password@localhost:3306/digitalfy_test?serverVersion=8.0&charset=utf8mb4"

# Ou avec 127.0.0.1
DATABASE_URL="mysql://digitalfy:digitalfy_password@127.0.0.1:3306/digitalfy_test?serverVersion=8.0&charset=utf8mb4"

APP_ENV=test
APP_DEBUG=false
MAILER_DSN=null://null
APP_SECRET=test_secret_key_for_testing_purposes_only
```

**Note** : Changez `serverVersion=8.0` selon votre version de MySQL :
- MySQL 5.7 : `serverVersion=5.7`
- MySQL 8.0 : `serverVersion=8.0`
- MariaDB 10.11 : `serverVersion=10.11.2-MariaDB`

---

## 🎯 Checklist rapide

- [ ] MySQL est démarré (`ps aux | grep mysql`)
- [ ] Port 3306 est accessible (`netstat -tuln | grep 3306`)
- [ ] Utilisateur 'digitalfy' existe
- [ ] Base de données 'digitalfy_test' existe
- [ ] Fichier `.env.test.local` est configuré
- [ ] Les migrations sont exécutées
- [ ] Les fixtures sont chargées

---

## 📞 Besoin d'aide ?

Si rien ne fonctionne, contactez-moi avec :

1. Votre système d'exploitation
2. La sortie de : `php -v`
3. La sortie de : `mysql --version` ou `mysqld --version`
4. La sortie de : `ps aux | grep mysql`
5. Le contenu de votre `.env.test.local`
6. Les erreurs exactes que vous rencontrez

---

*Dernière mise à jour : 2025-11-20*
