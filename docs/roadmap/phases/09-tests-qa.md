# Phase 9 : Tests & QA

**Durée** : 3 jours (Semaine 6)
**Objectif** : Tester et valider l'ensemble du site avant mise en production

---

## 9.1 Tests fonctionnels

### Navigation

- [ ] Tous les liens du menu fonctionnent
- [ ] Breadcrumbs corrects
- [ ] Pagination blog fonctionne
- [ ] Filtrage catégorie fonctionne
- [ ] Boutons CTA mènent aux bonnes pages

### Formulaire contact

- [ ] Validation côté client
- [ ] Validation côté serveur
- [ ] Messages d'erreur clairs
- [ ] Email reçu par le freelance
- [ ] Email de confirmation au demandeur (si activé)
- [ ] Données sauvegardées en BDD
- [ ] Protection anti-spam

### Backoffice EasyAdmin

- [ ] Connexion admin fonctionne
- [ ] CRUD articles complet
- [ ] CRUD catégories complet
- [ ] CRUD demandes de contact
- [ ] Upload images fonctionne
- [ ] Éditeur WYSIWYG opérationnel
- [ ] Filtres et recherche OK

---

## 9.2 Tests techniques

### Navigateurs

Tester sur :
- [ ] Chrome (dernière version)
- [ ] Firefox (dernière version)
- [ ] Safari (Mac/iOS)
- [ ] Edge

### Devices

- [ ] iPhone (différentes tailles)
- [ ] Android (différentes tailles)
- [ ] iPad / tablettes
- [ ] Desktop (1920x1080, 1366x768)

### Responsive

- [ ] Menu mobile fonctionne
- [ ] Images adaptatives
- [ ] Grilles s'adaptent
- [ ] Textes lisibles
- [ ] Boutons touch-friendly

### Performance

- [ ] PageSpeed Insights > 90
- [ ] Lighthouse score > 90
- [ ] LCP < 2.5s
- [ ] FID < 100ms
- [ ] CLS < 0.1
- [ ] Temps de chargement < 3s

---

## 9.3 Tests SEO

### On-Page

- [ ] Meta title/description sur toutes les pages
- [ ] Un seul H1 par page
- [ ] Structure Hn logique
- [ ] Alt text sur images
- [ ] URLs SEO-friendly
- [ ] Canonical tags corrects

### Données structurées

- [ ] Schema.org validé avec Rich Results Test
- [ ] LocalBusiness sur accueil
- [ ] Service sur pages services
- [ ] Article sur blog
- [ ] Pas d'erreurs de validation

### Fichiers techniques

- [ ] Sitemap.xml accessible
- [ ] Sitemap contient toutes les pages
- [ ] Robots.txt correct
- [ ] Favicon visible

### Indexabilité

- [ ] Pages indexables par Google
- [ ] Pas de balises noindex involontaires
- [ ] Pas de redirections cassées
- [ ] Pas d'erreurs 404

---

## 9.4 Tests de sécurité

### OWASP Top 10

- [ ] Protection injection SQL (via Doctrine)
- [ ] Protection XSS (Twig auto-escape)
- [ ] Protection CSRF sur formulaires
- [ ] Validation serveur des inputs
- [ ] Headers sécurité (X-Frame-Options, CSP)
- [ ] HTTPS activé
- [ ] Mots de passe hashés (si user system)

### Checklist Sécurité

- [ ] Formulaires protégés
- [ ] Upload fichiers sécurisé
- [ ] Pas de failles d'injection
- [ ] Backoffice sécurisé
- [ ] SSL/TLS configuré

---

## 9.5 Tests accessibilité

### WCAG 2.1

- [ ] Contrastes couleurs suffisants
- [ ] Navigation au clavier possible
- [ ] Liens descriptifs
- [ ] Alt text pertinents
- [ ] Labels formulaires présents
- [ ] Focus visible

### Outils

- [ ] WAVE Accessibility Tool
- [ ] Lighthouse Accessibility score > 90
- [ ] Tests navigation clavier

---

## ✅ Checklist finale Phase 9

### Fonctionnel
- [ ] Navigation complète testée
- [ ] Formulaire contact OK
- [ ] Backoffice EasyAdmin OK

### Technique
- [ ] Tous navigateurs OK
- [ ] Responsive complet
- [ ] Performance optimale

### SEO
- [ ] On-Page validé
- [ ] Schema.org validé
- [ ] Fichiers techniques OK
- [ ] Indexabilité vérifiée

### Sécurité & Accessibilité
- [ ] Protection OWASP OK
- [ ] Accessibilité WCAG OK

---

## 🚀 Prochaine étape

Passer à la [Phase 10 : Mise en production](10-mise-en-production.md)

---

*Document généré le 2025-11-18*
