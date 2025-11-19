# Configuration du Tracking et Analytics

Ce document explique comment configurer et utiliser le système de tracking et analytics du site Digitalfy.

## 📊 Google Analytics GA4

### Configuration

1. **Créer un compte Google Analytics**
   - Rendez-vous sur [Google Analytics](https://analytics.google.com/)
   - Créez une propriété GA4
   - Récupérez votre ID de mesure (format: `G-XXXXXXXXXX`)

2. **Configurer l'ID dans l'environnement**

   Ajoutez votre ID dans le fichier `.env.local` :

   ```bash
   GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
   ```

3. **Activer le tracking**

   Le tracking est automatiquement activé en environnement de production (`APP_ENV=prod`).
   Pour tester en développement, modifiez temporairement la condition dans `templates/partials/_analytics.html.twig`.

### Événements personnalisés implémentés

Le fichier `assets/js/analytics.js` contient plusieurs fonctions pour tracker les événements :

#### 1. Soumission du formulaire de contact
```javascript
trackContactFormSubmit(projectType, budget)
```
Paramètres :
- `projectType` : Type de projet (site vitrine, app mobile, etc.)
- `budget` : Budget estimé

#### 2. Clics sur les CTA
```javascript
trackCtaClick(ctaLocation, ctaText)
```
Paramètres :
- `ctaLocation` : Emplacement du CTA (hero, footer, etc.)
- `ctaText` : Texte du bouton

**Auto-tracking** : Les clics sur les éléments avec la classe `.btn` ou l'attribut `[data-track-cta]` sont automatiquement trackés.

#### 3. Lecture d'article de blog
```javascript
trackArticleRead(articleTitle, category)
```
Paramètres :
- `articleTitle` : Titre de l'article
- `category` : Catégorie de l'article

#### 4. Consultation de projet
```javascript
trackProjectView(projectTitle)
```
Paramètre :
- `projectTitle` : Titre du projet

#### 5. Liens externes
```javascript
trackOutboundLink(url, text)
```
**Auto-tracking** : Les clics sur les liens externes (hors digitalfy.fr) sont automatiquement trackés.

#### 6. Profondeur de scroll
```javascript
trackScrollDepth(percentage)
```
**Auto-tracking** : Le scroll est automatiquement tracké aux paliers 25%, 50%, 75% et 100%.

### Utilisation dans vos templates

Pour tracker un CTA spécifique :

```twig
<a href="{{ path('app_contact') }}"
   class="btn btn--primary"
   data-cta-location="hero">
    Contactez-moi
</a>
```

Pour tracker manuellement un événement :

```javascript
import { trackContactFormSubmit } from './analytics.js';

// Dans votre formulaire
form.addEventListener('submit', (e) => {
    const projectType = document.querySelector('#project_type').value;
    const budget = document.querySelector('#budget').value;
    trackContactFormSubmit(projectType, budget);
});
```

## 🔍 Google Search Console

### Configuration

1. **Vérifier la propriété du site**
   - Rendez-vous sur [Google Search Console](https://search.google.com/search-console)
   - Ajoutez votre propriété (https://www.digitalfy.fr)
   - Vérifiez la propriété (plusieurs méthodes disponibles)

2. **Soumettre le sitemap**
   - URL du sitemap : `https://www.digitalfy.fr/sitemap.xml`
   - Dans Search Console > Sitemaps > Ajouter un nouveau sitemap
   - Entrez `sitemap.xml`

3. **Configurer le domaine préféré**
   - Choisir entre `www.digitalfy.fr` et `digitalfy.fr`
   - Configurer les redirections appropriées

### Surveillance

Vérifiez régulièrement :
- ✅ Erreurs de crawl
- ✅ Couverture des pages
- ✅ Performance dans les résultats de recherche
- ✅ Requêtes de recherche
- ✅ Liens entrants

## 📈 Événements à surveiller

### Objectifs de conversion GA4

Configurez les événements suivants comme conversions dans GA4 :

1. **contact_form_submit** - Soumission du formulaire de contact
2. **cta_click** (avec filtre location=hero) - Clics sur CTA principal
3. **article_read** - Lecture complète d'un article (scroll > 75%)
4. **project_view** - Consultation d'un projet

### KPIs importants

- Taux de conversion du formulaire de contact
- Taux de rebond par page
- Temps moyen sur les articles de blog
- Pages les plus consultées
- Sources de trafic
- Recherches organiques (via Search Console)

## 🎯 Objectifs recommandés

### Court terme (1-3 mois)
- 100+ visiteurs uniques/mois
- 10+ soumissions formulaire/mois
- Taux de rebond < 60%
- Position moyenne dans les résultats < 20 (Search Console)

### Moyen terme (3-6 mois)
- 500+ visiteurs uniques/mois
- 30+ demandes de contact/mois
- Top 10 pour "développeur freelance nîmes"
- 5+ backlinks de qualité

## 🔒 Confidentialité et RGPD

Le tracking GA4 est configuré pour respecter la vie privée :

- ✅ IP anonymisée automatiquement par GA4
- ✅ Tracking uniquement en production
- ✅ Pas de cookies tiers non essentiels sans consentement
- ⚠️ À compléter : Ajouter un bandeau de consentement cookies si nécessaire

### Bandeau cookies (optionnel)

Si vous souhaitez ajouter un bandeau de consentement, considérez :
- [Tarteaucitron.js](https://tarteaucitron.io/) (gratuit, français)
- [CookieBot](https://www.cookiebot.com/) (payant, plus complet)
- [Axeptio](https://www.axeptio.eu/) (français, interface moderne)

## 📱 Facebook Pixel (optionnel)

Si vous prévoyez de faire des publicités Facebook/Instagram :

1. Créez un Pixel Facebook dans le Gestionnaire d'événements
2. Ajoutez l'ID du pixel dans `.env.local` :
   ```bash
   FACEBOOK_PIXEL_ID=XXXXXXXXXXXXX
   ```
3. Modifiez `templates/partials/_analytics.html.twig` pour inclure le script Facebook Pixel

## 📊 Tableaux de bord recommandés

### Dashboard GA4 personnalisé

Créez un dashboard avec :
- Visiteurs en temps réel
- Sources de trafic (semaine / mois)
- Pages les plus vues
- Conversions (formulaires)
- Taux de rebond par page
- Événements personnalisés

### Rapports hebdomadaires

Configurez un rapport automatique par email chaque lundi avec :
- Visiteurs de la semaine
- Nouvelles demandes de contact
- Articles les plus lus
- Nouvelles positions dans Search Console

---

*Document mis à jour : 2025-11-19*
