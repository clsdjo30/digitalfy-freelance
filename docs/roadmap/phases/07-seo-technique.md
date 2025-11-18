# Phase 7 : SEO Technique

**Durée** : 3 jours (Semaine 5)
**Objectif** : Optimiser le référencement naturel du site

---

## 📋 Vue d'ensemble

Le SEO technique garantit que :
- Google peut crawler et indexer toutes les pages
- Les pages sont optimisées pour le référencement
- Les données structurées enrichissent les résultats
- Les performances sont optimales

---

## 7.1 SEO On-Page

### Meta Tags sur toutes les pages

```twig
{# templates/base.html.twig #}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}Digitalfy{% endblock %}</title>
    <meta name="description" content="{% block meta_description %}{% endblock %}">
    <link rel="canonical" href="{{ app.request.uri }}">
    
    {# Open Graph #}
    <meta property="og:title" content="{% block og_title %}{% block title %}{% endblock %}{% endblock %}">
    <meta property="og:description" content="{% block og_description %}{% block meta_description %}{% endblock %}{% endblock %}">
    <meta property="og:image" content="{% block og_image %}{{ asset('images/og-default.jpg') }}{% endblock %}">
    <meta property="og:url" content="{{ app.request.uri }}">
    <meta property="og:type" content="website">
    
    {# Twitter Cards #}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{% block twitter_title %}{% block title %}{% endblock %}{% endblock %}">
    <meta name="twitter:description" content="{% block twitter_description %}{% block meta_description %}{% endblock %}{% endblock %}">
    <meta name="twitter:image" content="{% block twitter_image %}{{ asset('images/og-default.jpg') }}{% endblock %}">
</head>
```

### Checklist On-Page

- [ ] Meta title/description sur toutes les pages
- [ ] Canonical URLs
- [ ] Open Graph tags
- [ ] Twitter Cards
- [ ] Alt text sur toutes les images
- [ ] Structure Hn cohérente (1 seul H1)
- [ ] URLs SEO-friendly

---

## 7.2 Données structurées Schema.org

### LocalBusiness (Page d'accueil)

```json
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "Digitalfy",
    "description": "Développeur web et mobile freelance à Nîmes",
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "Nîmes",
        "addressRegion": "Occitanie",
        "addressCountry": "FR"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 43.8367,
        "longitude": 4.3601
    },
    "url": "https://www.digitalfy.fr",
    "telephone": "+33XXXXXXXXX"
}
```

### Service (Pages services)

```json
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "Développement d'application mobile à Nîmes",
    "description": "Création d'applications mobiles iOS & Android",
    "provider": {
        "@type": "LocalBusiness",
        "name": "Digitalfy"
    },
    "areaServed": {
        "@type": "City",
        "name": "Nîmes"
    }
}
```

### Article (Blog)

```json
{
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": "Titre de l'article",
    "image": "https://www.digitalfy.fr/uploads/blog/image.jpg",
    "datePublished": "2025-01-15",
    "author": {
        "@type": "Person",
        "name": "Marc Dubois"
    }
}
```

### Checklist Schema.org

- [ ] LocalBusiness sur accueil
- [ ] Service sur pages services
- [ ] Article sur blog posts
- [ ] BreadcrumbList
- [ ] FAQPage sur pages avec FAQ
- [ ] Tester avec Rich Results Test

---

## 7.3 Sitemap.xml automatique

### Bundle sitemap

```bash
composer require presta/sitemap-bundle
```

### Configuration

```php
// src/EventListener/SitemapListener.php
namespace App\EventListener;

use App\Repository\BlogPostRepository;
use App\Repository\ProjectRepository;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapListener implements EventSubscriberInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private BlogPostRepository $blogPostRepo,
        private ProjectRepository $projectRepo,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        // Pages statiques
        $event->getUrlContainer()->addUrl(
            new UrlConcrete(
                $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL),
                priority: 1.0,
                changefreq: 'weekly'
            ),
            'default'
        );

        // Articles de blog
        $posts = $this->blogPostRepo->findBy(['status' => 'published']);
        foreach ($posts as $post) {
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate('blog_post', ['slug' => $post->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    lastmod: $post->getUpdatedAt(),
                    priority: 0.7
                ),
                'default'
            );
        }

        // Projets
        $projects = $this->projectRepo->findBy(['published' => true]);
        foreach ($projects as $project) {
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate('project_show', ['slug' => $project->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    priority: 0.8
                ),
                'default'
            );
        }
    }
}
```

### Checklist Sitemap

- [ ] Installer PrestaSitemapBundle
- [ ] Configurer SitemapListener
- [ ] Inclure toutes les pages importantes
- [ ] Accessible sur `/sitemap.xml`
- [ ] Tester la génération

---

## 7.4 Robots.txt

```
# public/robots.txt
User-agent: *
Allow: /
Disallow: /admin

Sitemap: https://www.digitalfy.fr/sitemap.xml
```

### Checklist Robots.txt

- [ ] Créer `public/robots.txt`
- [ ] Bloquer `/admin`
- [ ] Référencer sitemap.xml
- [ ] Tester accessibilité

---

## 7.5 Performance Web Vitals

### Optimisation LCP (Largest Contentful Paint)

- [ ] Preload fonts
- [ ] Preload images critiques
- [ ] Optimiser taille images hero
- [ ] Lazy loading images non critiques

### Optimisation CLS (Cumulative Layout Shift)

- [ ] Définir width/height images
- [ ] Réserver espace pour contenus dynamiques
- [ ] Éviter injection contenu au-dessus du fold

### Optimisation FID (First Input Delay)

- [ ] Minifier JavaScript
- [ ] Différer JS non critique
- [ ] Optimiser code JavaScript

### Checklist Performance

- [ ] Score Lighthouse > 90
- [ ] LCP < 2.5s
- [ ] FID < 100ms
- [ ] CLS < 0.1
- [ ] Images WebP avec fallback
- [ ] Compression Gzip/Brotli
- [ ] Cache HTTP headers

---

## ✅ Checklist finale Phase 7

### On-Page SEO
- [ ] Meta tags complets partout
- [ ] Open Graph configuré
- [ ] Alt text sur images
- [ ] Structure Hn valide

### Données structurées
- [ ] Schema.org sur toutes les pages clés
- [ ] Validation Rich Results Test

### Fichiers techniques
- [ ] Sitemap.xml généré automatiquement
- [ ] Robots.txt configuré
- [ ] Favicon complet

### Performance
- [ ] Score Lighthouse > 90
- [ ] Web Vitals optimisés
- [ ] Images optimisées

---

## 🚀 Prochaine étape

Passer à la [Phase 8 : Tracking & Analytics](08-tracking-analytics.md)

---

*Document généré le 2025-11-18*
