# Cahier des Charges – Site Web Freelance (SEO Local Nîmes)

## 1. Objectif du projet
Créer un site web professionnel optimisé SEO pour attirer des clients locaux (Nîmes, Gard, Occitanie) et des prospects professionnels recherchant un développeur freelance spécialisé en :
- Développement mobile **React Native / Expo**
- Développement web **Symfony**
- Création de solutions digitales pour TPE/PME et professionnels de la restauration

Le site doit :
- Renforcer la visibilité locale
- Convertir les visiteurs en clients (prise de rendez-vous, devis, contact)
- Présenter les offres, services et réalisations
- Renforcer la crédibilité et le positionnement du freelance

---

## 2. Publics cibles

### 2.1. Clients privés (TPE/PME – non techniques)
- Restaurateurs
- Artisans
- Commerçants
- Coaches, indépendants, micro-entreprises
- Professions libérales
- Petits commerces locaux

**Besoins :**
- Site vitrine simple
- Présentation claire des services
- Cas clients
- Tarifs / Packs
- Contact facile

### 2.2. Clients professionnels (agences, CTO, entreprises tech)
- Agences web locales (Nîmes / Montpellier / Avignon)
- Startups recherchant un freelance mobile
- Entreprises souhaitant sous-traiter

**Besoins :**
- Stack technique
- Fiabilité du dev
- Portfolio technique
- Compétences précises (Symfony, React Native, Expo)
- Disponibilité / TJM

---

## 3. Arborescence cible SEO (structure optimisée)

```
/
├── services/
│     ├── developpement-application-mobile-nimes
│     ├── creation-site-internet-nimes
│     ├── solutions-digitales-restauration-nimes
│     ├── maintenance-support
├── projets/
│     ├── app-budget-react-native
│     ├── site-vigneron-symfony
│     ├── site-groupe-rock-react
├── blog/
│     ├── click-and-collect-nimes
│     ├── application-mobile-pour-restaurant
│     ├── site-internet-pour-artisan
├── a-propos/
├── contact/
└── mentions-legales/
```

Arborescence pensée pour :
- SEO local
- SEO métier
- SEO problématique/client

---

## 4. Pages et contenus obligatoires

### 4.1. Accueil (Page principale SEO)
**Objectif :** présenter la proposition de valeur + ciblage local (Nîmes).

Contenu :
- H1 : *Développeur freelance à Nîmes – Sites web & applications mobiles*
- Accroche orientée bénéfices
- CTA : *Prendre rendez-vous*
- 3 services phares
- Preuves sociales
- Zone géographique couverte : Nîmes, Gard, Occitanie

SEO :
- Title : “Développeur Freelance à Nîmes – Web & Mobile | Digitalfy”
- Schema.org : LocalBusiness
- Optimisation Core Web Vitals

---

### 4.2. Pages Services (4 pages SEO-first)

#### a) Développement d’application mobile (React Native)
- Cible : entreprises souhaitant une app mobile
- Title : *Développeur App Mobile à Nîmes – React Native & Expo*
- Contenu pédagogique + cas d’usage + FAQ

#### b) Création de site internet à Nîmes
- Cible : TPE/PME locales
- Title : *Création de Site Internet à Nîmes – Freelance Symfony*

#### c) Solutions digitales pour restaurants
- Title : *Solutions digitales pour restaurants à Nîmes – Apps & réservation en ligne*
- Référence à ton passé de chef → différenciation

#### d) Maintenance / Support
- Pour clients ayant déjà un site ou une app

---

### 4.3. Portfolio / Études de cas
Chaque projet = page SEO indépendante.

Structure :
- Problème client
- Solution
- Stack utilisée (pour clients pros)
- Résultats
- Images
- CTA

---

### 4.4. Blog
Articles orientés :
- SEO local
- Problématiques métier (resto, artisan)
- Pédagogie digitale

Objectif : attirer du trafic organique.

---

### 4.5. Page À propos
Basée sur ton histoire :
- Reconversion après 30 ans en restauration
- Expertise en gestion de projet / rigueur
- Positionnement : développeur proche du terrain

---

### 4.6. Page Contact
- formulaire
- localisation
- horaires ou disponibilité freelance
- lien Calendly optionnel

Les soumissions du formulaire de contact doivent être :
- envoyées par email au freelance,
- enregistrées en base et visibles dans le dashboard EasyAdmin (voir §5.6).

---

## 5. Fonctionnalités techniques

### 5.1. Rendu Serveur (SEO indispensable)
- Symfony + Twig (SSR)
- Aucune page marketing en SPA React

### 5.2. Performances
- Lazy loading images
- Compression
- Cache HTTP
- CDN (si possible)

### 5.3. SEO Technique
- Sitemap.xml automatique
- Robots.txt propre
- Rich Snippets : LocalBusiness, Services, FAQ
- Balises OG + Twitter Cards
- URLs optimisées (kebab-case + mots-clés locaux)

### 5.4. Tracking
- Google Analytics (GA4)
- Google Search Console
- Pixel Facebook (option)

### 5.5. Backoffice d’administration (EasyAdmin)

Mise en place d’un **dashboard EasyAdmin** pour gérer les contenus et les données métier.

#### 5.5.1. Gestion des articles de blog

Le backoffice doit permettre :

- **CRUD complet** des articles :
  - créer, modifier, publier, dépublier, supprimer.
- Champs obligatoires pour chaque article :
  - Titre
  - Slug (généré automatiquement à partir du titre, modifiable)
  - Extrait / chapeau (meta description ou résumé court)
  - Contenu principal (éditeur riche type Markdown ou WYSIWYG simple)
  - Catégorie (relation à une entité `Category`)
  - Image à la une (upload d’un fichier, stockage sur disque ou service externe)
  - Date de publication
  - Statut : brouillon / publié
- Champs optionnels pour optimisation SEO :
  - Meta title (si différent du titre)
  - Meta description
  - Tag(s) / mots-clés (champ texte ou relation à une entité `Tag`)

Contraintes :

- Validation de la longueur des champs SEO (guide indicatif dans le formulaire EasyAdmin).
- Le slug doit être **unique** et utilisé dans l’URL publique de l’article.
- L’image à la une doit être téléversée via EasyAdmin, avec prévisualisation miniature.

#### 5.5.2. Gestion des catégories de blog

L’admin doit permettre :

- CRUD complet des catégories :
  - Nom de la catégorie
  - Slug (utilisé dans l’URL `/blog/categorie/{slug}`)
  - Description courte (pour SEO et affichage page catégorie)
- Association des articles à une ou plusieurs catégories (au minimum une obligatoire).

#### 5.5.3. Filtrage et tri

Dans les listes d’articles EasyAdmin, il doit être possible de :

- Filtrer par statut (brouillon / publié)
- Filtrer par catégorie
- Rechercher par titre
- Trier par date de publication

### 5.6. Gestion des demandes de contact dans EasyAdmin

Les soumissions du formulaire `/contact` doivent :

- Être sauvegardées dans une entité `ContactRequest` comprenant :
  - Nom
  - Email
  - Téléphone (optionnel)
  - Type de projet (liste : site vitrine, site pro, application mobile, solution restaurant, autre)
  - Budget estimé (fourchettes ou texte libre)
  - Message
  - Date d’envoi
  - Statut de traitement : nouveau / en cours / clôturé
- Être listées dans une section dédiée du dashboard EasyAdmin.

Fonctionnalités attendues dans EasyAdmin :

- Liste des demandes avec colonnes : date, nom, email, type de projet, statut.
- Détail d’une demande avec tous les champs + champ interne “Notes” (non visible côté front).
- Possibilité de changer le statut (nouveau → en cours → clôturé).
- Option d’export CSV (facultatif mais souhaitable) pour récupérer les demandes.

Parallèlement, chaque soumission doit déclencher :

- L’envoi d’un email de notification au freelance (adresse configurable dans `.env`).
- (Optionnel) un email de confirmation au demandeur.

---

## 6. Stratégie SEO locale

### 6.1. Optimisation Google My Business
- Nom : Digitalfy – Développeur mobile freelance Nîmes
- Catégories : développeur logiciel, service informatique
- Posts réguliers 1×/semaine

### 6.2. Pages Locales
Créer des pages ciblant des villes autour de Nîmes :  
- Montpellier  
- Avignon  
- Arles  
- Beaucaire  
- Alès  

### 6.3. Backlinks locaux
- Annuaire CCI Nîmes
- Pages entreprises Gard
- Partenariats artisans & restos

---

## 7. Design & UX

### 7.1. Style
- Professionnel
- Minimaliste
- Couleurs sobres (bleu, blanc, gris foncé)
- Sections aérées

### 7.2. CTA
- “Demander un devis”
- “Réserver un appel gratuit”
- “Voir mes réalisations”

### 7.3. Responsive total
- mobile-first
- optimisation CLS/LCP

---

## 8. Planning de réalisation

| Phase | Durée | Détails |
|-------|--------|---------|
| Analyse & SEO | 1 semaine | mots-clés, concurrence |
| Design UX/UI | 1 semaine | maquettes |
| Développement Symfony | 2–3 semaines | pages statiques + backoffice blog & contact (EasyAdmin) |
| Intégration contenus | 1 semaine | textes optimisés SEO |
| Tests & optimisation | 3 jours | QA, mobilité, vitesse, vérification SEO |
| Mise en ligne | 1 jour | DNS + search console |

Total : **5 à 6 semaines**

---

## 9. Livrables

- Site complet Symfony (front public)
- Dashboard EasyAdmin configuré :
  - gestion des articles de blog
  - gestion des catégories
  - visualisation des demandes de contact
- Templates Twig SEO-ready
- Contenus optimisés (titles, meta, H1)
- Sitemap + robots.txt
- GMB configuré
- Page Google Search Console
- Guide d’utilisation (front + backoffice)

---

## 10. Évolutions futures

- Ajout de pages thématiques (resto / artisans / formations)
- Landing pages pour Ads (facultatif)
- Version anglaise (si pros internationaux)
- Portail client (devis, factures, documents)
- Système d’emailing simple (newsletter) relié aux demandes / contacts (option)

---

## Fin du cahier des charges
