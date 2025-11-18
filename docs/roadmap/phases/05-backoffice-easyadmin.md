# Phase 5 : Backoffice EasyAdmin

**Durée** : 5 jours (Semaine 4)
**Objectif** : Mettre en place le dashboard admin complet pour gérer le contenu

---

## 📋 Vue d'ensemble

Le backoffice EasyAdmin permet de :
- Gérer les articles de blog sans toucher au code
- Gérer les catégories
- Visualiser et traiter les demandes de contact
- Gérer le contenu de manière autonome

---

## 5.1 Installation et configuration

```bash
composer require easycorp/easyadmin-bundle
```

### Dashboard Controller

```php
// src/Controller/Admin/DashboardController.php
namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\ContactRequest;
use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Digitalfy Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section('Blog');
        yield MenuItem::linkToCrud('Articles', 'fa fa-file-text', BlogPost::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tags', Category::class);
        
        yield MenuItem::section('Projets');
        yield MenuItem::linkToCrud('Projets', 'fa fa-briefcase', Project::class);
        
        yield MenuItem::section('Contact');
        yield MenuItem::linkToCrud('Demandes', 'fa fa-envelope', ContactRequest::class);
        
        yield MenuItem::section('Site');
        yield MenuItem::linkToRoute('Voir le site', 'fa fa-eye', 'home');
    }
}
```

### Checklist Configuration

- [ ] Installer EasyAdminBundle
- [ ] Créer DashboardController
- [ ] Configurer le menu
- [ ] Sécuriser l'accès (`/admin`)

---

## 5.2 CRUD Articles de blog

```php
// src/Controller/Admin/BlogPostCrudController.php
namespace App\Controller\Admin;

use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class BlogPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            SlugField::new('slug')->setTargetFieldName('title'),
            TextareaField::new('excerpt', 'Extrait')
                ->setHelp('160 caractères max pour le SEO'),
            TextareaField::new('content', 'Contenu')
                ->setFormType(CKEditorType::class)
                ->hideOnIndex(),
            AssociationField::new('category', 'Catégorie'),
            ImageField::new('featuredImage', 'Image à la une')
                ->setBasePath('uploads/blog')
                ->setUploadDir('public/uploads/blog')
                ->setUploadedFileNamePattern('[randomhash].[extension]'),
            DateTimeField::new('publishedAt', 'Date de publication'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Brouillon' => 'draft',
                    'Publié' => 'published',
                ]),
            TextField::new('metaTitle', 'Meta Title')
                ->setHelp('60 caractères max')
                ->hideOnIndex(),
            TextareaField::new('metaDescription', 'Meta Description')
                ->setHelp('160 caractères max')
                ->hideOnIndex(),
        ];
    }
}
```

### Checklist CRUD Blog

- [ ] Créer BlogPostCrudController
- [ ] Configurer tous les champs
- [ ] Installer FOSCKEditorBundle pour WYSIWYG
- [ ] Configurer upload images
- [ ] Ajouter filtres (statut, catégorie)
- [ ] Ajouter recherche par titre

---

## 5.3 CRUD Catégories

```php
// src/Controller/Admin/CategoryCrudController.php
namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            SlugField::new('slug')->setTargetFieldName('name'),
            TextareaField::new('description', 'Description'),
        ];
    }
}
```

### Checklist CRUD Category

- [ ] Créer CategoryCrudController
- [ ] Configurer champs
- [ ] Tester CRUD complet

---

## 5.4 CRUD Demandes de contact

```php
// src/Controller/Admin/ContactRequestCrudController.php
namespace App\Controller\Admin;

use App\Entity\ContactRequest;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Demande de contact')
            ->setEntityLabelInPlural('Demandes de contact')
            ->setDefaultSort(['submittedAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            EmailField::new('email', 'Email'),
            TextField::new('phone', 'Téléphone'),
            TextField::new('projectType', 'Type de projet'),
            TextField::new('estimatedBudget', 'Budget estimé'),
            TextareaField::new('message', 'Message')
                ->hideOnIndex(),
            DateTimeField::new('submittedAt', 'Date')
                ->setFormat('dd/MM/yyyy HH:mm'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Nouveau' => 'new',
                    'En cours' => 'in_progress',
                    'Clôturé' => 'closed',
                ]),
            TextareaField::new('notes', 'Notes internes')
                ->hideOnIndex(),
        ];
    }
}
```

### Checklist CRUD Contact

- [ ] Créer ContactRequestCrudController
- [ ] Configurer tous les champs
- [ ] Tri par date décroissante
- [ ] Filtres par statut
- [ ] Notes internes

---

## 5.5 Notifications email

Déjà configuré dans Phase 2 (ContactController).

Vérifier que :
- [ ] Email envoyé au freelance à chaque soumission
- [ ] Template email HTML propre
- [ ] Email de confirmation au demandeur (optionnel)

---

## 5.6 Sécurisation

### Créer un utilisateur admin

```php
// src/Entity/User.php
// Entité User standard Symfony

// Créer un utilisateur via console
php bin/console make:user
php bin/console make:auth
```

### Sécuriser le dashboard

```yaml
# config/packages/security.yaml
security:
    firewalls:
        admin:
            pattern: ^/admin
            form_login:
                login_path: app_login
                check_path: app_login
            logout:
                path: app_logout
    
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
```

### Checklist Sécurité

- [ ] Créer entité User
- [ ] Système d'authentification
- [ ] Accès `/admin` protégé
- [ ] Page de login fonctionnelle

---

## ✅ Checklist finale Phase 5

### Configuration
- [ ] EasyAdmin installé
- [ ] Dashboard configuré
- [ ] Menu complet

### CRUD
- [ ] Articles de blog fonctionnel
- [ ] Catégories fonctionnel
- [ ] Demandes de contact fonctionnel
- [ ] Projets (optionnel)

### Fonctionnalités
- [ ] Upload images fonctionne
- [ ] Éditeur WYSIWYG opérationnel
- [ ] Filtres et recherche OK
- [ ] Notifications email

### Sécurité
- [ ] Authentification en place
- [ ] Accès sécurisé

---

## 🚀 Prochaine étape

Passer à la [Phase 6 : Design & Intégration](06-design-integration.md)

---

*Document généré le 2025-11-18*
