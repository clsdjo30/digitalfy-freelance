# Documentation des Tests - Phase 9

## Vue d'ensemble

Cette documentation décrit la suite de tests complète mise en place pour valider la qualité, la sécurité et l'accessibilité du site Digitalfy avant la mise en production.

## Table des matières

1. [Installation et Configuration](#installation-et-configuration)
2. [Structure des Tests](#structure-des-tests)
3. [Exécution des Tests](#exécution-des-tests)
4. [Types de Tests](#types-de-tests)
5. [Rapports et Résultats](#rapports-et-résultats)
6. [Maintenance](#maintenance)

---

## Installation et Configuration

### Prérequis

- PHP 8.2+
- Composer
- Base de données (PostgreSQL/MySQL)
- Extensions PHP : pdo, mbstring, xml

### Installation des dépendances

```bash
composer install
```

Les packages de test suivants sont installés automatiquement :
- PHPUnit 11.0
- Symfony Browser Kit
- Symfony Panther
- Zenstruck Foundry (fixtures)

### Configuration de la base de données de test

Créer un fichier `.env.test.local` avec les paramètres de votre base de données de test :

```env
DATABASE_URL="postgresql://user:password@localhost:5432/digitalfy_test?serverVersion=15&charset=utf8"
```

---

## Structure des Tests

Les tests sont organisés dans le dossier `tests/` :

```
tests/
├── bootstrap.php                    # Bootstrap des tests
├── Functional/                      # Tests fonctionnels
│   ├── NavigationTest.php          # Tests de navigation
│   ├── ContactFormTest.php         # Tests du formulaire
│   ├── AdminTest.php               # Tests du backoffice
│   ├── SEOTest.php                 # Tests SEO
│   ├── SecurityTest.php            # Tests de sécurité
│   └── AccessibilityTest.php       # Tests d'accessibilité
```

---

## Exécution des Tests

### Méthode 1 : Script automatisé (recommandé)

Le script `bin/run-tests.sh` exécute tous les tests et génère un rapport complet :

```bash
./bin/run-tests.sh
```

Ce script :
1. Vérifie l'environnement
2. Installe les dépendances si nécessaire
3. Crée/migre la base de données de test
4. Charge les fixtures
5. Exécute tous les tests
6. Génère un rapport détaillé

### Méthode 2 : Exécution manuelle

#### Exécuter tous les tests
```bash
php bin/phpunit
```

#### Exécuter une suite spécifique
```bash
php bin/phpunit tests/Functional/NavigationTest.php
php bin/phpunit tests/Functional/SEOTest.php
```

#### Exécuter un test spécifique
```bash
php bin/phpunit tests/Functional/NavigationTest.php --filter testHomePageLoads
```

#### Avec format testdox (lisible)
```bash
php bin/phpunit --testdox
```

#### Avec couverture de code
```bash
php bin/phpunit --coverage-html var/coverage
```

---

## Types de Tests

### 1. Tests de Navigation (NavigationTest.php)

**Objectif** : Valider que toutes les pages et liens fonctionnent correctement

**Tests inclus** :
- ✅ Tous les liens du menu principal
- ✅ Chargement des pages principales (accueil, blog, contact, portfolio)
- ✅ Pagination du blog
- ✅ Pages de services
- ✅ Pages légales
- ✅ Boutons CTA
- ✅ Gestion des erreurs 404
- ✅ Redirections

**Commande** :
```bash
php bin/phpunit tests/Functional/NavigationTest.php
```

---

### 2. Tests du Formulaire de Contact (ContactFormTest.php)

**Objectif** : Valider le fonctionnement complet du formulaire de contact

**Tests inclus** :
- ✅ Chargement du formulaire
- ✅ Soumission avec données valides
- ✅ Validation côté serveur
- ✅ Champs requis
- ✅ Protection CSRF
- ✅ Messages d'erreur clairs
- ✅ Validation de l'email
- ✅ Sauvegarde en base de données
- ✅ Status correct des demandes

**Commande** :
```bash
php bin/phpunit tests/Functional/ContactFormTest.php
```

---

### 3. Tests du Backoffice (AdminTest.php)

**Objectif** : Valider l'accès et les fonctionnalités du backoffice EasyAdmin

**Tests inclus** :
- ✅ Redirection vers login si non authentifié
- ✅ Connexion admin
- ✅ Chargement du dashboard
- ✅ Liste des articles
- ✅ Liste des catégories
- ✅ Liste des projets
- ✅ Liste des demandes de contact
- ✅ Bouton "Voir le site"
- ✅ Déconnexion
- ✅ Protection par rôle ROLE_ADMIN
- ✅ Menu admin complet
- ✅ Mise à jour de la dernière connexion

**Commande** :
```bash
php bin/phpunit tests/Functional/AdminTest.php
```

---

### 4. Tests SEO (SEOTest.php)

**Objectif** : Valider la conformité SEO et l'optimisation pour les moteurs de recherche

**Tests inclus** :

#### On-Page SEO
- ✅ Meta title sur toutes les pages (max 60 caractères)
- ✅ Meta description (50-160 caractères)
- ✅ Un seul H1 par page
- ✅ Structure hiérarchique des titres
- ✅ Attributs alt sur les images
- ✅ URLs SEO-friendly
- ✅ Canonical tags
- ✅ Meta Open Graph

#### Données structurées
- ✅ Schema.org JSON-LD présent
- ✅ LocalBusiness sur l'accueil
- ✅ Validation du JSON

#### Fichiers techniques
- ✅ Sitemap.xml accessible
- ✅ Sitemap contient des URLs
- ✅ Robots.txt accessible
- ✅ Référence au sitemap dans robots.txt
- ✅ Favicon

#### Indexabilité
- ✅ Pas de balises noindex involontaires
- ✅ Liens descriptifs

**Commande** :
```bash
php bin/phpunit tests/Functional/SEOTest.php
```

---

### 5. Tests de Sécurité (SecurityTest.php)

**Objectif** : Valider la conformité avec les bonnes pratiques OWASP Top 10

**Tests inclus** :

#### Protection CSRF
- ✅ Token CSRF sur formulaire de contact
- ✅ Token CSRF sur formulaire de login
- ✅ Rejet des tokens invalides

#### Protection XSS
- ✅ Auto-escape de Twig activé
- ✅ Tentatives d'injection XSS bloquées
- ✅ Échappement des données

#### Protection Injection SQL
- ✅ Doctrine protège avec requêtes préparées
- ✅ Tentatives d'injection SQL bloquées

#### Authentification et Autorisation
- ✅ Backoffice protégé par authentification
- ✅ Vérification des rôles (ROLE_ADMIN)
- ✅ Mots de passe hashés (bcrypt/argon2)
- ✅ Pas d'énumération d'utilisateurs

#### Autres protections
- ✅ Headers de sécurité
- ✅ Validation serveur des inputs
- ✅ Protection path traversal
- ✅ Upload de fichiers sécurisé
- ✅ Sessions sécurisées
- ✅ Pas de redirection ouverte
- ✅ Erreurs ne révèlent pas d'infos sensibles

**Commande** :
```bash
php bin/phpunit tests/Functional/SecurityTest.php
```

---

### 6. Tests d'Accessibilité (AccessibilityTest.php)

**Objectif** : Valider la conformité WCAG 2.1 niveau AA

**Tests inclus** :

#### Formulaires
- ✅ Labels associés aux champs
- ✅ Champs requis marqués
- ✅ Messages d'erreur associés aux champs
- ✅ Autocomplete approprié

#### Images et Médias
- ✅ Attributs alt sur toutes les images
- ✅ Alternatives pour vidéos/médias

#### Navigation et Structure
- ✅ Navigation au clavier possible
- ✅ Pas de tabindex positif
- ✅ Un seul H1 par page
- ✅ Hiérarchie des titres logique
- ✅ Landmarks ARIA (main, nav, header, footer)

#### Sémantique
- ✅ Attribut lang sur <html>
- ✅ Listes correctement structurées
- ✅ Tableaux avec headers
- ✅ Liens descriptifs
- ✅ Boutons accessibles

#### Éléments interactifs
- ✅ Focus visible
- ✅ Navigation au clavier
- ✅ Rôles ARIA appropriés

**Commande** :
```bash
php bin/phpunit tests/Functional/AccessibilityTest.php
```

---

## Rapports et Résultats

### Rapports générés

Le script `bin/run-tests.sh` génère plusieurs rapports dans `var/test-reports/` :

1. **Rapport texte** : `test-report-YYYYMMDD_HHMMSS.txt`
   - Sortie complète de tous les tests
   - Détails des erreurs

2. **Rapport Markdown** : `PHASE9-TEST-REPORT.md`
   - Résumé des résultats
   - Taux de réussite
   - Liste des catégories testées

3. **Couverture de code** : `coverage/index.html` (si Xdebug installé)
   - Analyse de la couverture du code par les tests

### Lire les rapports

```bash
# Voir le dernier rapport
cat var/test-reports/PHASE9-TEST-REPORT.md

# Ouvrir la couverture de code dans le navigateur
firefox var/test-reports/coverage/index.html
```

---

## Checklist Phase 9

Utiliser cette checklist pour valider que tous les tests sont passés :

### ✅ Tests Fonctionnels
- [ ] Navigation complète testée
- [ ] Formulaire contact OK
- [ ] Backoffice EasyAdmin OK

### ✅ Tests Techniques
- [ ] Tous navigateurs OK (manuel)
- [ ] Responsive complet (manuel)
- [ ] Performance optimale (Lighthouse)

### ✅ Tests SEO
- [ ] On-Page validé
- [ ] Schema.org validé
- [ ] Fichiers techniques OK
- [ ] Indexabilité vérifiée

### ✅ Tests Sécurité & Accessibilité
- [ ] Protection OWASP OK
- [ ] Accessibilité WCAG OK

---

## Maintenance

### Ajouter de nouveaux tests

1. Créer un nouveau fichier dans `tests/Functional/`
2. Étendre `WebTestCase`
3. Ajouter les méthodes de test avec annotation `@test`
4. Exécuter les tests

**Exemple** :

```php
<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MyNewTest extends WebTestCase
{
    /**
     * @test
     */
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->request('GET', '/my-page');

        $this->assertResponseIsSuccessful();
    }
}
```

### Mettre à jour les tests

Lorsque vous modifiez le code de l'application :

1. Exécutez les tests pour vérifier qu'ils passent toujours
2. Si un test échoue, mettez-le à jour pour refléter les changements
3. Ajoutez de nouveaux tests pour les nouvelles fonctionnalités

### Débogage des tests

#### Afficher la sortie détaillée
```bash
php bin/phpunit --testdox --verbose
```

#### Afficher les erreurs
```bash
php bin/phpunit --debug
```

#### Tester en mode interactif
```bash
php bin/phpunit --stop-on-failure
```

---

## Tests manuels complémentaires

Certains aspects doivent être testés manuellement :

### Navigateurs (tests cross-browser)
- [ ] Chrome (dernière version)
- [ ] Firefox (dernière version)
- [ ] Safari (Mac/iOS)
- [ ] Edge

### Devices
- [ ] iPhone (différentes tailles)
- [ ] Android (différentes tailles)
- [ ] iPad / tablettes
- [ ] Desktop (1920x1080, 1366x768)

### Performance (PageSpeed Insights)
- [ ] Score > 90
- [ ] LCP < 2.5s
- [ ] FID < 100ms
- [ ] CLS < 0.1

### Outils d'accessibilité
- [ ] WAVE Accessibility Tool
- [ ] Lighthouse Accessibility score > 90
- [ ] Tests navigation clavier

---

## Ressources

### Documentation
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Symfony Testing](https://symfony.com/doc/current/testing.html)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [WCAG 2.1 Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

### Outils utiles
- [Google Lighthouse](https://developers.google.com/web/tools/lighthouse)
- [WAVE Web Accessibility Tool](https://wave.webaim.org/)
- [Google Rich Results Test](https://search.google.com/test/rich-results)
- [Google PageSpeed Insights](https://pagespeed.web.dev/)

---

## Support

Pour toute question ou problème avec les tests :

1. Consultez cette documentation
2. Vérifiez les logs dans `var/log/test.log`
3. Consultez les rapports de test dans `var/test-reports/`
4. Ouvrez une issue dans le projet

---

*Documentation générée pour la Phase 9 - Tests & QA*
*Dernière mise à jour : 2025-11-20*
