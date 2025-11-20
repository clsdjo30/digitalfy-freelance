# 🧪 Tests - Guide de Démarrage Rapide

## ⚡ Démarrage Ultra Rapide

```bash
# 1. Démarrer MySQL (XAMPP ou service)
sudo /opt/lampp/lampp start        # XAMPP Linux
# ou
sudo systemctl start mysql         # Service MySQL

# 2. Lancer le script de test interactif
./bin/test-simple.sh
```

---

## ❌ Le script ne fonctionne pas ?

### Problème : MySQL n'est pas accessible

**Symptômes** :
- Erreur "Connection refused"
- Erreur "No such file or directory"
- Tests échouent immédiatement

**Solution** : Consultez le guide complet → **[DEMARRER_MYSQL.md](DEMARRER_MYSQL.md)**

---

## 📋 Scripts Disponibles

### 1️⃣ `./bin/test-simple.sh` ✨ (Recommandé)

**Script interactif qui** :
- ✅ Vérifie que MySQL est démarré
- ✅ Donne des instructions si MySQL n'est pas accessible
- ✅ Permet de choisir les tests à exécuter
- ✅ Plus simple à utiliser

```bash
./bin/test-simple.sh
```

### 2️⃣ `./bin/run-tests.sh` (Avancé)

**Script complet qui** :
- ✅ Exécute tous les tests automatiquement
- ✅ Génère des rapports détaillés
- ✅ Crée des rapports Markdown
- ⚠️ Nécessite MySQL démarré

```bash
./bin/run-tests.sh
```

### 3️⃣ PHPUnit direct

```bash
# Tous les tests
php bin/phpunit --testdox

# Un test spécifique
php bin/phpunit tests/Functional/NavigationTest.php --testdox

# Avec couverture de code
php bin/phpunit --coverage-html var/coverage
```

---

## 🎯 Tests Disponibles

| Test | Fichier | Description |
|------|---------|-------------|
| **Navigation** | `NavigationTest.php` | Liens, pagination, pages |
| **Formulaire** | `ContactFormTest.php` | Contact, validation, CSRF |
| **Admin** | `AdminTest.php` | Backoffice, auth, CRUD |
| **SEO** | `SEOTest.php` | Meta, schema, sitemap |
| **Sécurité** | `SecurityTest.php` | OWASP, XSS, CSRF |
| **Accessibilité** | `AccessibilityTest.php` | WCAG 2.1, ARIA |

---

## 📚 Documentation Complète

- **[DEMARRER_MYSQL.md](DEMARRER_MYSQL.md)** - Guide pour démarrer MySQL/XAMPP
- **[docs/TESTS.md](docs/TESTS.md)** - Documentation complète des tests
- **[docs/SETUP_TESTS.md](docs/SETUP_TESTS.md)** - Configuration de l'environnement
- **[tests/README.md](tests/README.md)** - Référence des tests

---

## 🔧 Configuration Minimale

Créez le fichier `.env.test.local` :

```env
# XAMPP (localhost recommandé)
DATABASE_URL="mysql://digitalfy:digitalfy_password@localhost:3306/digitalfy_test?serverVersion=8.0&charset=utf8mb4"

APP_ENV=test
APP_DEBUG=false
MAILER_DSN=null://null
APP_SECRET=test_secret_key
```

---

## 🚀 Workflow Complet

### Première fois

```bash
# 1. Démarrer MySQL
sudo /opt/lampp/lampp start

# 2. Créer l'utilisateur et la base de données
mysql -u root
```

```sql
CREATE USER IF NOT EXISTS 'digitalfy'@'localhost' IDENTIFIED BY 'digitalfy_password';
CREATE DATABASE IF NOT EXISTS digitalfy_test;
GRANT ALL PRIVILEGES ON digitalfy_test.* TO 'digitalfy'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# 3. Initialiser la base de données de test
php bin/console doctrine:database:create --env=test --if-not-exists
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-interaction

# 4. Lancer les tests
./bin/test-simple.sh
```

### Utilisation quotidienne

```bash
# Démarrer MySQL si nécessaire
sudo /opt/lampp/lampp start

# Lancer les tests
./bin/test-simple.sh
```

---

## ⚠️ Problèmes Courants

### ❌ "Connection refused"

**Cause** : MySQL n'est pas démarré

**Solution** :
```bash
# XAMPP
sudo /opt/lampp/lampp start

# Service
sudo systemctl start mysql

# Vérifier
ps aux | grep mysql
```

### ❌ "Access denied for user 'digitalfy'"

**Cause** : L'utilisateur n'existe pas ou mauvais mot de passe

**Solution** : Voir [DEMARRER_MYSQL.md](DEMARRER_MYSQL.md#configuration-de-la-base-de-données)

### ❌ "Unknown database 'digitalfy_test'"

**Cause** : Base de données non créée

**Solution** :
```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test --no-interaction
```

### ❌ Le script ne fait rien

**Causes possibles** :
1. Script pas exécutable → `chmod +x bin/test-simple.sh`
2. MySQL pas démarré → Voir [DEMARRER_MYSQL.md](DEMARRER_MYSQL.md)
3. Erreur silencieuse → Essayer `bash bin/test-simple.sh`

---

## 💡 Astuces

### Exécuter un seul test

```bash
php bin/phpunit tests/Functional/NavigationTest.php --filter testHomePageLoads
```

### Format lisible

```bash
php bin/phpunit --testdox
```

### Arrêter au premier échec

```bash
php bin/phpunit --stop-on-failure
```

### Mode verbeux

```bash
php bin/phpunit --testdox --verbose
```

### Avec couverture

```bash
php bin/phpunit --coverage-text
```

---

## 🎓 Ressources

- [Documentation PHPUnit](https://phpunit.de/documentation.html)
- [Symfony Testing](https://symfony.com/doc/current/testing.html)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [WCAG 2.1](https://www.w3.org/WAI/WCAG21/quickref/)

---

**Besoin d'aide ?** Consultez [DEMARRER_MYSQL.md](DEMARRER_MYSQL.md) pour un guide détaillé.
