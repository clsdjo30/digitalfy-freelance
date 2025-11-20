# Configuration de l'environnement de test

## Prérequis

Pour exécuter les tests, vous devez avoir :

1. **PHP 8.2+** avec les extensions :
   - pdo_mysql (pour MySQL/MariaDB)
   - xml
   - mbstring
   - curl

2. **Composer** installé

3. **Une base de données** :
   - MySQL 8.0+ ou MariaDB 10.11+
   - PostgreSQL 15+ (alternative)
   - SQLite 3+ (alternative, nécessite php-sqlite3)

## Configuration

### Option 1 : Docker Compose (recommandé)

Si vous utilisez Docker, lancez simplement :

```bash
docker compose up -d database
```

La base de données sera accessible sur `localhost:3306` avec les identifiants :
- Utilisateur : `digitalfy`
- Mot de passe : `digitalfy_password`

### Option 2 : Base de données locale

Si vous avez MySQL/MariaDB installé localement :

1. Créez un utilisateur de base de données :
```sql
CREATE USER 'digitalfy'@'localhost' IDENTIFIED BY 'digitalfy_password';
GRANT ALL PRIVILEGES ON digitalfy_test.* TO 'digitalfy'@'localhost';
FLUSH PRIVILEGES;
```

2. Vérifiez que le fichier `.env.test.local` existe avec la configuration :
```env
DATABASE_URL="mysql://digitalfy:digitalfy_password@127.0.0.1:3306/digitalfy_test?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```

### Option 3 : SQLite (plus simple)

Si vous voulez utiliser SQLite (ne nécessite pas de serveur) :

1. Installez l'extension SQLite :
```bash
# Ubuntu/Debian
sudo apt-get install php-sqlite3

# macOS (avec Homebrew)
brew install php-sqlite3
```

2. Modifiez `.env.test.local` :
```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_test.db"
```

## Initialisation de la base de données de test

Une fois la base de données configurée, initialisez-la :

```bash
# Créer la base de données
php bin/console doctrine:database:create --env=test --if-not-exists

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --env=test --no-interaction

# Charger les fixtures de test
php bin/console doctrine:fixtures:load --env=test --no-interaction
```

## Exécution des tests

### Méthode rapide

Le script automatisé s'occupe de tout :

```bash
./bin/run-tests.sh
```

### Exécution manuelle

```bash
# Tous les tests
php bin/phpunit

# Une suite spécifique
php bin/phpunit tests/Functional/NavigationTest.php

# Format lisible
php bin/phpunit --testdox

# Avec couverture de code (nécessite Xdebug)
php bin/phpunit --coverage-html var/coverage
```

## Dépannage

### Erreur "Connection refused"

La base de données n'est pas démarrée. Vérifiez :

```bash
# Avec Docker
docker compose ps

# Service local
sudo systemctl status mysql
# ou
sudo systemctl status mariadb
```

### Erreur "could not find driver"

Le driver PDO n'est pas installé :

```bash
# Ubuntu/Debian
sudo apt-get install php-mysql

# macOS (avec Homebrew)
brew install php-mysql
```

### Erreur "Access denied"

Les identifiants de la base de données sont incorrects. Vérifiez `.env.test.local`.

### Tests échouent avec "Class not found"

Regénérez l'autoloader :

```bash
composer dump-autoload
```

## Configuration CI/CD

Pour l'intégration continue (GitHub Actions, GitLab CI, etc.) :

### GitHub Actions

```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mariadb:10.11
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: digitalfy_test
          MYSQL_USER: digitalfy
          MYSQL_PASSWORD: digitalfy_password
        ports:
          - 3306:3306
        options: --health-cmd="healthcheck.sh --connect" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: pdo_mysql, mbstring, xml

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run tests
        run: ./bin/run-tests.sh
        env:
          DATABASE_URL: mysql://digitalfy:digitalfy_password@127.0.0.1:3306/digitalfy_test?serverVersion=10.11.2-MariaDB
```

## Nettoyage

Pour nettoyer la base de données de test :

```bash
# Supprimer la base de données
php bin/console doctrine:database:drop --env=test --force

# Supprimer le fichier SQLite (si utilisé)
rm var/data_test.db
```

## Variables d'environnement de test

Le fichier `.env.test.local` devrait contenir :

```env
# Base de données
DATABASE_URL="mysql://digitalfy:digitalfy_password@127.0.0.1:3306/digitalfy_test?serverVersion=10.11.2-MariaDB&charset=utf8mb4"

# Environnement
APP_ENV=test
APP_DEBUG=false

# Mailer (désactivé en test)
MAILER_DSN=null://null

# Secret
APP_SECRET=test_secret_key_for_testing_purposes_only

# Analytics (désactivé en test)
GOOGLE_ANALYTICS_ID=
GOOGLE_SEARCH_CONSOLE_ENABLED=false
```

**Important** : Ce fichier ne doit PAS être commité (il est dans `.gitignore`).

## Tests de performance

Pour tester les performances avec Apache Bench :

```bash
# Installer Apache Bench
sudo apt-get install apache2-utils

# Test simple
ab -n 1000 -c 10 http://localhost:8000/

# Test avec rapport
ab -n 1000 -c 10 -g results.tsv http://localhost:8000/
```

Pour Lighthouse :

```bash
npm install -g lighthouse

lighthouse http://localhost:8000 --view
```

## Ressources

- [Documentation PHPUnit](https://phpunit.de/documentation.html)
- [Symfony Testing](https://symfony.com/doc/current/testing.html)
- [Doctrine Testing](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/testing.html)

---

*Dernière mise à jour : 2025-11-20*
