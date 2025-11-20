# 🔧 Configuration MySQL XAMPP pour Tests depuis Conteneur Linux

## 🎯 Le Problème

Vous utilisez **Claude Code dans un conteneur Linux** sur **Windows 11**.
XAMPP MySQL tourne sur Windows, mais le conteneur Linux ne peut pas y accéder via `127.0.0.1`.

## ✅ Solution en 3 étapes

### Étape 1 : Configurer MySQL pour écouter sur toutes les interfaces

1. **Ouvrez le fichier de configuration MySQL :**
   ```
   C:\xampp\mysql\bin\my.ini
   ```

2. **Cherchez la ligne :**
   ```ini
   bind-address = 127.0.0.1
   ```

3. **Changez-la en :**
   ```ini
   bind-address = 0.0.0.0
   ```

   Ou commentez-la :
   ```ini
   # bind-address = 127.0.0.1
   ```

4. **Sauvegardez le fichier**

5. **Redémarrez MySQL dans XAMPP Control Panel :**
   - Cliquez sur "Stop" à côté de MySQL
   - Attendez 2-3 secondes
   - Cliquez sur "Start"

### Étape 2 : Trouver l'IP de votre PC Windows

1. **Ouvrez PowerShell** sur Windows (Win + X → PowerShell)

2. **Tapez :**
   ```powershell
   ipconfig
   ```

3. **Cherchez votre adresse IPv4** (généralement quelque chose comme `192.168.x.x` ou `10.x.x.x`)

   Exemple de sortie :
   ```
   Carte Ethernet Ethernet :
      Adresse IPv4. . . . . . . . . . . . : 192.168.1.100
   ```

4. **Notez cette adresse IP** (ex: `192.168.1.100`)

### Étape 3 : Mettre à jour .env.test.local

1. **Éditez le fichier `.env.test.local`** dans votre projet

2. **Remplacez la ligne `DATABASE_URL` par :**
   ```env
   DATABASE_URL="mysql://root:@VOTRE_IP_WINDOWS:3306/digitalfy_db_test?serverVersion=mariadb-10.4.32&charset=utf8mb4"
   ```

3. **Remplacez `VOTRE_IP_WINDOWS` par l'IP trouvée à l'étape 2**

   Exemple :
   ```env
   DATABASE_URL="mysql://root:@192.168.1.100:3306/digitalfy_db_test?serverVersion=mariadb-10.4.32&charset=utf8mb4"
   ```

4. **Sauvegardez le fichier**

## 🧪 Tester la connexion

Depuis le terminal du conteneur, exécutez :

```bash
php -r '$pdo = new PDO("mysql:host=VOTRE_IP;port=3306", "root", ""); echo "✓ Connexion OK!\n";'
```

Remplacez `VOTRE_IP` par l'IP de votre Windows.

Si ça fonctionne, vous verrez :
```
✓ Connexion OK!
```

## 🛡️ Pare-feu Windows (si la connexion échoue)

Si après les étapes ci-dessus ça ne fonctionne toujours pas :

1. **Ouvrez le Pare-feu Windows** (Panneau de configuration → Système et sécurité → Pare-feu Windows)

2. **Paramètres avancés** → **Règles de trafic entrant**

3. **Nouvelle règle :**
   - Type : Port
   - Protocole : TCP
   - Port : 3306
   - Action : Autoriser la connexion
   - Profil : Tous
   - Nom : MySQL XAMPP

4. **Cliquez sur Terminer**

5. **Réessayez la connexion**

## 🔐 Sécurité

⚠️ **Important** : Cette configuration permet à n'importe quel appareil sur votre réseau local d'accéder à MySQL.

Pour une utilisation en production, vous devriez :
- Utiliser un mot de passe pour root
- Restreindre bind-address
- Configurer le pare-feu correctement

Pour les tests en local, c'est acceptable.

## 📝 Checklist complète

- [ ] `my.ini` modifié : `bind-address = 0.0.0.0`
- [ ] MySQL redémarré dans XAMPP
- [ ] IP Windows trouvée avec `ipconfig`
- [ ] `.env.test.local` mis à jour avec l'IP
- [ ] Connexion testée et fonctionnelle
- [ ] Pare-feu configuré (si nécessaire)

## 🚀 Une fois configuré

Lancez les tests :

```bash
# Initialiser la base de données de test
php bin/console doctrine:database:create --env=test --if-not-exists
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-interaction

# Lancer les tests
./bin/test-simple.sh
```

## ❓ Problèmes courants

### "Connection refused"

**Cause** : MySQL ne tourne pas ou n'écoute pas sur le bon port

**Solution** :
1. Vérifiez que MySQL est bien démarré dans XAMPP
2. Vérifiez que le port est 3306 (dans XAMPP Control Panel → Config → my.ini)

### "Access denied"

**Cause** : Mauvais utilisateur ou mot de passe

**Solution** :
- XAMPP par défaut utilise `root` sans mot de passe
- Vérifiez dans `.env.test.local` : `mysql://root:@...`
- Le `:` après `root` signifie "pas de mot de passe"

### "Unknown database"

**Cause** : La base `digitalfy_db_test` n'existe pas

**Solution** :
```bash
php bin/console doctrine:database:create --env=test
```

### L'IP change à chaque redémarrage (IP dynamique)

**Cause** : Votre routeur attribue des IP dynamiques

**Solutions** :
1. Configurez une IP statique pour votre PC dans les paramètres réseau Windows
2. Ou créez un script qui met à jour automatiquement `.env.test.local`

## 💡 Alternative : IP statique dans Windows

Pour éviter que l'IP change :

1. **Panneau de configuration** → **Centre Réseau et partage**
2. **Modifier les paramètres de la carte**
3. **Clic droit sur votre connexion** → **Propriétés**
4. **Protocole Internet version 4 (TCP/IPv4)** → **Propriétés**
5. **Utiliser l'adresse IP suivante :**
   - Adresse IP : `192.168.1.100` (ou autre selon votre réseau)
   - Masque : `255.255.255.0`
   - Passerelle : `192.168.1.1` (ou l'IP de votre routeur)
   - DNS : `8.8.8.8` et `8.8.4.4` (Google DNS)

---

**Besoin d'aide ?** Contactez-moi avec :
- La sortie de `ipconfig`
- Le contenu de votre `.env.test.local`
- Les erreurs exactes que vous rencontrez
