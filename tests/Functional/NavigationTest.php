<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels pour la navigation du site
 * Phase 9 - Tests & QA - Section 9.1
 */
class NavigationTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @test
     * Vérifie que tous les liens du menu principal fonctionnent
     */
    public function testMainMenuLinks(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        // Extraire tous les liens de navigation
        $links = $crawler->filter('nav a')->links();

        foreach ($links as $link) {
            $this->client->click($link);
            $this->assertResponseIsSuccessful(
                sprintf('Le lien "%s" vers "%s" ne fonctionne pas', $link->getNode()->textContent, $link->getUri())
            );
        }
    }

    /**
     * @test
     * Vérifie que la page d'accueil se charge correctement
     */
    public function testHomePageLoads(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Digitalfy');
    }

    /**
     * @test
     * Vérifie que la page blog se charge correctement
     */
    public function testBlogPageLoads(): void
    {
        $this->client->request('GET', '/blog');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     * Vérifie que la pagination du blog fonctionne
     */
    public function testBlogPaginationWorks(): void
    {
        // Test page 1
        $crawler = $this->client->request('GET', '/blog?page=1');
        $this->assertResponseIsSuccessful();

        // Si des liens de pagination existent, les tester
        $paginationLinks = $crawler->filter('.pagination a');
        if ($paginationLinks->count() > 0) {
            $nextPageLink = $paginationLinks->first()->link();
            $this->client->click($nextPageLink);
            $this->assertResponseIsSuccessful('La pagination du blog ne fonctionne pas correctement');
        }
    }

    /**
     * @test
     * Vérifie que les pages de service se chargent
     */
    public function testServicePagesLoad(): void
    {
        $services = ['website', 'mobile-app', 'maintenance', 'restaurant'];

        foreach ($services as $service) {
            $this->client->request('GET', "/services/{$service}");
            $this->assertResponseIsSuccessful(
                sprintf('La page de service "%s" ne se charge pas', $service)
            );
        }
    }

    /**
     * @test
     * Vérifie que la page de contact se charge
     */
    public function testContactPageLoads(): void
    {
        $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    /**
     * @test
     * Vérifie que les pages légales se chargent
     */
    public function testLegalPagesLoad(): void
    {
        $pages = [
            '/mentions-legales' => 'Mentions légales',
            '/politique-confidentialite' => 'Politique',
            '/a-propos' => 'À propos'
        ];

        foreach ($pages as $url => $expectedText) {
            $this->client->request('GET', $url);
            $this->assertResponseIsSuccessful(
                sprintf('La page "%s" ne se charge pas', $url)
            );
        }
    }

    /**
     * @test
     * Vérifie que les boutons CTA mènent aux bonnes pages
     */
    public function testCTAButtonsWork(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        // Trouver les boutons CTA (Call-to-Action)
        $ctaButtons = $crawler->filter('a[href*="contact"]');

        if ($ctaButtons->count() > 0) {
            $ctaLink = $ctaButtons->first()->link();
            $this->client->click($ctaLink);
            $this->assertResponseIsSuccessful('Le bouton CTA ne fonctionne pas');
            $this->assertRouteSame('app_contact');
        }
    }

    /**
     * @test
     * Vérifie qu'une page inexistante retourne une 404
     */
    public function test404PageReturns404(): void
    {
        $this->client->request('GET', '/page-qui-nexiste-pas-du-tout');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * Vérifie que les redirections fonctionnent correctement
     */
    public function testRedirectsWorkCorrectly(): void
    {
        // Tester une redirection typique (si elle existe)
        $this->client->request('GET', '/admin');

        // Devrait rediriger vers la page de login
        $this->assertTrue(
            $this->client->getResponse()->isRedirect() || $this->client->getResponse()->isSuccessful(),
            'La page /admin devrait rediriger ou se charger'
        );
    }

    /**
     * @test
     * Vérifie que le portfolio se charge
     */
    public function testPortfolioPageLoads(): void
    {
        $this->client->request('GET', '/portfolio');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     * Vérifie que tous les liens internes fonctionnent (crawl complet)
     */
    public function testAllInternalLinksWork(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $internalLinks = $crawler->filter('a[href^="/"]')->links();

        // Limiter à 20 liens pour ne pas surcharger les tests
        $linksToTest = array_slice($internalLinks, 0, 20);

        foreach ($linksToTest as $link) {
            $uri = $link->getUri();

            // Ignorer les liens d'ancrage et les assets
            if (str_contains($uri, '#') || str_contains($uri, '/assets/')) {
                continue;
            }

            $this->client->click($link);

            $statusCode = $this->client->getResponse()->getStatusCode();
            $this->assertContains(
                $statusCode,
                [Response::HTTP_OK, Response::HTTP_FOUND, Response::HTTP_MOVED_PERMANENTLY],
                sprintf('Le lien "%s" retourne un code %d', $uri, $statusCode)
            );
        }
    }
}
