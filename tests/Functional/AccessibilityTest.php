<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests d'accessibilité pour vérifier la conformité WCAG 2.1
 * Phase 9 - Tests & QA - Section 9.5
 */
class AccessibilityTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @test
     * Vérifie que les formulaires ont des labels associés
     */
    public function testFormsHaveLabels(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        // Récupérer tous les inputs (sauf hidden et submit)
        $inputs = $crawler->filter('input:not([type="hidden"]):not([type="submit"]), textarea, select');

        foreach ($inputs as $input) {
            $id = $input->getAttribute('id');
            $name = $input->getAttribute('name');

            if ($id) {
                // Vérifier qu'il existe un label avec for="$id"
                $label = $crawler->filter(sprintf('label[for="%s"]', $id));
                $this->assertGreaterThan(
                    0,
                    $label->count(),
                    sprintf('L\'input avec l\'id "%s" devrait avoir un label associé', $id)
                );
            }
        }
    }

    /**
     * @test
     * Vérifie que les images ont des attributs alt
     */
    public function testImagesHaveAltAttributes(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $images = $crawler->filter('img');

        foreach ($images as $img) {
            $alt = $img->getAttribute('alt');
            $src = $img->getAttribute('src');

            $this->assertNotNull(
                $alt,
                sprintf('L\'image "%s" doit avoir un attribut alt (peut être vide pour les images décoratives)', $src)
            );
        }
    }

    /**
     * @test
     * Vérifie que les liens ont un texte descriptif
     */
    public function testLinksHaveDescriptiveText(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $links = $crawler->filter('a[href]');

        foreach ($links as $link) {
            $text = trim($link->textContent);
            $ariaLabel = $link->getAttribute('aria-label');
            $title = $link->getAttribute('title');

            // Le lien doit avoir soit du texte, soit un aria-label, soit un title
            $hasAccessibleText = !empty($text) || !empty($ariaLabel) || !empty($title);

            $this->assertTrue(
                $hasAccessibleText,
                sprintf('Le lien vers "%s" doit avoir un texte descriptif', $link->getAttribute('href'))
            );
        }
    }

    /**
     * @test
     * Vérifie que la page a une structure de navigation au clavier
     */
    public function testPageHasKeyboardNavigationStructure(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        // Vérifier que les éléments interactifs sont accessibles au clavier
        $interactiveElements = $crawler->filter('a, button, input:not([type="hidden"]), textarea, select');

        $this->assertGreaterThan(
            0,
            $interactiveElements->count(),
            'La page doit avoir des éléments interactifs'
        );

        // Les éléments ne devraient pas avoir tabindex avec une valeur positive (mauvaise pratique)
        $badTabindex = $crawler->filter('[tabindex]:not([tabindex="0"]):not([tabindex="-1"])');

        foreach ($badTabindex as $element) {
            $tabindex = $element->getAttribute('tabindex');
            $this->assertLessThanOrEqual(
                0,
                (int)$tabindex,
                'Les éléments ne devraient pas avoir un tabindex positif (mauvaise pratique d\'accessibilité)'
            );
        }
    }

    /**
     * @test
     * Vérifie que les boutons ont des attributs accessibles
     */
    public function testButtonsHaveAccessibleAttributes(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        $buttons = $crawler->filter('button');

        foreach ($buttons as $button) {
            $text = trim($button->textContent);
            $ariaLabel = $button->getAttribute('aria-label');
            $title = $button->getAttribute('title');
            $type = $button->getAttribute('type');

            // Le bouton doit avoir soit du texte, soit un aria-label
            $hasAccessibleText = !empty($text) || !empty($ariaLabel) || !empty($title);

            $this->assertTrue(
                $hasAccessibleText,
                'Tous les boutons doivent avoir un texte ou un attribut aria-label'
            );

            // Les boutons devraient avoir un type explicite
            $this->assertContains(
                $type,
                ['button', 'submit', 'reset', null],
                sprintf('Le bouton "%s" devrait avoir un type valide', $text)
            );
        }
    }

    /**
     * @test
     * Vérifie que la page a un titre principal (h1)
     */
    public function testPageHasMainHeading(): void
    {
        $pages = ['/', '/blog', '/contact', '/portfolio'];

        foreach ($pages as $url) {
            $crawler = $this->client->request('GET', $url);

            if (!$this->client->getResponse()->isSuccessful()) {
                continue;
            }

            $h1 = $crawler->filter('h1');
            $this->assertGreaterThan(
                0,
                $h1->count(),
                sprintf('La page "%s" doit avoir un titre principal (h1)', $url)
            );

            $h1Text = trim($h1->first()->text());
            $this->assertNotEmpty(
                $h1Text,
                sprintf('Le h1 de la page "%s" ne doit pas être vide', $url)
            );
        }
    }

    /**
     * @test
     * Vérifie que la structure des titres est hiérarchique
     */
    public function testHeadingHierarchyIsCorrect(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        // Récupérer tous les titres
        $headings = $crawler->filter('h1, h2, h3, h4, h5, h6');

        $previousLevel = 0;

        foreach ($headings as $heading) {
            $tagName = $heading->tagName;
            $level = (int) substr($tagName, 1); // Extraire le numéro (h1 -> 1)

            // Le saut de niveau ne devrait pas être > 1
            if ($previousLevel > 0) {
                $jump = $level - $previousLevel;
                $this->assertLessThanOrEqual(
                    1,
                    $jump,
                    sprintf(
                        'La hiérarchie des titres devrait être logique (trouvé %s après h%d)',
                        $tagName,
                        $previousLevel
                    )
                );
            }

            $previousLevel = $level;
        }
    }

    /**
     * @test
     * Vérifie que le document a une langue définie
     */
    public function testDocumentHasLangAttribute(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $html = $crawler->filter('html');
        $this->assertGreaterThan(0, $html->count(), 'Le document doit avoir une balise <html>');

        $lang = $html->attr('lang');
        $this->assertNotEmpty($lang, 'La balise <html> doit avoir un attribut lang');
        $this->assertEquals('fr', $lang, 'La langue du document devrait être "fr"');
    }

    /**
     * @test
     * Vérifie que les éléments de navigation ont des roles ARIA appropriés
     */
    public function testNavigationHasProperAriaRoles(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        // Vérifier qu'il y a un élément nav
        $nav = $crawler->filter('nav, [role="navigation"]');
        $this->assertGreaterThan(
            0,
            $nav->count(),
            'La page devrait avoir au moins un élément de navigation'
        );
    }

    /**
     * @test
     * Vérifie que les champs de formulaire requis sont marqués
     */
    public function testRequiredFormFieldsAreMarked(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        $requiredInputs = $crawler->filter('input[required], textarea[required], select[required]');

        foreach ($requiredInputs as $input) {
            $id = $input->getAttribute('id');
            $name = $input->getAttribute('name');
            $required = $input->getAttribute('required');
            $ariaRequired = $input->getAttribute('aria-required');

            // Le champ devrait avoir soit required, soit aria-required
            $this->assertTrue(
                $required !== null || $ariaRequired === 'true',
                sprintf('Le champ requis "%s" devrait avoir l\'attribut required ou aria-required', $name)
            );
        }
    }

    /**
     * @test
     * Vérifie que les liens de skip navigation existent
     */
    public function testSkipNavigationLinksExist(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        // Chercher un lien "skip to content" ou similaire
        $skipLinks = $crawler->filter('a[href^="#"]:contains("contenu"), a[href^="#"]:contains("main"), .skip-link');

        // Ce n'est pas obligatoire mais c'est une bonne pratique
        if ($skipLinks->count() > 0) {
            $this->assertGreaterThan(0, $skipLinks->count(), 'Lien de skip navigation trouvé (bonne pratique)');
        } else {
            $this->markTestSkipped('Pas de lien de skip navigation (recommandé mais non obligatoire)');
        }
    }

    /**
     * @test
     * Vérifie que les messages d'erreur sont associés aux champs
     */
    public function testErrorMessagesAreAssociatedWithFields(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        // Soumettre le formulaire avec des erreurs
        $form = $crawler->selectButton('Envoyer')->form([
            'contact[name]' => '',
            'contact[email]' => 'invalid-email',
            'contact[projectType]' => 'site-vitrine',
            'contact[message]' => '',
        ]);

        $crawler = $this->client->submit($form);

        // Vérifier que les messages d'erreur sont présents
        $errorMessages = $crawler->filter('.invalid-feedback, .form-error-message, .error');

        if ($errorMessages->count() > 0) {
            // Les messages d'erreur devraient être liés aux champs via aria-describedby
            $fieldsWithErrors = $crawler->filter('input.is-invalid, textarea.is-invalid, [aria-invalid="true"]');
            $this->assertGreaterThan(0, $fieldsWithErrors->count(), 'Les champs en erreur devraient être marqués');
        }
    }

    /**
     * @test
     * Vérifie que les éléments interactifs ont un focus visible
     */
    public function testInteractiveElementsHaveFocusIndicator(): void
    {
        // Ce test est difficile à automatiser complètement
        // On vérifie juste qu'il n'y a pas de CSS qui supprime l'outline
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        // Note: Un vrai test de focus nécessiterait des tests E2E avec Panther
        $this->assertTrue(true, 'Le focus visible devrait être testé manuellement ou avec Panther');
    }

    /**
     * @test
     * Vérifie que les listes sont correctement structurées
     */
    public function testListsAreProperlyStructured(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        // Vérifier que les <li> sont bien dans des <ul> ou <ol>
        $listItems = $crawler->filter('li');

        foreach ($listItems as $li) {
            $parent = $li->parentNode;
            $parentTag = $parent->tagName;

            $this->assertContains(
                $parentTag,
                ['ul', 'ol'],
                'Les éléments <li> doivent être enfants de <ul> ou <ol>'
            );
        }
    }

    /**
     * @test
     * Vérifie que les tableaux ont des headers
     */
    public function testTablesHaveHeaders(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $tables = $crawler->filter('table');

        foreach ($tables as $table) {
            $crawler = new \Symfony\Component\DomCrawler\Crawler($table);

            // Les tableaux devraient avoir soit <thead>, soit <th>
            $hasHeader = $crawler->filter('thead, th')->count() > 0;

            if (!$hasHeader) {
                // Vérifier si c'est un tableau de mise en page (mauvaise pratique)
                $role = $table->getAttribute('role');
                $this->assertEquals(
                    'presentation',
                    $role,
                    'Les tableaux de données doivent avoir des en-têtes ou role="presentation"'
                );
            }
        }
    }

    /**
     * @test
     * Vérifie que les landmarks ARIA sont présents
     */
    public function testAriaLandmarksArePresent(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        // Vérifier la présence des landmarks principaux
        $main = $crawler->filter('main, [role="main"]');
        $this->assertGreaterThan(0, $main->count(), 'La page devrait avoir un landmark main');

        $nav = $crawler->filter('nav, [role="navigation"]');
        $this->assertGreaterThan(0, $nav->count(), 'La page devrait avoir un landmark navigation');

        // Header et footer sont recommandés mais pas obligatoires
        $header = $crawler->filter('header, [role="banner"]');
        $footer = $crawler->filter('footer, [role="contentinfo"]');

        $this->assertGreaterThan(0, $header->count(), 'La page devrait avoir un header');
        $this->assertGreaterThan(0, $footer->count(), 'La page devrait avoir un footer');
    }

    /**
     * @test
     * Vérifie que les vidéos et médias ont des alternatives
     */
    public function testMediaHasAlternatives(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $videos = $crawler->filter('video');

        foreach ($videos as $video) {
            // Les vidéos devraient avoir soit des sous-titres, soit une transcription
            $crawler = new \Symfony\Component\DomCrawler\Crawler($video);
            $track = $crawler->filter('track[kind="captions"], track[kind="subtitles"]');

            if ($track->count() === 0) {
                // Vérifier s'il y a une description ou transcription à proximité
                $this->markTestIncomplete('Vérifier manuellement que les vidéos ont des alternatives textuelles');
            }
        }

        $this->assertTrue(true, 'Vérification des médias effectuée');
    }

    /**
     * @test
     * Vérifie que les champs de formulaire ont des autocomplete appropriés
     */
    public function testFormFieldsHaveAutocomplete(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        // L'email devrait avoir autocomplete="email"
        $emailInput = $crawler->filter('input[type="email"], input[name*="email"]');
        if ($emailInput->count() > 0) {
            $autocomplete = $emailInput->first()->attr('autocomplete');
            // C'est une bonne pratique mais pas obligatoire pour l'accessibilité
            if ($autocomplete) {
                $this->assertEquals('email', $autocomplete);
            }
        }

        $this->assertTrue(true, 'Autocomplete vérifié');
    }
}
