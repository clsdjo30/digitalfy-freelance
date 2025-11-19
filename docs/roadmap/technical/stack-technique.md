# Stack Technique - Digitalfy

Technologies et architecture du projet

---

## 🏗️ Backend

### Framework

- **Symfony 7.0** (ou 6.4 LTS)
- **PHP 8.2+**
- Architecture MVC
- Routing avec annotations/attributes
- Twig pour les templates (SSR)

### Base de données

- **PostgreSQL 15+** (recommandé) ou **MySQL 8+**
- **Doctrine ORM** pour l'abstraction
- Migrations versionnées
- Fixtures avec Foundry

### Bundles essentiels

```json
{
    "easycorp/easyadmin-bundle": "^4.0",
    "symfony/webpack-encore-bundle": "^2.0",
    "symfony/mailer": "^7.0",
    "symfony/form": "^7.0",
    "symfony/validator": "^7.0",
    "vich/uploader-bundle": "^2.0",
    "cocur/slugify": "^4.0",
    "presta/sitemap-bundle": "^3.0"
}
```

---

## 🎨 Frontend

### Build Tools

- **Webpack Encore** pour la compilation des assets
- **SCSS** pour le CSS
- **Vanilla JavaScript** (pas de framework lourd)

### Fonts

- **Inter** - Police principale
- **Poppins** - Police titres
- Google Fonts CDN

### Icônes

- SVG inline pour les icônes
- FontAwesome (optionnel)

---

## 📦 Structure du projet

```
digitalfy-vitrine/
├── assets/
│   ├── app.js
│   ├── styles/
│   │   ├── app.scss
│   │   ├── _variables.scss
│   │   ├── _mixins.scss
│   │   ├── base/
│   │   ├── components/
│   │   ├── sections/
│   │   └── pages/
│   └── images/
├── config/
│   ├── packages/
│   ├── routes.yaml
│   └── services.yaml
├── migrations/
├── public/
│   ├── build/
│   ├── uploads/
│   ├── index.php
│   └── robots.txt
├── src/
│   ├── Controller/
│   │   ├── Admin/
│   │   ├── HomeController.php
│   │   ├── ServiceController.php
│   │   ├── BlogController.php
│   │   └── ContactController.php
│   ├── Entity/
│   │   ├── BlogPost.php
│   │   ├── Category.php
│   │   ├── ContactRequest.php
│   │   └── Project.php
│   ├── Form/
│   │   └── ContactType.php
│   ├── Repository/
│   └── EventListener/
│       └── SitemapListener.php
├── templates/
│   ├── base.html.twig
│   ├── home/
│   ├── service/
│   ├── blog/
│   ├── project/
│   ├── contact/
│   ├── page/
│   ├── partials/
│   │   ├── _navigation.html.twig
│   │   └── _footer.html.twig
│   └── components/
│       ├── _button.html.twig
│       ├── _card.html.twig
│       ├── _stats.html.twig
│       └── _faq.html.twig
├── var/
├── vendor/
├── .env
├── .env.local
├── composer.json
├── package.json
└── webpack.config.js
```

---

## 🚀 Performance

### Backend

- **OPcache** activé en production
- **Doctrine Query Cache**
- **HTTP Cache headers**
- **Symfony Cache** (APCu ou Redis)

### Frontend

- **Webpack** : minification JS/CSS
- **Images** : WebP avec fallback
- **Lazy loading** sur images non critiques
- **Critical CSS** inline (optionnel)
- **Gzip/Brotli** compression

---

## 🔒 Sécurité

- **HTTPS** forcé
- **CSRF protection** sur formulaires
- **XSS protection** (Twig auto-escape)
- **SQL Injection** protégé (Doctrine)
- **Headers sécurité** : X-Frame-Options, CSP
- **Rate limiting** sur formulaires (optionnel)

---

## 📊 SEO

- **SSR** avec Twig (pas de SPA React)
- **Meta tags** dynamiques
- **Schema.org** JSON-LD
- **Sitemap.xml** automatique (PrestaSitemapBundle)
- **Robots.txt**
- **Canonical URLs**
- **Open Graph** + Twitter Cards

---

## 📈 Analytics

- **Google Analytics GA4**
- **Google Search Console**
- **Facebook Pixel** (optionnel)
- **Microsoft Clarity** ou Hotjar (optionnel)

---

## 🛠️ Développement

### Environnement local

```bash
# Prérequis
- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL 15+ ou MySQL 8+

# Installation
git clone <repository>
composer install
npm install
cp .env .env.local
# Éditer .env.local avec config locale

# Base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

# Compiler assets
npm run dev

# Lancer serveur
symfony serve
```

### Scripts npm

```json
{
    "scripts": {
        "dev": "encore dev",
        "watch": "encore dev --watch",
        "build": "encore production"
    }
}
```

---

## 🌐 Déploiement

### Production

- **Serveur** : VPS Linux (Ubuntu 22.04)
- **Web server** : Nginx
- **Process manager** : PHP-FPM
- **SSL** : Let's Encrypt
- **Monitoring** : UptimeRobot
- **Backups** : Quotidiens automatiques

### CI/CD (optionnel)

- GitHub Actions
- GitLab CI
- Deployer PHP

---

## 📚 Documentation

- [Symfony Docs](https://symfony.com/doc/current/index.html)
- [EasyAdmin Docs](https://symfony.com/bundles/EasyAdminBundle/current/index.html)
- [Webpack Encore Docs](https://symfony.com/doc/current/frontend.html)
- [Twig Docs](https://twig.symfony.com/doc/)

---

*Document généré le 2025-11-18*
