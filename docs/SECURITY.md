# Guide de Sécurité - Digitalfy

Ce document détaille toutes les mesures de sécurité mises en place dans l'application Digitalfy.

## 🔐 Authentification

### Système d'utilisateurs

- **Entité User** : Stockage sécurisé avec :
  - Hash des mots de passe (algorithme `auto` - bcrypt/argon2)
  - Email unique comme identifiant
  - Système de rôles (`ROLE_USER`, `ROLE_ADMIN`, `ROLE_SUPER_ADMIN`)
  - Suivi des connexions (dernière connexion)
  - Possibilité de désactiver un compte (`isActive`)

### Connexion

- **Route** : `/login`
- **Protection CSRF** : Activée sur le formulaire de connexion
- **Rate Limiting** : 5 tentatives maximum par 15 minutes
- **Remember Me** : Cookie sécurisé (1 semaine, httponly, samesite=lax)
- **Logging** : Toutes les tentatives de connexion sont enregistrées (succès et échecs)

### Mot de passe

- **Hash** : Utilisation de l'algorithme automatique de Symfony (bcrypt ou argon2 selon disponibilité)
- **Politique** : Minimum 8 caractères (recommandé : 12+, avec majuscules, minuscules, chiffres et symboles)
- **Rehashing automatique** : Via `PasswordUpgraderInterface` pour mettre à jour automatiquement les hash obsolètes

## 🛡️ Protection des routes

### Dashboard Admin

Toutes les routes `/admin/*` sont protégées :

```php
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
```

### Hiérarchie des rôles

```yaml
role_hierarchy:
    ROLE_ADMIN: ROLE_USER
    ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]
```

- **ROLE_USER** : Utilisateur de base (pour extensions futures)
- **ROLE_ADMIN** : Accès au dashboard administrateur
- **ROLE_SUPER_ADMIN** : Tous les droits + impersonation

### Access Control

```yaml
access_control:
    - { path: ^/login, roles: PUBLIC_ACCESS }
    - { path: ^/admin, roles: ROLE_ADMIN }
    - { path: ^/, roles: PUBLIC_ACCESS }
```

## 🔒 Sécurité Frontend

### Templates

1. **Auto-escaping Twig** : Activé par défaut, protection XSS automatique
2. **CSRF sur tous les formulaires** : Token CSRF obligatoire
3. **Validation côté serveur** : Toujours validé même si validation JS présente

### Headers de sécurité recommandés

À configurer dans votre serveur web (nginx/apache) :

```nginx
# Content Security Policy
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' https://www.googletagmanager.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'";

# Autres headers de sécurité
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;
```

## 🔍 Monitoring et Logging

### Événements de sécurité enregistrés

- ✅ Connexions réussies (username, IP, user agent, timestamp)
- ✅ Tentatives échouées (username, IP, user agent, raison, timestamp)
- ✅ Dernière connexion mise à jour sur chaque login

### Logs

Les logs de sécurité sont dans :
- **Dev** : `var/log/dev.log`
- **Prod** : `var/log/prod.log`

Chercher les entrées avec `channel: security` ou `channel: app`

## 🚨 Détection d'intrusion

### Rate Limiting

- **Login** : 5 tentatives / 15 minutes par IP
- Configurable dans `security.yaml` :

```yaml
login_throttling:
    max_attempts: 5
    interval: '15 minutes'
```

### Recommandations supplémentaires

Pour la production, considérez :

1. **Fail2ban** : Bannir automatiquement les IPs après X échecs
2. **WAF (Web Application Firewall)** : Cloudflare, AWS WAF, ou ModSecurity
3. **Monitoring** : Sentry, New Relic, ou Datadog pour alertes en temps réel

## 📝 Bonnes pratiques

### Côté serveur

1. ✅ Validation stricte de toutes les entrées utilisateur
2. ✅ Utilisation de requêtes préparées (Doctrine ORM)
3. ✅ Pas d'exposition d'informations sensibles dans les erreurs
4. ✅ Variables d'environnement pour secrets (`.env.local`)
5. ✅ HTTPS obligatoire en production
6. ✅ Cookies avec flags `secure`, `httponly`, `samesite`

### Côté client

1. ✅ Auto-escaping Twig (protection XSS)
2. ✅ CSRF tokens sur tous les formulaires
3. ✅ Validation HTML5 + validation serveur
4. ✅ Pas de données sensibles dans le code JavaScript
5. ✅ Content Security Policy (CSP)

## 🔧 Gestion des utilisateurs

### Créer un administrateur

```bash
php bin/console app:create-admin
```

Options disponibles :
```bash
php bin/console app:create-admin --email=admin@example.com --password=secret --fullname="Admin User"
```

### Désactiver un utilisateur

Via EasyAdmin ou directement en base :

```sql
UPDATE user SET is_active = 0 WHERE email = 'user@example.com';
```

### Réinitialiser un mot de passe

Pour l'instant, réinitialisation manuelle :

```bash
php bin/console app:create-admin # Créer un nouveau compte
# Ou modifier directement en base après avoir hashé le mot de passe
```

**TODO** : Implémenter un système de reset par email

## 🎯 Checklist de sécurité pour la production

Avant la mise en production :

- [ ] Changer `APP_SECRET` dans `.env.local`
- [ ] `APP_ENV=prod` dans `.env.local`
- [ ] HTTPS activé et forcé
- [ ] Headers de sécurité configurés (nginx/apache)
- [ ] Désactiver le Profiler Symfony
- [ ] Désactiver le debug mode
- [ ] Configurer les logs (rotation)
- [ ] Activer le rate limiting
- [ ] Sauvegardes régulières de la base
- [ ] Monitoring des logs de sécurité
- [ ] Tester la connexion et déconnexion
- [ ] Vérifier que `/admin` nécessite une authentification
- [ ] Supprimer tous les comptes de test
- [ ] Documenter les accès admin

## 🔐 Variables d'environnement sensibles

Ne JAMAIS commiter :
- `APP_SECRET`
- `DATABASE_URL` (credentials)
- `MAILER_DSN` (si SMTP avec credentials)
- Clés API tierces

Utiliser `.env.local` (gitignored) pour les valeurs réelles.

## 📞 En cas de problème de sécurité

1. **Bloquer l'accès** : Désactiver le compte compromis
2. **Analyser les logs** : Identifier l'origine de l'attaque
3. **Changer les secrets** : `APP_SECRET`, mots de passe
4. **Notifier** : Informer les utilisateurs si données compromises (RGPD)
5. **Corriger** : Patcher la faille identifiée
6. **Documenter** : Pour éviter la récidive

## 🔗 Ressources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Symfony Security Best Practices](https://symfony.com/doc/current/security.html)
- [Symfony Security Checklist](https://symfony.com/doc/current/deployment.html)
- [ANSSI - Guide de sécurité des applications web](https://www.ssi.gouv.fr/)

---

*Document mis à jour : 2025-11-19*
