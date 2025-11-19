# Phase 1 : Setup & Architecture

**Durée** : 5 jours (Semaine 1)
**Objectif** : Mettre en place l'environnement Symfony et la structure de base du projet

---

## 📋 Vue d'ensemble

Cette phase pose les fondations techniques du projet :
- Installation et configuration de Symfony
- Setup des outils de développement
- Création de la structure de base de données
- Configuration des bundles essentiels

---

## 1.1 Configuration projet Symfony

### Installation Symfony

```bash
# Créer un nouveau projet Symfony
composer create-project symfony/skeleton:"7.0.*" digitalfy-vitrine
cd digitalfy-vitrine

# Installer webapp pack (inclut Twig, assets, etc.)
composer require webapp

# Installer les dépendances essentielles
composer require symfony/orm-pack
composer require symfony/maker-bundle --dev
composer require symfony/debug-bundle --dev
```

### Checklist Configuration

- [ ] Installer Symfony 7.0 (ou 6.4 LTS)
- [ ] Vérifier PHP 8.2+ installé
- [ ] Configurer `.env` et `.env.local`
- [ ] Configurer la base de données (PostgreSQL ou MySQL)
- [ ] Tester que Symfony fonctionne (`symfony serve`)

---

## 1.2 Installation des bundles essentiels

### Bundles à installer

```bash
# EasyAdmin pour le backoffice
composer require easycorp/easyadmin-bundle

# Webpack Encore pour les assets
composer require symfony/webpack-encore-bundle
npm install

# Système de mail
composer require symfony/mailer

# Validation & formulaires
composer require symfony/validator
composer require symfony/form

# Gestion des fichiers uploadés
composer require vich/uploader-bundle

# Slugify pour les URLs
composer require cocur/slugify

# Fixtures pour les données de test
composer require --dev doctrine/doctrine-fixtures-bundle
composer require --dev zenstruck/foundry
```

### Checklist Bundles

- [ ] EasyAdminBundle installé
- [ ] Webpack Encore configuré
- [ ] Symfony Mailer installé et configuré
- [ ] VichUploaderBundle pour upload images
- [ ] Doctrine Fixtures pour données de test
- [ ] Foundry pour factories

---

## 1.3 Structure du projet

### Arborescence des contrôleurs

```
src/
├── Controller/
│   ├── Admin/
│   │   ├── DashboardController.php
│   │   ├── BlogPostCrudController.php
│   │   ├── CategoryCrudController.php
│   │   └── ContactRequestCrudController.php
│   ├── HomeController.php
│   ├── ServiceController.php
│   ├── ProjectController.php
│   ├── BlogController.php
│   ├── ContactController.php
│   └── PageController.php (À propos, Mentions légales)
```

### Checklist Structure

- [ ] Créer dossier `Controller/Admin/`
- [ ] Créer structure des contrôleurs principaux
- [ ] Configurer le routing dans `config/routes.yaml`

---

## 1.4 Configuration du routing

### Fichier `config/routes.yaml`

```yaml
# Routes principales
home:
    path: /
    controller: App\Controller\HomeController::index

# Services
services:
    path: /services
    controller: App\Controller\ServiceController::index

service_show:
    path: /services/{slug}
    controller: App\Controller\ServiceController::show

# Projets
projects:
    path: /projets
    controller: App\Controller\ProjectController::index

project_show:
    path: /projets/{slug}
    controller: App\Controller\ProjectController::show

# Blog
blog:
    path: /blog
    controller: App\Controller\BlogController::index

blog_category:
    path: /blog/categorie/{slug}
    controller: App\Controller\BlogController::category

blog_post:
    path: /blog/{slug}
    controller: App\Controller\BlogController::show

# Pages institutionnelles
about:
    path: /a-propos
    controller: App\Controller\PageController::about

contact:
    path: /contact
    controller: App\Controller\ContactController::index

legal:
    path: /mentions-legales
    controller: App\Controller\PageController::legal

privacy:
    path: /politique-confidentialite
    controller: App\Controller\PageController::privacy
```

### Checklist Routing

- [ ] Configurer toutes les routes principales
- [ ] Vérifier la cohérence des URLs SEO-friendly
- [ ] Tester que les routes sont accessibles

---

## 1.5 Création des entités

### Entité BlogPost

```php
// src/Entity/BlogPost.php
namespace App\Entity;

use App\Repository\BlogPostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
class BlogPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 300)]
    private ?string $excerpt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'blogPosts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $featuredImage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $publishedAt = null;

    #[ORM\Column(length: 20)]
    private ?string $status = 'draft'; // draft, published

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $metaDescription = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    // Getters et setters...
}
```

### Entité Category

```php
// src/Entity/Category.php
namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: BlogPost::class)]
    private Collection $blogPosts;

    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
    }

    // Getters et setters...
}
```

### Entité ContactRequest

```php
// src/Entity/ContactRequest.php
namespace App\Entity;

use App\Repository\ContactRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRequestRepository::class)]
class ContactRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $projectType = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $estimatedBudget = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $submittedAt = null;

    #[ORM\Column(length: 20)]
    private ?string $status = 'new'; // new, in_progress, closed

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    // Getters et setters...
}
```

### Entité Project (optionnel pour Phase 1)

```php
// src/Entity/Project.php
namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnail = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $technologies = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $context = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $solution = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $results = null;

    #[ORM\Column]
    private ?bool $published = false;

    // Getters et setters...
}
```

### Checklist Entités

- [ ] Créer entité `BlogPost`
- [ ] Créer entité `Category`
- [ ] Créer entité `ContactRequest`
- [ ] Créer entité `Project` (optionnel phase 1)
- [ ] Créer entité `Tag` (optionnel)
- [ ] Générer les repositories
- [ ] Valider les relations entre entités

---

## 1.6 Migrations de base de données

```bash
# Créer la première migration
php bin/console make:migration

# Examiner le fichier de migration généré
# migrations/VersionXXXXXXXX.php

# Exécuter la migration
php bin/console doctrine:migrations:migrate
```

### Checklist Migrations

- [ ] Créer la migration initiale
- [ ] Vérifier le SQL généré
- [ ] Exécuter la migration
- [ ] Vérifier que les tables sont créées

---

## 1.7 Fixtures de données de test

### Exemple de fixture pour Category

```php
// src/DataFixtures/CategoryFixtures.php
namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Cocur\Slugify\Slugify;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $slugify = new Slugify();

        $categories = [
            'SEO Local' => 'Conseils pour améliorer votre référencement local à Nîmes',
            'Développement Web' => 'Actualités et astuces sur le développement web',
            'Applications Mobiles' => 'Tout savoir sur les applications mobiles',
            'Solutions Digitales' => 'Solutions pour digitaliser votre entreprise',
        ];

        foreach ($categories as $name => $description) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug($slugify->slugify($name));
            $category->setDescription($description);

            $manager->persist($category);

            // Référence pour utiliser dans BlogPostFixtures
            $this->addReference('category-' . $slugify->slugify($name), $category);
        }

        $manager->flush();
    }
}
```

### Checklist Fixtures

- [ ] Créer fixtures pour `Category`
- [ ] Créer fixtures pour `BlogPost` (exemples)
- [ ] Créer fixtures pour `Project` (exemples)
- [ ] Charger les fixtures : `php bin/console doctrine:fixtures:load`

---

## 1.8 Configuration Webpack Encore

### Fichier `webpack.config.js`

```javascript
const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .enableSassLoader()
    .enablePostCssLoader()
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
;

module.exports = Encore.getWebpackConfig();
```

### Structure des assets

```
assets/
├── app.js              # Point d'entrée JS
├── styles/
│   ├── app.scss        # Point d'entrée SCSS
│   ├── _variables.scss
│   └── _mixins.scss
└── images/
    └── .gitkeep
```

### Checklist Webpack

- [ ] Configurer `webpack.config.js`
- [ ] Créer `assets/app.js`
- [ ] Créer `assets/styles/app.scss`
- [ ] Installer les dépendances npm : `npm install`
- [ ] Compiler les assets : `npm run dev`
- [ ] Vérifier que les assets sont générés dans `public/build/`

---

## 1.9 Template de base Twig

### Fichier `templates/base.html.twig`

```twig
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}Digitalfy - Développeur Freelance Nîmes{% endblock %}</title>

    {% block meta %}
        <meta name="description" content="{% block meta_description %}Développeur web & mobile freelance à Nîmes{% endblock %}">
        <link rel="canonical" href="{{ app.request.uri }}">
    {% endblock %}

    {% block stylesheets %}
        {{ encore_entry_link_tags('app') }}
    {% endblock %}

    {% block head_scripts %}{% endblock %}
</head>
<body>
    {% include 'partials/_navigation.html.twig' %}

    <main>
        {% block body %}{% endblock %}
    </main>

    {% include 'partials/_footer.html.twig' %}

    {% block javascripts %}
        {{ encore_entry_script_tags('app') }}
    {% endblock %}
</body>
</html>
```

### Checklist Templates

- [ ] Créer `templates/base.html.twig`
- [ ] Créer `templates/partials/_navigation.html.twig`
- [ ] Créer `templates/partials/_footer.html.twig`
- [ ] Créer dossier `templates/components/`

---

## 1.10 Configuration environnement

### Fichier `.env.local` à créer

```env
# Base de données
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/digitalfy_vitrine?serverVersion=15&charset=utf8"

# Mailer
MAILER_DSN=smtp://localhost:1025

# App
APP_ENV=dev
APP_SECRET=your-secret-key-here
```

### Checklist Configuration

- [ ] Créer `.env.local` (ne pas committer)
- [ ] Configurer DATABASE_URL
- [ ] Configurer MAILER_DSN
- [ ] Générer APP_SECRET si nécessaire
- [ ] Ajouter `.env.local` au `.gitignore`

---

## ✅ Checklist finale Phase 1

### Configuration & Installation
- [ ] Symfony installé et fonctionnel
- [ ] Tous les bundles installés
- [ ] Base de données configurée
- [ ] Webpack Encore configuré et compilé

### Structure
- [ ] Arborescence des contrôleurs créée
- [ ] Routing configuré
- [ ] Templates de base créés

### Entités & Base de données
- [ ] Toutes les entités créées
- [ ] Migrations générées et exécutées
- [ ] Fixtures créées et chargées

### Tests
- [ ] Site accessible sur `http://localhost:8000`
- [ ] Compilation assets fonctionne
- [ ] Base de données contient les fixtures

---

## 🚀 Prochaine étape

Une fois cette phase terminée, passer à la [Phase 2 : Pages principales](02-pages-principales.md)

---

*Document généré le 2025-11-18*
