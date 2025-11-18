# Phase 3 : Système de blog

**Durée** : 3 jours (Semaine 3)
**Objectif** : Mettre en place le système de blog avec les 3 premiers articles SEO

---

## 📋 Vue d'ensemble

Le blog est un élément clé de la stratégie SEO :
- Attirer du trafic organique
- Positionner sur des requêtes longue traîne
- Démontrer l'expertise
- Créer du contenu evergreen

---

## 3.1 Frontend Blog

### Contrôleur Blog

```php
// src/Controller/BlogController.php
namespace App\Controller;

use App\Repository\BlogPostRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog')]
    public function index(Request $request, BlogPostRepository $repo): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 12;

        $posts = $repo->findPublishedPaginated($page, $limit);
        $totalPosts = $repo->countPublished();

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => ceil($totalPosts / $limit),
        ]);
    }

    #[Route('/blog/categorie/{slug}', name: 'blog_category')]
    public function category(
        string $slug,
        Request $request,
        CategoryRepository $categoryRepo,
        BlogPostRepository $postRepo
    ): Response {
        $category = $categoryRepo->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException();
        }

        $page = $request->query->getInt('page', 1);
        $posts = $postRepo->findByCategory($category, $page);

        return $this->render('blog/category.html.twig', [
            'category' => $category,
            'posts' => $posts,
            'currentPage' => $page,
        ]);
    }

    #[Route('/blog/{slug}', name: 'blog_post')]
    public function show(string $slug, BlogPostRepository $repo): Response
    {
        $post = $repo->findOneBy(['slug' => $slug, 'status' => 'published']);

        if (!$post) {
            throw $this->createNotFoundException();
        }

        // Articles suggérés (même catégorie)
        $relatedPosts = $repo->findRelated($post, 3);

        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
```

### Repository custom queries

```php
// src/Repository/BlogPostRepository.php
public function findPublishedPaginated(int $page, int $limit): array
{
    return $this->createQueryBuilder('p')
        ->where('p.status = :status')
        ->setParameter('status', 'published')
        ->orderBy('p.publishedAt', 'DESC')
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}

public function countPublished(): int
{
    return $this->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->where('p.status = :status')
        ->setParameter('status', 'published')
        ->getQuery()
        ->getSingleScalarResult();
}

public function findRelated(BlogPost $post, int $limit): array
{
    return $this->createQueryBuilder('p')
        ->where('p.category = :category')
        ->andWhere('p.id != :id')
        ->andWhere('p.status = :status')
        ->setParameter('category', $post->getCategory())
        ->setParameter('id', $post->getId())
        ->setParameter('status', 'published')
        ->orderBy('p.publishedAt', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}
```

### Checklist Contrôleur

- [ ] Créer `BlogController`
- [ ] Méthodes personnalisées dans `BlogPostRepository`
- [ ] Pagination fonctionnelle
- [ ] Filtrage par catégorie

---

## 3.2 Templates Blog

### Liste articles - `templates/blog/index.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}Blog – Actualités développement web & mobile | Digitalfy{% endblock %}

{% block body %}
<div class="blog-page">
    <section class="blog-hero">
        <div class="container">
            <h1>Blog</h1>
            <p>Conseils, actualités et astuces sur le développement web et mobile</p>
        </div>
    </section>

    <section class="blog-content">
        <div class="container">
            <div class="blog-grid">
                {% for post in posts %}
                    {% include 'components/_blog-card.html.twig' with {post: post} %}
                {% endfor %}
            </div>

            {# Pagination #}
            {% if totalPages > 1 %}
                <nav class="pagination">
                    {% for page in 1..totalPages %}
                        <a href="{{ path('blog', {page: page}) }}"
                           class="{% if page == currentPage %}active{% endif %}">
                            {{ page }}
                        </a>
                    {% endfor %}
                </nav>
            {% endif %}
        </div>
    </section>
</div>
{% endblock %}
```

### Article détail - `templates/blog/show.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}{{ post.metaTitle ?? post.title }} | Blog Digitalfy{% endblock %}

{% block meta_description %}{{ post.metaDescription ?? post.excerpt }}{% endblock %}

{% block body %}
<article class="blog-post">
    <header class="post-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ path('home') }}">Accueil</a> /
                <a href="{{ path('blog') }}">Blog</a> /
                <a href="{{ path('blog_category', {slug: post.category.slug}) }}">
                    {{ post.category.name }}
                </a> /
                <span>{{ post.title }}</span>
            </nav>

            <h1>{{ post.title }}</h1>

            <div class="post-meta">
                <span class="post-date">{{ post.publishedAt|date('d/m/Y') }}</span>
                <span class="post-category">
                    <a href="{{ path('blog_category', {slug: post.category.slug}) }}">
                        {{ post.category.name }}
                    </a>
                </span>
            </div>

            {% if post.featuredImage %}
                <img src="{{ asset('uploads/blog/' ~ post.featuredImage) }}"
                     alt="{{ post.title }}"
                     class="post-featured-image">
            {% endif %}
        </div>
    </header>

    <div class="post-content">
        <div class="container">
            {{ post.content|raw }}
        </div>
    </div>

    <footer class="post-footer">
        <div class="container">
            <div class="share-buttons">
                <span>Partager :</span>
                <a href="https://twitter.com/intent/tweet?url={{ url('blog_post', {slug: post.slug}) }}"
                   target="_blank">Twitter</a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ url('blog_post', {slug: post.slug}) }}"
                   target="_blank">LinkedIn</a>
            </div>
        </div>
    </footer>

    {% if relatedPosts|length > 0 %}
        <section class="related-posts">
            <div class="container">
                <h2>Articles similaires</h2>
                <div class="blog-grid">
                    {% for relatedPost in relatedPosts %}
                        {% include 'components/_blog-card.html.twig' with {post: relatedPost} %}
                    {% endfor %}
                </div>
            </div>
        </section>
    {% endif %}
</article>

{# Schema.org Article #}
{% block head_scripts %}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": "{{ post.title }}",
    "image": "{{ asset('uploads/blog/' ~ post.featuredImage) }}",
    "datePublished": "{{ post.publishedAt|date('c') }}",
    "dateModified": "{{ post.updatedAt|date('c') }}",
    "author": {
        "@type": "Person",
        "name": "Marc Dubois"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Digitalfy",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/logo.png') }}"
        }
    },
    "description": "{{ post.excerpt }}"
}
</script>
{% endblock %}
{% endblock %}
```

### Checklist Templates

- [ ] Template liste blog
- [ ] Template article détail
- [ ] Template catégorie
- [ ] Composant card article
- [ ] Breadcrumb
- [ ] Pagination
- [ ] Boutons partage
- [ ] Articles similaires

---

## 3.3 Les 3 premiers articles

### Article 1 : Click & Collect à Nîmes

**Slug** : `click-and-collect-nimes`
**Catégorie** : Solutions Digitales
**Contenu** : Voir [docs/contenus-seo-digitalfy.md](../../contenus-seo-digitalfy.md#article-click-and-collect)

Structure :
- Qu'est-ce que le Click & Collect ?
- Pourquoi c'est intéressant pour Nîmes ?
- Bénéfices concrets
- Comment mettre en place ?
- CTA vers contact

### Article 2 : Application mobile pour restaurant

**Slug** : `application-mobile-pour-restaurant`
**Catégorie** : Applications Mobiles
**Contenu** : Voir [docs/contenus-seo-digitalfy.md](../../contenus-seo-digitalfy.md#article-app-restaurant)

Structure :
- Pour quel type de restaurant ?
- Avantages pour les clients
- Avantages pour l'organisation
- Exemples de fonctionnalités
- Comment démarrer ?

### Article 3 : Site internet pour artisan

**Slug** : `site-internet-pour-artisan`
**Catégorie** : Développement Web
**Contenu** : Voir [docs/contenus-seo-digitalfy.md](../../contenus-seo-digitalfy.md#article-site-artisan)

Structure :
- Vos clients vous cherchent en ligne
- Un site rassure et filtre
- Contenu d'un site simple
- Erreurs à éviter
- Par où commencer ?

### Checklist Articles

- [ ] Créer fixtures pour les 3 articles
- [ ] Rédiger le contenu complet (Markdown ou HTML)
- [ ] Optimiser meta title/description
- [ ] Ajouter images à la une
- [ ] Vérifier structure Hn
- [ ] Ajouter CTAs en fin d'article

---

## ✅ Checklist finale Phase 3

### Backend
- [ ] Contrôleur Blog créé
- [ ] Méthodes repository custom
- [ ] Pagination fonctionnelle

### Frontend
- [ ] Templates blog créés
- [ ] Composants réutilisables
- [ ] Breadcrumb
- [ ] Articles similaires
- [ ] Boutons partage

### Contenu
- [ ] 3 articles créés avec fixtures
- [ ] Contenu SEO optimisé
- [ ] Images optimisées
- [ ] Schema.org Article sur chaque page

### Tests
- [ ] Liste articles accessible
- [ ] Détail article accessible
- [ ] Pagination fonctionne
- [ ] Filtrage par catégorie fonctionne
- [ ] Responsive OK

---

## 🚀 Prochaine étape

Une fois cette phase terminée, passer à la [Phase 4 : Portfolio/Projets](04-portfolio.md)

---

*Document généré le 2025-11-18*
