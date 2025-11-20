#!/bin/bash

###############################################################################
# Script de test simple - Vérifie la base de données avant de lancer les tests
###############################################################################

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}=====================================${NC}"
echo -e "${BLUE}   Vérification de l'environnement${NC}"
echo -e "${BLUE}=====================================${NC}"
echo ""

# Fonction pour tester la connexion MySQL
test_mysql_connection() {
    echo -e "${YELLOW}Test de connexion à MySQL...${NC}"

    # Tester avec localhost
    php -r "
    try {
        \$pdo = new PDO('mysql:host=localhost', 'digitalfy', 'digitalfy_password');
        echo '✓ Connexion MySQL OK (localhost)\n';
        exit(0);
    } catch (Exception \$e) {
        try {
            \$pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'digitalfy', 'digitalfy_password');
            echo '✓ Connexion MySQL OK (127.0.0.1:3306)\n';
            exit(0);
        } catch (Exception \$e2) {
            echo '✗ Erreur de connexion MySQL\n';
            echo 'Message: ' . \$e2->getMessage() . '\n';
            exit(1);
        }
    }
    " 2>&1

    return $?
}

# Test de connexion
if ! test_mysql_connection; then
    echo ""
    echo -e "${RED}========================================${NC}"
    echo -e "${RED}   MySQL n'est pas accessible !${NC}"
    echo -e "${RED}========================================${NC}"
    echo ""
    echo -e "${YELLOW}Solutions possibles :${NC}"
    echo ""
    echo "1. Démarrer XAMPP :"
    echo "   - Linux: sudo /opt/lampp/lampp start"
    echo "   - Windows: Ouvrir XAMPP Control Panel et démarrer MySQL"
    echo "   - macOS: sudo /Applications/XAMPP/xamppfiles/xampp start"
    echo ""
    echo "2. Démarrer MySQL système :"
    echo "   sudo systemctl start mysql"
    echo "   # ou"
    echo "   sudo service mysql start"
    echo ""
    echo "3. Vérifier que MySQL écoute sur le bon port :"
    echo "   sudo netstat -tuln | grep 3306"
    echo ""
    echo "4. Vérifier les identifiants dans .env.test.local"
    echo ""
    exit 1
fi

echo ""
echo -e "${GREEN}✓ MySQL est accessible${NC}"
echo ""

# Vérifier que la base de données de test existe
echo -e "${YELLOW}Vérification de la base de données de test...${NC}"

DB_EXISTS=$(php -r "
try {
    \$pdo = new PDO('mysql:host=localhost', 'digitalfy', 'digitalfy_password');
    \$result = \$pdo->query(\"SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'digitalfy_test'\");
    echo \$result ? '1' : '0';
} catch (Exception \$e) {
    echo '0';
}
" 2>&1)

if [ "$DB_EXISTS" != "1" ]; then
    echo -e "${YELLOW}Base de données 'digitalfy_test' non trouvée. Création...${NC}"
    php bin/console doctrine:database:create --env=test --if-not-exists
    php bin/console doctrine:migrations:migrate --env=test --no-interaction
    php bin/console doctrine:fixtures:load --env=test --no-interaction
    echo -e "${GREEN}✓ Base de données de test créée${NC}"
else
    echo -e "${GREEN}✓ Base de données de test existe${NC}"
fi

echo ""
echo -e "${BLUE}=====================================${NC}"
echo -e "${BLUE}   Exécution des tests${NC}"
echo -e "${BLUE}=====================================${NC}"
echo ""

# Demander quel test exécuter
echo "Choisissez les tests à exécuter :"
echo "1) Tous les tests"
echo "2) Tests de navigation uniquement"
echo "3) Tests du formulaire de contact"
echo "4) Tests du backoffice admin"
echo "5) Tests SEO"
echo "6) Tests de sécurité"
echo "7) Tests d'accessibilité"
echo ""
read -p "Votre choix (1-7) [1]: " choice
choice=${choice:-1}

case $choice in
    1)
        echo -e "${BLUE}Exécution de tous les tests...${NC}"
        php bin/phpunit --testdox
        ;;
    2)
        echo -e "${BLUE}Tests de navigation...${NC}"
        php bin/phpunit tests/Functional/NavigationTest.php --testdox
        ;;
    3)
        echo -e "${BLUE}Tests du formulaire de contact...${NC}"
        php bin/phpunit tests/Functional/ContactFormTest.php --testdox
        ;;
    4)
        echo -e "${BLUE}Tests du backoffice admin...${NC}"
        php bin/phpunit tests/Functional/AdminTest.php --testdox
        ;;
    5)
        echo -e "${BLUE}Tests SEO...${NC}"
        php bin/phpunit tests/Functional/SEOTest.php --testdox
        ;;
    6)
        echo -e "${BLUE}Tests de sécurité...${NC}"
        php bin/phpunit tests/Functional/SecurityTest.php --testdox
        ;;
    7)
        echo -e "${BLUE}Tests d'accessibilité...${NC}"
        php bin/phpunit tests/Functional/AccessibilityTest.php --testdox
        ;;
    *)
        echo -e "${RED}Choix invalide${NC}"
        exit 1
        ;;
esac

echo ""
echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}   Tests terminés${NC}"
echo -e "${GREEN}=====================================${NC}"
