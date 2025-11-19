# Guide d'Authentification - Digitalfy

Ce guide explique comment utiliser le système d'authentification de l'application Digitalfy.

## 🚀 Démarrage rapide

### 1. Créer la base de données et les tables

```bash
# Créer la base de données (si ce n'est pas déjà fait)
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

### 2. Créer votre premier administrateur

```bash
php bin/console app:create-admin
```

La commande vous demandera :
- **Email** : Votre adresse email (servira d'identifiant)
- **Nom complet** : Votre nom complet
- **Mot de passe** : Minimum 8 caractères (12+ recommandé)

Exemple d'interaction :
```
Email de l'administrateur: admin@digitalfy.fr
Nom complet: Marc Dubois
Mot de passe (min. 8 caractères): ************
Confirmer le mot de passe: ************

✓ Administrateur créé avec succès !
  Email: admin@digitalfy.fr
  Nom: Marc Dubois
  Rôle: ROLE_ADMIN

! Vous pouvez maintenant vous connecter à /login avec ces identifiants.
```

### 3. Se connecter au dashboard

1. Accédez à `/login` dans votre navigateur
2. Entrez vos identifiants (email + mot de passe)
3. Cochez "Se souvenir de moi" si vous voulez rester connecté (optionnel)
4. Cliquez sur "Se connecter"

Vous serez redirigé vers `/admin` (le dashboard EasyAdmin).

## 📱 Routes disponibles

| Route | Description | Accès |
|-------|-------------|-------|
| `/login` | Page de connexion | Public |
| `/logout` | Déconnexion | Authentifié |
| `/admin` | Dashboard administrateur | ROLE_ADMIN |
| `/admin/*` | Toutes les pages admin | ROLE_ADMIN |

## 👥 Gestion des utilisateurs

### Créer un administrateur (en ligne de commande)

**Méthode interactive** :
```bash
php bin/console app:create-admin
```

**Méthode avec options** :
```bash
php bin/console app:create-admin \
  --email=admin@example.com \
  --password=MonMotDePasseSecurise123! \
  --fullname="Marc Dubois"
```

### Désactiver un utilisateur

Via SQL (si base de données accessible) :
```sql
UPDATE user SET is_active = 0 WHERE email = 'user@example.com';
```

Ou via EasyAdmin (à implémenter si besoin) :
- Se connecter au dashboard
- Section "Utilisateurs" (si ajoutée au menu)
- Décocher "Actif" pour l'utilisateur

### Changer le rôle d'un utilisateur

Via SQL :
```sql
-- Passer en admin
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'user@example.com';

-- Passer en super admin
UPDATE user SET roles = '["ROLE_SUPER_ADMIN"]' WHERE email = 'user@example.com';

-- Retirer les rôles (utilisateur simple)
UPDATE user SET roles = '[]' WHERE email = 'user@example.com';
```

## 🔑 Système de rôles

### Hiérarchie des rôles

```
ROLE_SUPER_ADMIN
    ↓ hérite de
ROLE_ADMIN
    ↓ hérite de
ROLE_USER
```

### Description des rôles

| Rôle | Description | Permissions |
|------|-------------|-------------|
| `ROLE_USER` | Utilisateur de base | Accès aux fonctionnalités futures (profil, etc.) |
| `ROLE_ADMIN` | Administrateur | Accès complet au dashboard `/admin` |
| `ROLE_SUPER_ADMIN` | Super administrateur | Tous les droits + impersonation |

### Impersonation (ROLE_SUPER_ADMIN)

Les super admins peuvent "se faire passer" pour un autre utilisateur :

```
# Ajouter ?_switch_user=email@example.com à n'importe quelle URL
/admin?_switch_user=user@example.com

# Pour revenir à votre compte
/admin?_switch_user=_exit
```

Utile pour :
- Déboguer un problème spécifique à un utilisateur
- Tester les permissions
- Support client

## 🔒 Sécurité

### Fonctionnalités de sécurité activées

✅ **Hash des mots de passe** : Bcrypt/Argon2 automatique
✅ **Protection CSRF** : Sur tous les formulaires
✅ **Rate Limiting** : 5 tentatives de connexion / 15 minutes
✅ **Remember Me** : Cookie sécurisé (httponly, secure, samesite)
✅ **Logging** : Toutes les connexions sont enregistrées
✅ **Protection XSS** : Auto-escaping Twig
✅ **Protection SQL Injection** : Requêtes préparées Doctrine

### Politique de mot de passe

**Exigences actuelles** :
- Minimum 8 caractères

**Recommandations** :
- Au moins 12 caractères
- Mélange de majuscules et minuscules
- Au moins un chiffre
- Au moins un symbole (@, #, $, %, etc.)
- Pas de mots du dictionnaire
- Pas d'informations personnelles (date de naissance, etc.)

**Exemples de bons mots de passe** :
- `MyS3cur3P@ssw0rd!2024`
- `Digitalfy#Admin@2025`
- `Tr0ubl3$ome&P@ssw0rd`

### Connexion sécurisée

1. **HTTPS obligatoire en production**
   - Certificat SSL/TLS
   - Redirection automatique HTTP → HTTPS

2. **Rate Limiting**
   - 5 tentatives max par IP en 15 minutes
   - Délai exponentiel après échecs

3. **Détection d'intrusion**
   - Toutes les tentatives sont loggées
   - IP, User-Agent, timestamp enregistrés
   - Alertes possibles via monitoring (Sentry, etc.)

## 🔐 Remember Me (Se souvenir de moi)

### Configuration

- **Durée** : 7 jours (604800 secondes)
- **Cookie** : `REMEMBERME`
- **Flags** : `secure`, `httponly`, `samesite=lax`

### Fonctionnement

1. L'utilisateur coche "Se souvenir de moi" lors de la connexion
2. Un cookie sécurisé est créé
3. À la prochaine visite, l'utilisateur est automatiquement connecté
4. Le cookie expire après 7 jours d'inactivité

### Désactiver Remember Me

Pour désactiver cette fonctionnalité, commenter dans `security.yaml` :

```yaml
# remember_me:
#     secret: '%kernel.secret%'
#     ...
```

## 📊 Monitoring

### Logs de sécurité

Les événements de sécurité sont dans `var/log/` :

**Connexions réussies** :
```
[2025-11-19 10:30:45] app.INFO: Connexion réussie {"username":"admin@digitalfy.fr","ip":"192.168.1.1","user_agent":"Mozilla/5.0..."}
```

**Échecs de connexion** :
```
[2025-11-19 10:31:12] app.WARNING: Tentative de connexion échouée {"username":"hacker@evil.com","ip":"1.2.3.4","reason":"Invalid credentials"}
```

### Analyser les logs

```bash
# Voir toutes les tentatives de connexion
tail -f var/log/dev.log | grep "Connexion"

# Compter les échecs par IP
grep "Tentative de connexion échouée" var/log/prod.log | grep -oP 'ip":"[^"]+' | sort | uniq -c | sort -rn

# Voir les dernières connexions réussies
grep "Connexion réussie" var/log/prod.log | tail -20
```

## 🚨 Troubleshooting

### Problème : "Invalid CSRF token"

**Cause** : Le token CSRF a expiré ou est invalide
**Solution** :
1. Vider le cache : `php bin/console cache:clear`
2. Rafraîchir la page de login (F5)
3. Vérifier que les cookies sont activés

### Problème : "Too many failed login attempts"

**Cause** : Rate limiting activé après 5 échecs
**Solution** :
1. Attendre 15 minutes
2. Ou vider le cache : `php bin/console cache:pool:clear cache.security.rate_limiter`

### Problème : Redirection infinie après login

**Cause** : L'utilisateur n'a pas le rôle nécessaire
**Solution** :
1. Vérifier les rôles en base de données
2. S'assurer que l'utilisateur a au moins `ROLE_ADMIN`
3. Vider le cache de sécurité

### Problème : "Access Denied"

**Cause** : L'utilisateur n'a pas le rôle ROLE_ADMIN
**Solution** :
```bash
# Donner le rôle admin à un utilisateur
mysql -u root -p digitalfy_db
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'user@example.com';
```

## 🔄 Procédure de réinitialisation de mot de passe

**Note** : La fonctionnalité de reset par email n'est pas encore implémentée.

Pour l'instant, réinitialisation manuelle :

1. **Via la commande** (créer un nouveau compte temporaire) :
```bash
php bin/console app:create-admin --email=temp@digitalfy.fr --password=TempPass123!
```

2. **Via SQL** (si vous avez accès à la base) :
```bash
# Générer un hash
php -r "echo password_hash('NouveauMotDePasse', PASSWORD_BCRYPT);"

# Mettre à jour
mysql -u root -p digitalfy_db
UPDATE user SET password = '$2y$10$...' WHERE email = 'user@example.com';
```

## 📋 Checklist de mise en production

Avant de déployer en production :

- [ ] Changer `APP_SECRET` dans `.env.local`
- [ ] `APP_ENV=prod` dans `.env.local`
- [ ] Supprimer tous les comptes de test
- [ ] Créer le compte admin principal
- [ ] Utiliser un mot de passe fort (12+ caractères)
- [ ] Activer HTTPS et forcer la redirection
- [ ] Tester la connexion et la déconnexion
- [ ] Vérifier que `/admin` requiert l'authentification
- [ ] Configurer le monitoring des logs
- [ ] Documenter les accès admin (qui a accès)
- [ ] Sauvegarder la base de données

## 🆘 Support

En cas de problème :

1. Consulter les logs : `var/log/dev.log` ou `var/log/prod.log`
2. Vider le cache : `php bin/console cache:clear`
3. Vérifier la configuration : `config/packages/security.yaml`
4. Consulter la documentation Symfony : https://symfony.com/doc/current/security.html

---

*Document mis à jour : 2025-11-19*
