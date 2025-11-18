# Phase 4 : Portfolio/Projets

**Durée** : 2 jours (Semaine 3)
**Objectif** : Créer les études de cas projets pour démontrer l'expertise

---

## 📋 Vue d'ensemble

Le portfolio permet de :
- Démontrer les compétences techniques
- Rassurer les prospects
- Présenter différents types de projets
- Optimiser le SEO sur des requêtes spécifiques

---

## 4.1 Page liste projets

**URL** : `/projets`

### Contrôleur

```php
// src/Controller/ProjectController.php
#[Route('/projets', name: 'projects')]
public function index(ProjectRepository $repo): Response
{
    $projects = $repo->findBy(['published' => true], ['id' => 'DESC']);
    
    return $this->render('project/index.html.twig', [
        'projects' => $projects,
    ]);
}

#[Route('/projets/{slug}', name: 'project_show')]
public function show(string $slug, ProjectRepository $repo): Response
{
    $project = $repo->findOneBy(['slug' => $slug, 'published' => true]);
    
    if (!$project) {
        throw $this->createNotFoundException();
    }
    
    return $this->render('project/show.html.twig', [
        'project' => $project,
    ]);
}
```

### Checklist

- [ ] Créer `ProjectController`
- [ ] Template liste projets avec grille
- [ ] Filtrage par technologie (optionnel)

---

## 4.2 Les 3 études de cas

### Projet 1 : Application budget React Native

**Slug** : `app-budget-react-native`
**Technologies** : React Native, Expo, TypeScript
**Contenu** : Voir [docs/contenus-seo-digitalfy.md](../../contenus-seo-digitalfy.md)

Structure :
- Contexte & objectifs
- Solution mise en place
- Fonctionnalités principales
- Stack technique
- Résultats & perspectives

### Projet 2 : Site vigneron Symfony

**Slug** : `site-vigneron-symfony`
**Technologies** : Symfony, Twig, Doctrine
**Contenu** : Voir documentation

Structure :
- Contexte & besoins
- Objectifs du site
- Structure des pages
- Stack technique
- Bénéfices

### Projet 3 : Site groupe rock React

**Slug** : `site-groupe-rock-react`
**Technologies** : React, Tailwind CSS
**Contenu** : Voir documentation

Structure :
- Contexte & enjeux
- Fonctionnalités
- Stack technique
- Résultats

### Checklist Projets

- [ ] Créer fixtures pour les 3 projets
- [ ] Préparer screenshots/mockups
- [ ] Rédiger études de cas complètes
- [ ] Optimiser SEO (titles, meta)
- [ ] Ajouter CTAs vers contact

---

## 4.3 Template étude de cas

```twig
{# templates/project/show.html.twig #}
{% extends 'base.html.twig' %}

{% block title %}{{ project.title }} – Étude de cas | Digitalfy{% endblock %}

{% block body %}
<article class="project-page">
    <section class="project-hero">
        <div class="container">
            <h1>{{ project.title }}</h1>
            <p class="project-excerpt">{{ project.description }}</p>
            
            <div class="project-tags">
                {% for tech in project.technologies %}
                    <span class="tag">{{ tech }}</span>
                {% endfor %}
            </div>
        </div>
    </section>
    
    {% if project.thumbnail %}
        <section class="project-thumbnail">
            <img src="{{ asset('uploads/projects/' ~ project.thumbnail) }}" 
                 alt="{{ project.title }}">
        </section>
    {% endif %}
    
    <section class="project-section">
        <div class="container">
            <h2>Contexte & objectifs</h2>
            {{ project.context|raw }}
        </div>
    </section>
    
    <section class="project-section bg-light">
        <div class="container">
            <h2>Solution mise en place</h2>
            {{ project.solution|raw }}
        </div>
    </section>
    
    <section class="project-section">
        <div class="container">
            <h2>Résultats</h2>
            {{ project.results|raw }}
        </div>
    </section>
    
    <section class="project-cta">
        <div class="container">
            <h2>Un projet similaire ?</h2>
            <a href="{{ path('contact') }}" class="btn btn-primary">
                Parlons-en ensemble
            </a>
        </div>
    </section>
</article>
{% endblock %}
```

---

## ✅ Checklist finale Phase 4

### Backend
- [ ] Contrôleur créé
- [ ] 3 projets en fixtures

### Frontend
- [ ] Page liste projets
- [ ] Template étude de cas
- [ ] Composant card projet

### Contenu
- [ ] 3 études de cas rédigées
- [ ] Screenshots/mockups optimisés
- [ ] Meta tags optimisés

### Tests
- [ ] Liste projets accessible
- [ ] Détail projet accessible
- [ ] Responsive OK

---

## 🚀 Prochaine étape

Passer à la [Phase 5 : Backoffice EasyAdmin](05-backoffice-easyadmin.md)

---

*Document généré le 2025-11-18*
