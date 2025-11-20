# Tests - Digitalfy

## Quick Start

### Exécuter tous les tests

```bash
./bin/run-tests.sh
```

### Exécuter une suite spécifique

```bash
# Tests de navigation
php bin/phpunit tests/Functional/NavigationTest.php

# Tests du formulaire
php bin/phpunit tests/Functional/ContactFormTest.php

# Tests du backoffice
php bin/phpunit tests/Functional/AdminTest.php

# Tests SEO
php bin/phpunit tests/Functional/SEOTest.php

# Tests de sécurité
php bin/phpunit tests/Functional/SecurityTest.php

# Tests d'accessibilité
php bin/phpunit tests/Functional/AccessibilityTest.php
```

### Format lisible (testdox)

```bash
php bin/phpunit --testdox
```

## Structure

```
tests/
├── Functional/
│   ├── NavigationTest.php       # Tests de navigation et liens
│   ├── ContactFormTest.php      # Tests du formulaire de contact
│   ├── AdminTest.php            # Tests du backoffice EasyAdmin
│   ├── SEOTest.php              # Tests SEO (meta, schema, sitemap)
│   ├── SecurityTest.php         # Tests de sécurité OWASP
│   └── AccessibilityTest.php    # Tests d'accessibilité WCAG 2.1
├── bootstrap.php                # Bootstrap des tests
└── README.md                    # Ce fichier
```

## Couverture

Les tests couvrent les aspects suivants de la Phase 9 :

### ✅ Fonctionnel
- Navigation complète
- Formulaire de contact (validation, CSRF, persistance)
- Backoffice EasyAdmin (CRUD, authentification, autorisation)

### ✅ SEO
- Meta tags (title, description, Open Graph)
- Structure HTML (H1, hiérarchie)
- Données structurées Schema.org
- Fichiers techniques (sitemap, robots.txt)

### ✅ Sécurité
- CSRF, XSS, SQL Injection
- Authentification et autorisation
- Hashage des mots de passe
- Headers de sécurité

### ✅ Accessibilité
- Labels de formulaires
- Attributs alt sur images
- Navigation au clavier
- Structure ARIA
- Conformité WCAG 2.1

## Documentation complète

Voir [docs/TESTS.md](../docs/TESTS.md) pour la documentation détaillée.

## Rapports

Les rapports de test sont générés dans `var/test-reports/` :
- `PHASE9-TEST-REPORT.md` - Résumé en Markdown
- `test-report-*.txt` - Rapport détaillé
- `coverage/` - Couverture de code (si Xdebug installé)
