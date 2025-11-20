# 🧪 Tests sur Windows avec XAMPP - Guide Rapide

## ⚡ Configuration Rapide (5 minutes)

### 1️⃣ Configurer MySQL XAMPP

Éditez `C:\xampp\mysql\bin\my.ini` :

```ini
# Cherchez cette ligne :
bind-address = 127.0.0.1

# Changez-la en :
bind-address = 0.0.0.0
```

**Redémarrez MySQL** dans XAMPP Control Panel.

### 2️⃣ Trouver votre IP Windows

Dans PowerShell :
```powershell
ipconfig
```

Notez votre **IPv4** (ex: `192.168.1.100`)

### 3️⃣ Configurer .env.test.local

Éditez `.env.test.local` et remplacez cette ligne :

```env
DATABASE_URL="mysql://root:@host.docker.internal:3306/digitalfy_db_test?serverVersion=mariadb-10.4.32&charset=utf8mb4"
```

Par (en utilisant VOTRE IP Windows) :

```env
DATABASE_URL="mysql://root:@192.168.1.100:3306/digitalfy_db_test?serverVersion=mariadb-10.4.32&charset=utf8mb4"
```

### 4️⃣ Tester

```bash
# Tester la connexion
php -r '$pdo = new PDO("mysql:host=192.168.1.100;port=3306", "root", ""); echo "✓ OK!\n";'

# Initialiser la base de test
php bin/console doctrine:database:create --env=test --if-not-exists
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-interaction

# Lancer les tests
./bin/test-simple.sh
```

## ❓ Problèmes ?

**Connexion refusée ?** → Voir [CONFIGURATION_MYSQL_WINDOWS.md](CONFIGURATION_MYSQL_WINDOWS.md)

**Pare-feu ?** → Autorisez le port 3306 dans le Pare-feu Windows

**IP change ?** → Configurez une IP statique dans Windows

---

📚 **Documentation complète** : [CONFIGURATION_MYSQL_WINDOWS.md](CONFIGURATION_MYSQL_WINDOWS.md)
