# Phase 2 : Pages principales

**Durée** : 5 jours (Semaine 2)
**Objectif** : Créer les pages stratégiques du site avec contenu SEO optimisé

---

## 📋 Vue d'ensemble

Cette phase se concentre sur les pages essentielles du site :
- Page d'accueil (conversion + SEO local)
- 4 pages services (cœur de l'offre)
- Pages institutionnelles (crédibilité)

Toutes les pages doivent être optimisées SEO dès leur création.

---

## 2.1 Page d'accueil

### URL : `/`

### Contrôleur

```php
// src/Controller/HomeController.php
namespace App\Controller;

use App\Repository\BlogPostRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        BlogPostRepository $blogPostRepo,
        ProjectRepository $projectRepo
    ): Response {
        // Récupérer les 3 derniers articles publiés
        $recentPosts = $blogPostRepo->findBy(
            ['status' => 'published'],
            ['publishedAt' => 'DESC'],
            3
        );

        // Récupérer les 3 projets mis en avant
        $featuredProjects = $projectRepo->findBy(
            ['published' => true],
            ['id' => 'DESC'],
            3
        );

        return $this->render('home/index.html.twig', [
            'recentPosts' => $recentPosts,
            'featuredProjects' => $featuredProjects,
        ]);
    }
}
```

### Template `templates/home/index.html.twig`

Voir le fichier complet dans [content/contenu-pages.md](../content/contenu-pages.md)

**Structure de la page** :
1. Hero Section
2. Services (3 cards)
3. Pourquoi un développeur local ?
4. Projets récents
5. Pour qui je travaille ?
6. FAQ (optionnel)
7. CTA final

### SEO

```twig
{% block title %}Développeur Freelance à Nîmes – Sites web & applications mobiles | Digitalfy{% endblock %}

{% block meta_description %}
    Développeur web & mobile freelance à Nîmes. Création de sites internet, applications mobiles React Native, et solutions digitales pour TPE/PME et restaurateurs.
{% endblock %}

{% block head_scripts %}
    {# Schema.org LocalBusiness #}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "Digitalfy",
        "image": "{{ asset('images/logo.png') }}",
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
        "url": "{{ url('home') }}",
        "telephone": "+33XXXXXXXXX",
        "priceRange": "€€",
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "opens": "09:00",
            "closes": "18:00"
        }
    }
    </script>
{% endblock %}
```

### Checklist Page d'accueil

- [ ] Créer `HomeController`
- [ ] Créer template `home/index.html.twig`
- [ ] Intégrer le contenu SEO optimisé
- [ ] Ajouter Schema.org LocalBusiness
- [ ] Ajouter les CTAs principaux
- [ ] Tester le responsive
- [ ] Vérifier meta tags

---

## 2.2 Pages Services

### 2.2.1 Page : Développement d'application mobile

**URL** : `/services/developpement-application-mobile-nimes`

#### Contrôleur

```php
// src/Controller/ServiceController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/services', name: 'services')]
    public function index(): Response
    {
        return $this->render('service/index.html.twig');
    }

    #[Route('/services/{slug}', name: 'service_show')]
    public function show(string $slug): Response
    {
        // Map des services disponibles
        $services = [
            'developpement-application-mobile-nimes' => [
                'title' => 'Développement d\'applications mobiles à Nîmes',
                'template' => 'service/mobile-app.html.twig',
            ],
            'creation-site-internet-nimes' => [
                'title' => 'Création de site internet à Nîmes',
                'template' => 'service/website.html.twig',
            ],
            'solutions-digitales-restauration-nimes' => [
                'title' => 'Solutions digitales pour restaurants',
                'template' => 'service/restaurant.html.twig',
            ],
            'maintenance-support' => [
                'title' => 'Maintenance & Support',
                'template' => 'service/maintenance.html.twig',
            ],
        ];

        if (!isset($services[$slug])) {
            throw $this->createNotFoundException('Service non trouvé');
        }

        $service = $services[$slug];

        return $this->render($service['template'], [
            'service' => $service,
        ]);
    }
}
```

#### Template `templates/service/mobile-app.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}Développement d'application mobile à Nîmes – React Native & Expo | Digitalfy{% endblock %}

{% block meta_description %}
    Création d'applications mobiles iOS & Android à Nîmes avec React Native. Développeur freelance pour TPE/PME, restaurants et projets sur mesure.
{% endblock %}

{% block body %}
<article class="service-page">
    {# Hero Section #}
    <section class="service-hero">
        <div class="container">
            <h1>Développement d'applications mobiles à Nîmes – React Native & Expo</h1>

            <p class="lead">
                À Nîmes et dans le Gard, de plus en plus d'entreprises se tournent vers le mobile
                pour moderniser leur activité. Je vous accompagne dans la création d'applications
                mobiles iOS et Android sur mesure.
            </p>
        </div>
    </section>

    {# Pourquoi une app mobile ? #}
    <section class="service-section">
        <div class="container">
            <h2>Pourquoi une application mobile pour votre activité ?</h2>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">📱</div>
                    <h3>Simplifier les commandes</h3>
                    <p>Facilitez la prise de commandes et les réservations</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">📊</div>
                    <h3>Centraliser les infos</h3>
                    <p>Toutes vos informations importantes au même endroit</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">💬</div>
                    <h3>Communication directe</h3>
                    <p>Restez en contact avec vos clients facilement</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">⚡</div>
                    <h3>Automatiser les tâches</h3>
                    <p>Gagnez du temps sur les tâches répétitives</p>
                </div>
            </div>
        </div>
    </section>

    {# Types d'applications #}
    <section class="service-section bg-light">
        <div class="container">
            <h2>Les types d'applications que je développe</h2>

            <ul class="app-types-list">
                <li>Application de réservation (restaurants, praticiens, événements)</li>
                <li>Click & collect pour restaurants ou commerces</li>
                <li>Application interne de gestion</li>
                <li>Application de suivi (budget, coaching, habitudes)</li>
                <li>MVP pour tester une idée de startup</li>
            </ul>
        </div>
    </section>

    {# Ma méthode #}
    <section class="service-section">
        <div class="container">
            <h2>Ma méthode de travail</h2>

            <div class="method-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Échange initial</h3>
                    <p>Discussion sur vos besoins et objectifs</p>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Spécifications</h3>
                    <p>Formalisation des fonctionnalités</p>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Maquettes</h3>
                    <p>Validation du parcours utilisateur</p>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Développement</h3>
                    <p>Création et tests de l'application</p>
                </div>

                <div class="step">
                    <div class="step-number">5</div>
                    <h3>Publication</h3>
                    <p>Accompagnement et prise en main</p>
                </div>
            </div>
        </div>
    </section>

    {# Technologies #}
    <section class="service-section bg-light">
        <div class="container">
            <h2>Technologies utilisées</h2>

            <div class="tech-list">
                <div class="tech-item">
                    <strong>React Native & Expo</strong>
                    <p>Applications multiplateformes iOS & Android</p>
                </div>
                <div class="tech-item">
                    <strong>API Backend</strong>
                    <p>Symfony ou autre pour gérer les données</p>
                </div>
                <div class="tech-item">
                    <strong>Services tiers</strong>
                    <p>Paiement, notifications, analytics</p>
                </div>
            </div>
        </div>
    </section>

    {# FAQ #}
    <section class="service-section">
        <div class="container">
            <h2>Questions fréquentes</h2>

            <div class="faq">
                <div class="faq-item">
                    <h3>Combien coûte une application mobile ?</h3>
                    <p>
                        Le prix dépend des fonctionnalités et de la complexité.
                        Après notre premier échange, je vous fournis un devis détaillé.
                    </p>
                </div>

                <div class="faq-item">
                    <h3>En combien de temps l'application peut-elle être en ligne ?</h3>
                    <p>
                        Pour une application simple, comptez entre 4 et 8 semaines,
                        du cadrage à la mise en production.
                    </p>
                </div>

                <div class="faq-item">
                    <h3>Puis-je faire évoluer l'application plus tard ?</h3>
                    <p>
                        Oui. L'architecture est pensée pour évoluer : nouvelles fonctionnalités,
                        design amélioré, connexion à d'autres services.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {# CTA #}
    <section class="service-cta">
        <div class="container">
            <h2>Travaillons ensemble sur votre application</h2>
            <p>Vous avez une idée d'application ou un besoin concret ?</p>

            <a href="{{ path('contact') }}" class="btn btn-primary btn-lg">
                Contactez-moi pour en parler
            </a>
        </div>
    </section>
</article>

{# Schema.org Service #}
{% block head_scripts %}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "Développement d'application mobile à Nîmes",
    "description": "Création d'applications mobiles iOS & Android avec React Native pour TPE/PME et restaurateurs",
    "provider": {
        "@type": "LocalBusiness",
        "name": "Digitalfy"
    },
    "areaServed": {
        "@type": "City",
        "name": "Nîmes"
    }
}
</script>
{% endblock %}
{% endblock %}
```

#### Checklist Service Mobile

- [ ] Créer template `service/mobile-app.html.twig`
- [ ] Intégrer le contenu complet du guide SEO
- [ ] Ajouter Schema.org Service
- [ ] Ajouter FAQ avec Schema FAQPage
- [ ] Ajouter CTAs pertinents
- [ ] Tester responsive

---

### 2.2.2 Page : Création de site internet

**URL** : `/services/creation-site-internet-nimes`

**Template** : `templates/service/website.html.twig`

**Structure similaire** :
- Hero avec H1
- Pourquoi un site internet ?
- Types de sites
- Optimisation SEO local
- Processus
- Tarifs
- FAQ
- CTA

Voir contenu complet dans [../content/contenu-pages.md](../content/contenu-pages.md#page-creation-site-internet)

#### Checklist Service Website

- [ ] Créer template `service/website.html.twig`
- [ ] Intégrer contenu SEO
- [ ] Schema.org Service
- [ ] CTA vers contact

---

### 2.2.3 Page : Solutions digitales restauration

**URL** : `/services/solutions-digitales-restauration-nimes`

**Template** : `templates/service/restaurant.html.twig`

**Points clés** :
- Mise en avant de l'expertise restauration (30 ans)
- Problèmes concrets des restaurateurs
- Solutions proposées (réservation, click & collect)
- Exemples de scénarios
- Processus adapté

#### Checklist Service Restaurant

- [ ] Créer template `service/restaurant.html.twig`
- [ ] Intégrer contenu SEO
- [ ] Mettre en avant double expertise
- [ ] Schema.org Service
- [ ] CTA spécifique resto

---

### 2.2.4 Page : Maintenance & Support

**URL** : `/services/maintenance-support`

**Template** : `templates/service/maintenance.html.twig`

**Structure** :
- Pourquoi la maintenance ?
- Ce qui peut être pris en charge
- Pour qui ?
- Fonctionnement & tarifs
- CTA

#### Checklist Service Maintenance

- [ ] Créer template `service/maintenance.html.twig`
- [ ] Intégrer contenu SEO
- [ ] Schema.org Service
- [ ] CTA vers contact

---

## 2.3 Pages institutionnelles

### 2.3.1 Page À propos

**URL** : `/a-propos`

#### Contrôleur

```php
// src/Controller/PageController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/a-propos', name: 'about')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig');
    }

    #[Route('/mentions-legales', name: 'legal')]
    public function legal(): Response
    {
        return $this->render('page/legal.html.twig');
    }

    #[Route('/politique-confidentialite', name: 'privacy')]
    public function privacy(): Response
    {
        return $this->render('page/privacy.html.twig');
    }
}
```

#### Template `templates/page/about.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}À propos – Marc Dubois, développeur freelance à Nîmes | Digitalfy{% endblock %}

{% block meta_description %}
    Parcours de Marc Dubois, développeur web & mobile freelance à Nîmes. 30 ans d'expérience en restauration, reconversion dans le développement.
{% endblock %}

{% block body %}
<article class="about-page">
    <section class="about-hero">
        <div class="container">
            <h1>À propos de Digitalfy</h1>
        </div>
    </section>

    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-image">
                    <img src="{{ asset('images/profile.jpg') }}" alt="Marc Dubois">
                </div>

                <div class="about-content">
                    <h2>Qui suis-je ?</h2>
                    <p>
                        Je m'appelle Marc Dubois, je suis développeur web & mobile freelance basé à Nîmes.
                        Après plus de 30 ans d'expérience dans la restauration, dont 10 ans comme directeur
                        et chef de cuisine, j'ai choisi de me reconvertir dans le développement d'applications
                        et de sites internet.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section bg-light">
        <div class="container">
            <h2>De la restauration au développement</h2>

            <p>Mon expérience précédente m'a appris :</p>

            <ul class="skills-list">
                <li>La gestion de la pression et des imprévus</li>
                <li>La rigueur dans l'organisation</li>
                <li>L'importance du service client</li>
                <li>La nécessité de solutions simples et efficaces</li>
            </ul>

            <p>
                Ces qualités sont aujourd'hui au cœur de ma pratique de développeur :
                je ne propose pas des "projets techniques", je construis des outils qui
                répondent à des contraintes réelles.
            </p>
        </div>
    </section>

    <section class="about-section">
        <div class="container">
            <h2>Ce que je vous apporte concrètement</h2>

            <div class="value-props">
                <div class="value-prop">
                    <h3>Approche orientée métier</h3>
                    <p>Pas uniquement technique, mais adaptée à votre activité</p>
                </div>

                <div class="value-prop">
                    <h3>Discours clair</h3>
                    <p>Sans jargon technique, même sur des sujets complexes</p>
                </div>

                <div class="value-prop">
                    <h3>Accompagnement complet</h3>
                    <p>Du besoin à la mise en ligne et au-delà</p>
                </div>

                <div class="value-prop">
                    <h3>Relation de confiance</h3>
                    <p>Un partenaire sur la durée, pas juste un prestataire</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section bg-light">
        <div class="container">
            <h2>Zone d'intervention</h2>

            <p>Je travaille principalement avec des clients :</p>

            <ul>
                <li>À Nîmes et dans le Gard</li>
                <li>En Occitanie</li>
                <li>En France, à distance, lorsque le projet s'y prête</li>
            </ul>

            <p>
                Les échanges peuvent se faire en visioconférence ou en rendez-vous
                physique selon votre localisation.
            </p>
        </div>
    </section>

    <section class="about-cta">
        <div class="container">
            <h2>Travaillons ensemble</h2>
            <a href="{{ path('contact') }}" class="btn btn-primary btn-lg">
                Contactez-moi
            </a>
        </div>
    </section>
</article>
{% endblock %}
```

#### Checklist Page À propos

- [ ] Créer template `page/about.html.twig`
- [ ] Intégrer contenu SEO
- [ ] Ajouter photo professionnelle
- [ ] Mettre en avant reconversion
- [ ] CTA vers contact

---

### 2.3.2 Page Contact

**URL** : `/contact`

#### Contrôleur

```php
// src/Controller/ContactController.php
namespace App\Controller;

use App\Entity\ContactRequest;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $contactRequest = new ContactRequest();
        $form = $this->createForm(ContactType::class, $contactRequest);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRequest->setSubmittedAt(new \DateTime());
            $contactRequest->setStatus('new');

            $em->persist($contactRequest);
            $em->flush();

            // Envoyer email de notification
            $email = (new Email())
                ->from('noreply@digitalfy.fr')
                ->to($this->getParameter('admin_email'))
                ->subject('Nouvelle demande de contact')
                ->html($this->renderView('emails/contact-notification.html.twig', [
                    'contactRequest' => $contactRequest,
                ]));

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé. Je vous recontacterai rapidement.');

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
```

#### Formulaire

```php
// src/Form/ContactType.php
namespace App\Form;

use App\Entity\ContactRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom / Prénom *',
                'attr' => ['placeholder' => 'Jean Dupont'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'attr' => ['placeholder' => 'jean@exemple.fr'],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['placeholder' => '06 12 34 56 78'],
            ])
            ->add('projectType', ChoiceType::class, [
                'label' => 'Type de projet *',
                'choices' => [
                    'Site vitrine' => 'site-vitrine',
                    'Site professionnel' => 'site-pro',
                    'Application mobile' => 'app-mobile',
                    'Solution restaurant' => 'solution-restaurant',
                    'Maintenance' => 'maintenance',
                    'Autre' => 'autre',
                ],
            ])
            ->add('estimatedBudget', ChoiceType::class, [
                'label' => 'Budget estimé',
                'required' => false,
                'choices' => [
                    'Moins de 2000€' => '< 2000',
                    '2000€ - 5000€' => '2000-5000',
                    '5000€ - 10000€' => '5000-10000',
                    'Plus de 10000€' => '> 10000',
                    'Je ne sais pas' => 'unknown',
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message *',
                'attr' => [
                    'rows' => 6,
                    'placeholder' => 'Décrivez votre projet en quelques lignes...',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactRequest::class,
        ]);
    }
}
```

#### Template `templates/contact/index.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}Contact – Développeur freelance à Nîmes | Digitalfy{% endblock %}

{% block meta_description %}
    Contactez Digitalfy pour un projet de site internet, application mobile ou solution digitale à Nîmes et dans le Gard.
{% endblock %}

{% block body %}
<div class="contact-page">
    <section class="contact-hero">
        <div class="container">
            <h1>Contactez Digitalfy</h1>
        </div>
    </section>

    <section class="contact-content">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-form-wrapper">
                    <h2>Discutons de votre projet</h2>

                    <p>
                        Vous avez un projet de site internet, d'application mobile ou
                        de solution digitale ? Expliquez-moi votre besoin en quelques lignes.
                    </p>

                    {% for message in app.flashes('success') %}
                        <div class="alert alert-success">{{ message }}</div>
                    {% endfor %}

                    {{ form_start(form) }}
                        {{ form_row(form.name) }}
                        {{ form_row(form.email) }}
                        {{ form_row(form.phone) }}
                        {{ form_row(form.projectType) }}
                        {{ form_row(form.estimatedBudget) }}
                        {{ form_row(form.message) }}

                        <button type="submit" class="btn btn-primary">
                            Envoyer le message
                        </button>
                    {{ form_end(form) }}
                </div>

                <div class="contact-info">
                    <h2>Informations pratiques</h2>

                    <div class="info-item">
                        <h3>📍 Localisation</h3>
                        <p>Nîmes (Gard)</p>
                    </div>

                    <div class="info-item">
                        <h3>🌍 Zone d'intervention</h3>
                        <p>Gard, Occitanie, France (à distance)</p>
                    </div>

                    <div class="info-item">
                        <h3>💬 Langue</h3>
                        <p>Français</p>
                    </div>

                    <div class="info-item">
                        <h3>💼 LinkedIn</h3>
                        <p><a href="#">Voir mon profil</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
{% endblock %}
```

#### Checklist Page Contact

- [ ] Créer `ContactController`
- [ ] Créer `ContactType` form
- [ ] Créer template `contact/index.html.twig`
- [ ] Créer template email `emails/contact-notification.html.twig`
- [ ] Configurer MAILER_DSN dans `.env`
- [ ] Tester soumission formulaire
- [ ] Vérifier réception email

---

### 2.3.3 Page Mentions légales

**URL** : `/mentions-legales`

#### Template `templates/page/legal.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}Mentions légales | Digitalfy{% endblock %}

{% block body %}
<div class="legal-page">
    <div class="container">
        <h1>Mentions légales</h1>

        <h2>Éditeur du site</h2>
        <p>
            <strong>Digitalfy</strong><br>
            Marc Dubois<br>
            Auto-entrepreneur<br>
            SIRET : [À compléter]<br>
            Adresse : [À compléter]<br>
            Email : contact@digitalfy.fr
        </p>

        <h2>Hébergement</h2>
        <p>
            [Nom de l'hébergeur]<br>
            [Adresse]<br>
            [Téléphone]
        </p>

        <h2>Propriété intellectuelle</h2>
        <p>
            L'ensemble du contenu de ce site (textes, images, vidéos) est la propriété
            exclusive de Digitalfy, sauf mention contraire.
        </p>

        <h2>Données personnelles</h2>
        <p>
            Les données collectées via le formulaire de contact sont utilisées uniquement
            dans le cadre de la relation commerciale. Conformément au RGPD, vous disposez
            d'un droit d'accès, de rectification et de suppression de vos données.
        </p>
        <p>
            Pour exercer vos droits : <a href="mailto:contact@digitalfy.fr">contact@digitalfy.fr</a>
        </p>
    </div>
</div>
{% endblock %}
```

#### Checklist Mentions légales

- [ ] Créer template `page/legal.html.twig`
- [ ] Compléter informations légales (SIRET, adresse)
- [ ] Ajouter info hébergeur
- [ ] Vérifier conformité RGPD

---

## ✅ Checklist finale Phase 2

### Page d'accueil
- [ ] Contrôleur créé
- [ ] Template complet avec toutes les sections
- [ ] Contenu SEO intégré
- [ ] Schema.org LocalBusiness
- [ ] CTAs en place
- [ ] Responsive testé

### Pages services (4 pages)
- [ ] Développement application mobile
- [ ] Création de site internet
- [ ] Solutions restauration
- [ ] Maintenance & support
- [ ] Chaque page avec Schema.org Service
- [ ] FAQ sur chaque page
- [ ] CTAs vers contact

### Pages institutionnelles
- [ ] Page À propos
- [ ] Page Contact avec formulaire fonctionnel
- [ ] Page Mentions légales
- [ ] Email de notification fonctionne

### Tests
- [ ] Toutes les pages accessibles
- [ ] Formulaire contact fonctionne
- [ ] Emails reçus correctement
- [ ] Responsive OK sur toutes les pages
- [ ] Meta tags vérifiés

---

## 🚀 Prochaine étape

Une fois cette phase terminée, passer à la [Phase 3 : Système de blog](03-systeme-blog.md)

---

*Document généré le 2025-11-18*
