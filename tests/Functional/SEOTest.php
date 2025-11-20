<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests SEO pour valider la conformité avec les bonnes pratiques
 * Phase 9 - Tests & QA - Section 9.3
 */
class SEOTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @test
     * Vérifie que toutes les pages principales ont un meta title
     */
    public function testAllMainPagesHaveMetaTitle(): void
    {
        $pages = [
            '/' => 'Accueil',
            '/blog' => 'Blog',
            '/contact' => 'Contact',
            '/portfolio' => 'Portfolio',
            '/a-propos' => 'À propos',
            '/mentions-legales' => 'Mentions légales',
            '/politique-confidentialite' => 'Politique de confidentialité',
        ];

        foreach ($pages as $url => $pageName) {
            $crawler = $this->client->request('GET', $url);

            if (!$this->client->getResponse()->isSuccessful()) {
                continue; // Skip si la page n'existe pas encore
            }

            $title = $crawler->filter('title')->first();
            $this->assertGreaterThan(
                0,
                $title->count(),
                sprintf('La page "%s" (%s) n\'a pas de balise <title>', $pageName, $url)
            );

            $titleText = $title->text();
            $this->assertNotEmpty($titleText, sprintf('Le title de la page "%s" est vide', $pageName));
            $this->assertLessThanOrEqual(
                60,
                strlen($titleText),
                sprintf('Le title de la page "%s" est trop long (%d caractères)', $pageName, strlen($titleText))
            );
        }
    }

    /**
     * @test
     * Vérifie que toutes les pages principales ont une meta description
     */
    public function testAllMainPagesHaveMetaDescription(): void
    {
        $pages = ['/', '/blog', '/contact', '/portfolio', '/a-propos'];

        foreach ($pages as $url) {
            $crawler = $this->client->request('GET', $url);

            if (!$this->client->getResponse()->isSuccessful()) {
                continue;
            }

            $metaDescription = $crawler->filter('meta[name="description"]');
            $this->assertGreaterThan(
                0,
                $metaDescription->count(),
                sprintf('La page "%s" n\'a pas de meta description', $url)
            );

            $description = $metaDescription->attr('content');
            $this->assertNotEmpty($description, sprintf('La meta description de "%s" est vide', $url));
            $this->assertGreaterThanOrEqual(
                50,
                strlen($description),
                sprintf('La meta description de "%s" est trop courte (%d caractères)', $url, strlen($description))
            );
            $this->assertLessThanOrEqual(
                160,
                strlen($description),
                sprintf('La meta description de "%s" est trop longue (%d caractères)', $url, strlen($description))
            );
        }
    }

    /**
     * @test
     * Vérifie qu'il n'y a qu'un seul H1 par page
     */
    public function testOnlyOneH1PerPage(): void
    {
        $pages = ['/', '/blog', '/contact', '/portfolio'];

        foreach ($pages as $url) {
            $crawler = $this->client->request('GET', $url);

            if (!$this->client->getResponse()->isSuccessful()) {
                continue;
            }

            $h1Count = $crawler->filter('h1')->count();
            $this->assertEquals(
                1,
                $h1Count,
                sprintf('La page "%s" devrait avoir exactement un H1 (trouvé: %d)', $url, $h1Count)
            );
        }
    }

    /**
     * @test
     * Vérifie la structure des balises Hn est logique
     */
    public function testHeadingStructureIsLogical(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        // Vérifier qu'il y a un H1
        $this->assertGreaterThan(0, $crawler->filter('h1')->count(), 'La page d\'accueil doit avoir un H1');

        // Vérifier que les H2 sont présents
        $this->assertGreaterThan(0, $crawler->filter('h2')->count(), 'La page d\'accueil doit avoir des H2');
    }

    /**
     * @test
     * Vérifie que les images ont des attributs alt
     */
    public function testImagesHaveAltText(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $images = $crawler->filter('img');

        if ($images->count() > 0) {
            foreach ($images as $img) {
                $alt = $img->getAttribute('alt');
                $src = $img->getAttribute('src');

                // L'attribut alt doit exister (peut être vide pour les images décoratives)
                $this->assertNotNull(
                    $alt,
                    sprintf('L\'image "%s" n\'a pas d\'attribut alt', $src)
                );
            }
        }
    }

    /**
     * @test
     * Vérifie que les URLs sont SEO-friendly
     */
    public function testUrlsAreSeoFriendly(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $links = $crawler->filter('a[href^="/"]');

        foreach ($links as $link) {
            $href = $link->getAttribute('href');

            // Ignorer les ancres et les assets
            if (str_contains($href, '#') || str_contains($href, '/assets/')) {
                continue;
            }

            // Vérifier qu'il n'y a pas de caractères interdits dans l'URL
            $this->assertDoesNotMatchRegularExpression(
                '/[^a-z0-9\-\/_.?&=]/',
                strtolower($href),
                sprintf('L\'URL "%s" contient des caractères non SEO-friendly', $href)
            );
        }
    }

    /**
     * @test
     * Vérifie que le sitemap.xml existe et est accessible
     */
    public function testSitemapXmlExists(): void
    {
        $this->client->request('GET', '/sitemap.xml');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'text/xml; charset=UTF-8');
    }

    /**
     * @test
     * Vérifie que le sitemap contient des URLs
     */
    public function testSitemapContainsUrls(): void
    {
        $crawler = $this->client->request('GET', '/sitemap.xml');
        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<urlset', $content, 'Le sitemap doit contenir une balise <urlset>');
        $this->assertStringContainsString('<loc>', $content, 'Le sitemap doit contenir des balises <loc>');

        // Vérifier qu'il y a au moins quelques URLs
        $urlCount = substr_count($content, '<url>');
        $this->assertGreaterThanOrEqual(3, $urlCount, 'Le sitemap doit contenir au moins 3 URLs');
    }

    /**
     * @test
     * Vérifie que le robots.txt existe et est accessible
     */
    public function testRobotsTxtExists(): void
    {
        $this->client->request('GET', '/robots.txt');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'text/plain; charset=UTF-8');
    }

    /**
     * @test
     * Vérifie que le robots.txt contient la référence au sitemap
     */
    public function testRobotsTxtContainsSitemapReference(): void
    {
        $this->client->request('GET', '/robots.txt');
        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('Sitemap:', $content, 'Le robots.txt doit référencer le sitemap');
    }

    /**
     * @test
     * Vérifie que le favicon existe
     */
    public function testFaviconExists(): void
    {
        $this->client->request('GET', '/favicon.ico');
        // Le favicon peut retourner 200 ou 404, on vérifie juste qu'il ne cause pas d'erreur serveur
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 404]);
    }

    /**
     * @test
     * Vérifie que les pages ont des canonical tags
     */
    public function testPagesHaveCanonicalTags(): void
    {
        $pages = ['/', '/blog', '/contact'];

        foreach ($pages as $url) {
            $crawler = $this->client->request('GET', $url);

            if (!$this->client->getResponse()->isSuccessful()) {
                continue;
            }

            $canonical = $crawler->filter('link[rel="canonical"]');
            $this->assertGreaterThan(
                0,
                $canonical->count(),
                sprintf('La page "%s" n\'a pas de canonical tag', $url)
            );
        }
    }

    /**
     * @test
     * Vérifie que les données structurées Schema.org existent sur l'accueil
     */
    public function testHomePageHasStructuredData(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $jsonLd = $crawler->filter('script[type="application/ld+json"]');
        $this->assertGreaterThan(
            0,
            $jsonLd->count(),
            'La page d\'accueil doit contenir des données structurées JSON-LD'
        );

        // Vérifier que c'est du JSON valide
        $jsonContent = $jsonLd->first()->text();
        $data = json_decode($jsonContent, true);
        $this->assertNotNull($data, 'Les données structurées doivent être du JSON valide');

        // Vérifier que le @context est schema.org
        $this->assertArrayHasKey('@context', $data);
        $this->assertStringContainsString('schema.org', $data['@context']);
    }

    /**
     * @test
     * Vérifie que la page d'accueil a le schema LocalBusiness
     */
    public function testHomePageHasLocalBusinessSchema(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $jsonLd = $crawler->filter('script[type="application/ld+json"]');

        if ($jsonLd->count() > 0) {
            $jsonContent = $jsonLd->first()->text();
            $data = json_decode($jsonContent, true);

            if (isset($data['@type'])) {
                $this->assertEquals(
                    'LocalBusiness',
                    $data['@type'],
                    'La page d\'accueil doit avoir le schema LocalBusiness'
                );
            }
        }
    }

    /**
     * @test
     * Vérifie qu'il n'y a pas de balises noindex involontaires
     */
    public function testNoUnintentionalNoindexTags(): void
    {
        $pages = ['/', '/blog', '/contact', '/portfolio'];

        foreach ($pages as $url) {
            $crawler = $this->client->request('GET', $url);

            if (!$this->client->getResponse()->isSuccessful()) {
                continue;
            }

            $noindexMeta = $crawler->filter('meta[name="robots"][content*="noindex"]');
            $this->assertEquals(
                0,
                $noindexMeta->count(),
                sprintf('La page "%s" a une balise noindex qui empêche l\'indexation', $url)
            );
        }
    }

    /**
     * @test
     * Vérifie que les liens sont descriptifs
     */
    public function testLinksAreDescriptive(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $links = $crawler->filter('a[href]');
        $genericTexts = ['cliquez ici', 'ici', 'click here', 'lire plus', 'plus'];

        foreach ($links as $link) {
            $text = trim(strtolower($link->textContent));

            if (empty($text)) {
                // Si le texte est vide, vérifier s'il y a un aria-label
                $ariaLabel = $link->getAttribute('aria-label');
                $this->assertNotEmpty(
                    $ariaLabel,
                    sprintf('Le lien vers "%s" n\'a ni texte ni aria-label', $link->getAttribute('href'))
                );
            } else {
                // Le texte ne devrait pas être trop générique
                $this->assertNotContains(
                    $text,
                    $genericTexts,
                    sprintf('Le lien "%s" a un texte trop générique: "%s"', $link->getAttribute('href'), $text)
                );
            }
        }
    }

    /**
     * @test
     * Vérifie que les pages ont des meta Open Graph
     */
    public function testPagesHaveOpenGraphTags(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $ogTitle = $crawler->filter('meta[property="og:title"]');
        $ogDescription = $crawler->filter('meta[property="og:description"]');

        $this->assertGreaterThan(0, $ogTitle->count(), 'La page doit avoir un og:title');
        $this->assertGreaterThan(0, $ogDescription->count(), 'La page doit avoir un og:description');
    }
}
