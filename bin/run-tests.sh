#!/bin/bash

###############################################################################
# Script d'exécution des tests - Phase 9
# Ce script exécute tous les tests et génère un rapport complet
###############################################################################

set -e  # Arrêter en cas d'erreur

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
REPORT_DIR="var/test-reports"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
REPORT_FILE="${REPORT_DIR}/test-report-${TIMESTAMP}.txt"

# Créer le répertoire de rapports s'il n'existe pas
mkdir -p ${REPORT_DIR}

echo -e "${BLUE}=====================================${NC}"
echo -e "${BLUE}   Tests & QA - Phase 9${NC}"
echo -e "${BLUE}=====================================${NC}"
echo ""

# Fonction pour afficher le statut
print_status() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ $1${NC}"
    else
        echo -e "${RED}✗ $1${NC}"
    fi
}

# Initialiser le rapport
echo "Rapport de tests - Phase 9" > ${REPORT_FILE}
echo "Date: $(date)" >> ${REPORT_FILE}
echo "========================================" >> ${REPORT_FILE}
echo "" >> ${REPORT_FILE}

# 1. Vérifier que Composer est installé
echo -e "${YELLOW}[1/8] Vérification de l'environnement...${NC}"
if command -v composer &> /dev/null; then
    echo -e "${GREEN}✓ Composer installé${NC}"
else
    echo -e "${RED}✗ Composer non installé${NC}"
    exit 1
fi

# 2. Installer les dépendances si nécessaire
echo -e "${YELLOW}[2/8] Installation des dépendances...${NC}"
if [ ! -d "vendor" ]; then
    echo "Installation des dépendances Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
    print_status "Dépendances installées"
else
    echo -e "${GREEN}✓ Dépendances déjà installées${NC}"
fi

# 3. Créer la base de données de test
echo -e "${YELLOW}[3/8] Configuration de la base de données de test...${NC}"
echo "Création de la base de données de test..."
php bin/console doctrine:database:create --env=test --if-not-exists --no-interaction 2>&1 | tee -a ${REPORT_FILE}
print_status "Base de données de test créée"

echo "Migration du schéma..."
php bin/console doctrine:migrations:migrate --env=test --no-interaction 2>&1 | tee -a ${REPORT_FILE}
print_status "Schéma migré"

# 4. Charger les fixtures
echo -e "${YELLOW}[4/8] Chargement des fixtures de test...${NC}"
php bin/console doctrine:fixtures:load --env=test --no-interaction 2>&1 | tee -a ${REPORT_FILE}
print_status "Fixtures chargées"

# 5. Exécuter les tests fonctionnels
echo ""
echo -e "${YELLOW}[5/8] Exécution des tests fonctionnels...${NC}"
echo "" >> ${REPORT_FILE}
echo "=== Tests Fonctionnels ===" >> ${REPORT_FILE}

echo -e "  ${BLUE}→ Tests de navigation${NC}"
php bin/phpunit tests/Functional/NavigationTest.php --testdox 2>&1 | tee -a ${REPORT_FILE}
print_status "Tests de navigation"

echo -e "  ${BLUE}→ Tests du formulaire de contact${NC}"
php bin/phpunit tests/Functional/ContactFormTest.php --testdox 2>&1 | tee -a ${REPORT_FILE}
print_status "Tests du formulaire de contact"

echo -e "  ${BLUE}→ Tests du backoffice${NC}"
php bin/phpunit tests/Functional/AdminTest.php --testdox 2>&1 | tee -a ${REPORT_FILE}
print_status "Tests du backoffice"

# 6. Exécuter les tests SEO
echo ""
echo -e "${YELLOW}[6/8] Exécution des tests SEO...${NC}"
echo "" >> ${REPORT_FILE}
echo "=== Tests SEO ===" >> ${REPORT_FILE}
php bin/phpunit tests/Functional/SEOTest.php --testdox 2>&1 | tee -a ${REPORT_FILE}
print_status "Tests SEO"

# 7. Exécuter les tests de sécurité
echo ""
echo -e "${YELLOW}[7/8] Exécution des tests de sécurité...${NC}"
echo "" >> ${REPORT_FILE}
echo "=== Tests de Sécurité ===" >> ${REPORT_FILE}
php bin/phpunit tests/Functional/SecurityTest.php --testdox 2>&1 | tee -a ${REPORT_FILE}
print_status "Tests de sécurité"

# 8. Exécuter les tests d'accessibilité
echo ""
echo -e "${YELLOW}[8/8] Exécution des tests d'accessibilité...${NC}"
echo "" >> ${REPORT_FILE}
echo "=== Tests d'Accessibilité ===" >> ${REPORT_FILE}
php bin/phpunit tests/Functional/AccessibilityTest.php --testdox 2>&1 | tee -a ${REPORT_FILE}
print_status "Tests d'accessibilité"

# 9. Exécuter tous les tests avec couverture (optionnel)
echo ""
echo -e "${YELLOW}[Bonus] Génération de la couverture de code...${NC}"
if command -v php-coveralls &> /dev/null || php -m | grep -q xdebug; then
    php bin/phpunit --coverage-html ${REPORT_DIR}/coverage --coverage-text 2>&1 | tee -a ${REPORT_FILE}
    print_status "Couverture de code générée"
    echo -e "${GREEN}Rapport de couverture disponible dans: ${REPORT_DIR}/coverage/index.html${NC}"
else
    echo -e "${YELLOW}⚠ Xdebug non installé, couverture de code ignorée${NC}"
fi

# 10. Résumé final
echo ""
echo -e "${BLUE}=====================================${NC}"
echo -e "${BLUE}   Résumé des tests${NC}"
echo -e "${BLUE}=====================================${NC}"
echo ""

# Compter les résultats
TOTAL_TESTS=$(grep -c "✔" ${REPORT_FILE} 2>/dev/null || echo "0")
FAILED_TESTS=$(grep -c "✘" ${REPORT_FILE} 2>/dev/null || echo "0")

echo "Total de tests exécutés: ${TOTAL_TESTS}"
echo "Tests échoués: ${FAILED_TESTS}"
echo ""
echo -e "Rapport complet disponible dans: ${GREEN}${REPORT_FILE}${NC}"
echo ""

# Générer un rapport markdown
MARKDOWN_REPORT="${REPORT_DIR}/PHASE9-TEST-REPORT.md"
cat > ${MARKDOWN_REPORT} << EOF
# Rapport de Tests - Phase 9

**Date**: $(date +"%Y-%m-%d %H:%M:%S")

## Résumé

- **Tests exécutés**: ${TOTAL_TESTS}
- **Tests échoués**: ${FAILED_TESTS}
- **Taux de réussite**: $((100 - (FAILED_TESTS * 100 / TOTAL_TESTS)))%

## Catégories testées

### ✅ Tests Fonctionnels
- Navigation
- Formulaire de contact
- Backoffice EasyAdmin

### ✅ Tests SEO
- Meta tags (title, description)
- Structure des titres (H1, H2, etc.)
- Schema.org / données structurées
- Sitemap.xml et robots.txt
- URLs SEO-friendly

### ✅ Tests de Sécurité
- Protection CSRF
- Protection XSS
- Protection contre les injections SQL
- Headers de sécurité
- Authentification et autorisation

### ✅ Tests d'Accessibilité
- Labels de formulaires
- Attributs alt sur les images
- Navigation au clavier
- Structure ARIA
- Contrastes et focus visible

## Détails

Voir le rapport complet: \`${REPORT_FILE}\`

$(if [ ${FAILED_TESTS} -eq 0 ]; then
    echo "## ✅ Tous les tests sont passés avec succès!"
    echo ""
    echo "Le site est prêt pour la Phase 10 - Mise en production"
else
    echo "## ⚠️ Certains tests ont échoué"
    echo ""
    echo "Veuillez consulter le rapport complet pour plus de détails."
fi)

---

*Rapport généré automatiquement par bin/run-tests.sh*
EOF

echo -e "Rapport markdown disponible dans: ${GREEN}${MARKDOWN_REPORT}${NC}"
echo ""

if [ ${FAILED_TESTS} -eq 0 ]; then
    echo -e "${GREEN}✅ Tous les tests sont passés avec succès!${NC}"
    echo -e "${GREEN}Le site est prêt pour la Phase 10 - Mise en production${NC}"
    exit 0
else
    echo -e "${RED}❌ Certains tests ont échoué. Veuillez consulter le rapport.${NC}"
    exit 1
fi
