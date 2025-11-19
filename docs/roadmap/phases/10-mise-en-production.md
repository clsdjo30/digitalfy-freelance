# Phase 10 : Mise en production

**Durée** : 2 jours (Semaine 6)
**Objectif** : Déployer le site en production et l'optimiser

---

## 10.1 Hébergement

### Choix hébergeur

Options recommandées :
- **VPS** : DigitalOcean, OVH, Scaleway
- **Cloud** : AWS, Google Cloud
- **Hébergement managé** : Platform.sh, Symfony Cloud

### Configuration serveur

- [ ] Serveur Linux (Ubuntu 22.04 recommandé)
- [ ] PHP 8.2+
- [ ] PostgreSQL 15+ ou MySQL 8+
- [ ] Nginx ou Apache
- [ ] Composer installé
- [ ] Node.js pour assets

---

## 10.2 Déploiement

### Préparation

```bash
# Sur le serveur
git clone <repository>
cd digitalfy-vitrine

# Installer dépendances
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Configuration
cp .env .env.local
# Éditer .env.local avec valeurs production

# Base de données
php bin/console doctrine:migrations:migrate --no-interaction

# Clear cache
php bin/console cache:clear
php bin/console cache:warmup
```

### Configuration production

```.env
# .env.local
APP_ENV=prod
APP_DEBUG=0

DATABASE_URL="postgresql://user:password@localhost:5432/digitalfy_prod"

MAILER_DSN=smtp://mailserver:port

APP_SECRET=<generate-strong-secret>
```

### Nginx configuration

```nginx
server {
    listen 80;
    server_name digitalfy.fr www.digitalfy.fr;
    root /var/www/digitalfy-vitrine/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }
}
```

### Checklist Déploiement

- [ ] Code déployé
- [ ] Dépendances installées
- [ ] Assets compilés
- [ ] Base de données migrée
- [ ] Configuration production
- [ ] Nginx/Apache configuré

---

## 10.3 DNS & SSL

### Configuration DNS

```
Type    Nom    Valeur
A       @      [IP_SERVEUR]
A       www    [IP_SERVEUR]
```

### SSL Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d digitalfy.fr -d www.digitalfy.fr
```

### Checklist DNS/SSL

- [ ] DNS configuré
- [ ] Propagation vérifiée
- [ ] Certificat SSL installé
- [ ] HTTPS forcé
- [ ] Redirection www → non-www (ou inverse)

---

## 10.4 Optimisations production

### PHP OPcache

```ini
; /etc/php/8.2/fpm/conf.d/10-opcache.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### Cache HTTP

```nginx
# Nginx cache headers
location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

### Compression

```nginx
gzip on;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml+rss text/javascript;
```

### Checklist Optimisations

- [ ] OPcache activé
- [ ] Cache HTTP configuré
- [ ] Compression Gzip
- [ ] CDN configuré (optionnel)

---

## 10.5 Post-lancement

### Google Search Console

- [ ] Soumettre sitemap.xml
- [ ] Vérifier indexation
- [ ] Configurer alertes erreurs

### Monitoring

- [ ] UptimeRobot ou Pingdom configuré
- [ ] Alertes email en cas de downtime
- [ ] Logs erreurs configurés

### Backup

```bash
# Script backup quotidien
0 2 * * * pg_dump digitalfy_prod | gzip > /backups/db-$(date +\%Y\%m\%d).sql.gz
```

### Documentation

- [ ] Guide utilisation EasyAdmin
- [ ] Procédures de maintenance
- [ ] Contacts support

### Checklist Post-lancement

- [ ] Sitemap soumis à GSC
- [ ] Monitoring actif
- [ ] Backups automatiques
- [ ] Documentation livrée

---

## ✅ Checklist finale Phase 10

### Hébergement
- [ ] Serveur configuré
- [ ] Dépendances installées
- [ ] Site accessible

### Déploiement
- [ ] Code en production
- [ ] Base de données migrée
- [ ] Assets compilés

### DNS & Sécurité
- [ ] DNS configuré
- [ ] SSL actif
- [ ] HTTPS forcé

### Optimisations
- [ ] OPcache actif
- [ ] Cache HTTP configuré
- [ ] Compression activée

### Post-lancement
- [ ] GSC configuré
- [ ] Monitoring actif
- [ ] Backups en place

---

## 🚀 Prochaine étape

Passer à la [Phase 11 : SEO Local](11-seo-local.md)

---

*Document généré le 2025-11-18*
