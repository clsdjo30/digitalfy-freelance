# Phase 8 : Tracking & Analytics

**Durée** : 1 jour (Semaine 5)
**Objectif** : Mettre en place le suivi des performances et conversions

---

## 8.1 Google Analytics GA4

### Installation

```twig
{# templates/base.html.twig - dans <head> #}
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
```

### Événements personnalisés

```javascript
// Soumission formulaire contact
gtag('event', 'contact_form_submit', {
    'project_type': projectType,
    'budget': budget
});

// Clic CTA
gtag('event', 'cta_click', {
    'cta_location': 'hero',
    'cta_text': 'Planifier un appel'
});

// Lecture article blog
gtag('event', 'article_read', {
    'article_title': articleTitle,
    'category': category
});
```

### Checklist GA4

- [ ] Compte GA4 créé
- [ ] Tag installé sur toutes les pages
- [ ] Événements personnalisés configurés
- [ ] Objectifs de conversion définis
- [ ] Tester avec GA Debugger

---

## 8.2 Google Search Console

### Configuration

1. Vérifier la propriété du site
2. Soumettre sitemap.xml
3. Configurer domaine préféré (avec ou sans www)

### Checklist GSC

- [ ] Propriété vérifiée
- [ ] Sitemap soumis
- [ ] Domaine préféré configuré
- [ ] Monitoring des erreurs crawl

---

## 8.3 Outils marketing (optionnel)

### Facebook Pixel

```html
<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', 'YOUR_PIXEL_ID');
  fbq('track', 'PageView');
</script>
```

### Checklist Optionnel

- [ ] Facebook Pixel (si Ads prévues)
- [ ] LinkedIn Insight Tag
- [ ] Microsoft Clarity ou Hotjar (heatmaps)

---

## ✅ Checklist finale Phase 8

- [ ] Google Analytics GA4 installé
- [ ] Événements personnalisés configurés
- [ ] Google Search Console configuré
- [ ] Sitemap soumis
- [ ] Tests effectués

---

## 🚀 Prochaine étape

Passer à la [Phase 9 : Tests & QA](09-tests-qa.md)

---

*Document généré le 2025-11-18*
