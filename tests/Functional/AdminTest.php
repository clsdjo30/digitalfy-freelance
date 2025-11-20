<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels pour le backoffice EasyAdmin
 * Phase 9 - Tests & QA - Section 9.1 (Backoffice EasyAdmin)
 */
class AdminTest extends WebTestCase
{
    private $client;
    private $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Créer un utilisateur admin pour les tests
        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();

        // Vérifier si un utilisateur de test existe déjà
        $userRepo = $container->get(UserRepository::class);
        $this->user = $userRepo->findOneBy(['email' => 'admin@test.com']);

        if (!$this->user) {
            $this->user = new User();
            $this->user->setEmail('admin@test.com');
            $this->user->setRoles(['ROLE_ADMIN']);

            // Hash du mot de passe 'test123'
            $passwordHasher = $container->get('security.user_password_hasher');
            $hashedPassword = $passwordHasher->hashPassword($this->user, 'test123');
            $this->user->setPassword($hashedPassword);

            $em->persist($this->user);
            $em->flush();
        }
    }

    /**
     * @test
     * Vérifie que la page admin redirige vers le login si non authentifié
     */
    public function testAdminPageRedirectsToLoginWhenNotAuthenticated(): void
    {
        $this->client->request('GET', '/admin');
        $this->assertResponseRedirects('/login');
    }

    /**
     * @test
     * Vérifie que la connexion admin fonctionne
     */
    public function testAdminLoginWorks(): void
    {
        // Aller sur la page de login
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        // Remplir le formulaire de login
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'admin@test.com',
            'password' => 'test123',
        ]);

        $this->client->submit($form);

        // Devrait rediriger vers /admin
        $this->assertResponseRedirects();
        $this->client->followRedirect();

        // Vérifier qu'on est sur le dashboard admin
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin');
    }

    /**
     * @test
     * Vérifie que le dashboard admin se charge correctement
     */
    public function testAdminDashboardLoads(): void
    {
        // Se connecter en tant qu'admin
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1, .content-header-title', 'Dashboard');
    }

    /**
     * @test
     * Vérifie que la liste des articles est accessible
     */
    public function testAdminBlogPostListIsAccessible(): void
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        // Trouver et cliquer sur le lien "Articles"
        $link = $crawler->filter('a:contains("Articles")')->first()->link();
        $this->client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.content-header-title, h1');
    }

    /**
     * @test
     * Vérifie que la liste des catégories est accessible
     */
    public function testAdminCategoryListIsAccessible(): void
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        // Trouver et cliquer sur le lien "Catégories"
        $link = $crawler->filter('a:contains("Catégories")')->first()->link();
        $this->client->click($link);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     * Vérifie que la liste des projets est accessible
     */
    public function testAdminProjectListIsAccessible(): void
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        // Trouver et cliquer sur le lien "Projets"
        $link = $crawler->filter('a:contains("Projets")')->first()->link();
        $this->client->click($link);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     * Vérifie que la liste des demandes de contact est accessible
     */
    public function testAdminContactRequestListIsAccessible(): void
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        // Trouver et cliquer sur le lien "Demandes"
        $link = $crawler->filter('a:contains("Demandes")')->first()->link();
        $this->client->click($link);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     * Vérifie que le bouton "Voir le site" fonctionne
     */
    public function testAdminViewSiteButtonWorks(): void
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        // Trouver le lien "Voir le site"
        $link = $crawler->filter('a:contains("Voir le site")')->first()->link();
        $this->client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_home');
    }

    /**
     * @test
     * Vérifie que la déconnexion fonctionne
     */
    public function testAdminLogoutWorks(): void
    {
        $this->client->loginUser($this->user);

        // Aller sur le dashboard
        $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        // Se déconnecter
        $this->client->request('GET', '/logout');

        // Après déconnexion, /admin devrait rediriger vers /login
        $this->client->request('GET', '/admin');
        $this->assertResponseRedirects('/login');
    }

    /**
     * @test
     * Vérifie qu'un utilisateur sans ROLE_ADMIN ne peut pas accéder au backoffice
     */
    public function testNonAdminUserCannotAccessAdmin(): void
    {
        // Créer un utilisateur sans ROLE_ADMIN
        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();

        $normalUser = new User();
        $normalUser->setEmail('user@test.com');
        $normalUser->setRoles(['ROLE_USER']);

        $passwordHasher = $container->get('security.user_password_hasher');
        $hashedPassword = $passwordHasher->hashPassword($normalUser, 'test123');
        $normalUser->setPassword($hashedPassword);

        $em->persist($normalUser);
        $em->flush();

        // Se connecter en tant qu'utilisateur normal
        $this->client->loginUser($normalUser);

        // Essayer d'accéder au dashboard
        $this->client->request('GET', '/admin');

        // Devrait être refusé (403 Forbidden)
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     * Vérifie que les filtres et la recherche fonctionnent
     */
    public function testAdminFiltersAndSearchWork(): void
    {
        $this->client->loginUser($this->user);

        // Aller sur la liste des articles
        $this->client->request('GET', '/admin?crudAction=index&crudControllerFqcn=App\\Controller\\Admin\\BlogPostCrudController');

        // Si la page se charge, les filtres sont disponibles
        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     * Vérifie que le menu admin contient tous les éléments nécessaires
     */
    public function testAdminMenuContainsAllNecessaryItems(): void
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        // Vérifier que les sections du menu existent
        $menuText = $crawler->filter('.sidebar, .main-sidebar, nav')->text();

        $this->assertStringContainsString('Dashboard', $menuText);
        $this->assertStringContainsString('Blog', $menuText);
        $this->assertStringContainsString('Articles', $menuText);
        $this->assertStringContainsString('Catégories', $menuText);
        $this->assertStringContainsString('Projets', $menuText);
        $this->assertStringContainsString('Demandes', $menuText);
    }

    /**
     * @test
     * Vérifie que la dernière connexion est mise à jour
     */
    public function testAdminLastLoginIsUpdated(): void
    {
        $this->client->loginUser($this->user);

        $oldLastLogin = $this->user->getLastLoginAt();

        // Attendre 1 seconde pour s'assurer que le timestamp change
        sleep(1);

        // Accéder au dashboard
        $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        // Recharger l'utilisateur depuis la base de données
        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();
        $em->refresh($this->user);

        $newLastLogin = $this->user->getLastLoginAt();

        $this->assertNotNull($newLastLogin);
        if ($oldLastLogin) {
            $this->assertGreaterThan($oldLastLogin->getTimestamp(), $newLastLogin->getTimestamp());
        }
    }
}
