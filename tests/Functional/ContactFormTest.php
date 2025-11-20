<?php

namespace App\Tests\Functional;

use App\Entity\ContactRequest;
use App\Repository\ContactRequestRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels pour le formulaire de contact
 * Phase 9 - Tests & QA - Section 9.1 (Formulaire contact)
 */
class ContactFormTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @test
     * Vérifie que le formulaire de contact se charge correctement
     */
    public function testContactFormLoads(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        // Vérifier que tous les champs requis existent
        $this->assertSelectorExists('input[name="contact[name]"]', 'Le champ nom n\'existe pas');
        $this->assertSelectorExists('input[name="contact[email]"]', 'Le champ email n\'existe pas');
        $this->assertSelectorExists('select[name="contact[projectType]"]', 'Le champ type de projet n\'existe pas');
        $this->assertSelectorExists('textarea[name="contact[message]"]', 'Le champ message n\'existe pas');
    }

    /**
     * @test
     * Vérifie la soumission d'un formulaire valide
     */
    public function testContactFormSubmissionWithValidData(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Envoyer')->form([
            'contact[name]' => 'Jean Test',
            'contact[email]' => 'jean.test@example.com',
            'contact[phone]' => '0612345678',
            'contact[projectType]' => 'site-vitrine',
            'contact[estimatedBudget]' => '2000-5000',
            'contact[message]' => 'Ceci est un message de test pour valider le formulaire de contact.',
        ]);

        $this->client->submit($form);

        // Vérifier la redirection
        $this->assertResponseRedirects('/contact');
        $this->client->followRedirect();

        // Vérifier le message de succès
        $this->assertSelectorExists('.alert-success');
        $this->assertSelectorTextContains('.alert-success', 'bien été envoyé');

        // Vérifier que les données ont été sauvegardées en BDD
        $container = static::getContainer();
        $contactRequestRepo = $container->get(ContactRequestRepository::class);
        $savedRequest = $contactRequestRepo->findOneBy(['email' => 'jean.test@example.com']);

        $this->assertNotNull($savedRequest, 'La demande de contact n\'a pas été sauvegardée en BDD');
        $this->assertEquals('Jean Test', $savedRequest->getName());
        $this->assertEquals('site-vitrine', $savedRequest->getProjectType());
        $this->assertEquals('new', $savedRequest->getStatus());
    }

    /**
     * @test
     * Vérifie la validation côté serveur avec des données invalides
     */
    public function testContactFormValidationWithInvalidData(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        // Soumettre avec un email invalide
        $form = $crawler->selectButton('Envoyer')->form([
            'contact[name]' => 'Jean Test',
            'contact[email]' => 'email-invalide',
            'contact[projectType]' => 'site-vitrine',
            'contact[message]' => 'Message de test',
        ]);

        $this->client->submit($form);

        // La page devrait recharger avec des erreurs
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.invalid-feedback, .form-error-message');
    }

    /**
     * @test
     * Vérifie la validation des champs requis
     */
    public function testContactFormRequiredFields(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        // Soumettre sans les champs requis
        $form = $crawler->selectButton('Envoyer')->form([
            'contact[name]' => '',
            'contact[email]' => '',
            'contact[projectType]' => '',
            'contact[message]' => '',
        ]);

        $this->client->submit($form);

        // La page devrait recharger avec des erreurs
        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     * Vérifie la protection CSRF sur le formulaire
     */
    public function testContactFormCsrfProtection(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        // Vérifier que le token CSRF est présent
        $csrfToken = $crawler->filter('input[name="contact[_token]"]');
        $this->assertGreaterThan(0, $csrfToken->count(), 'Le token CSRF n\'est pas présent dans le formulaire');
    }

    /**
     * @test
     * Vérifie que les messages d'erreur sont clairs
     */
    public function testContactFormErrorMessagesAreClear(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        // Soumettre avec un email invalide
        $form = $crawler->selectButton('Envoyer')->form([
            'contact[name]' => 'Test',
            'contact[email]' => 'not-an-email',
            'contact[projectType]' => 'site-vitrine',
            'contact[message]' => 'Test message',
        ]);

        $crawler = $this->client->submit($form);

        // Vérifier qu'il y a un message d'erreur
        $errorMessages = $crawler->filter('.invalid-feedback, .form-error-message, .alert-danger');
        $this->assertGreaterThan(0, $errorMessages->count(), 'Aucun message d\'erreur n\'est affiché');
    }

    /**
     * @test
     * Vérifie que le téléphone est optionnel
     */
    public function testContactFormPhoneIsOptional(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        // Soumettre sans téléphone
        $form = $crawler->selectButton('Envoyer')->form([
            'contact[name]' => 'Jean Sans Telephone',
            'contact[email]' => 'jean.sanstelephone@example.com',
            'contact[projectType]' => 'site-vitrine',
            'contact[message]' => 'Je n\'ai pas de téléphone',
        ]);

        $this->client->submit($form);

        // Vérifier la redirection (succès)
        $this->assertResponseRedirects('/contact');
    }

    /**
     * @test
     * Vérifie que les choix de type de projet sont corrects
     */
    public function testContactFormProjectTypeChoices(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        $projectTypeSelect = $crawler->filter('select[name="contact[projectType]"]');
        $this->assertGreaterThan(0, $projectTypeSelect->count());

        $options = $projectTypeSelect->filter('option');
        $this->assertGreaterThanOrEqual(6, $options->count(), 'Le select doit avoir au moins 6 options');

        // Vérifier que les options attendues existent
        $optionsText = $options->each(fn($node) => $node->text());
        $this->assertContains('Site vitrine', $optionsText);
        $this->assertContains('Application mobile', $optionsText);
    }

    /**
     * @test
     * Vérifie la validation de l'email
     */
    public function testContactFormEmailValidation(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        $invalidEmails = [
            'invalide',
            '@example.com',
            'test@',
            'test..test@example.com',
        ];

        foreach ($invalidEmails as $invalidEmail) {
            $form = $crawler->selectButton('Envoyer')->form([
                'contact[name]' => 'Test Name',
                'contact[email]' => $invalidEmail,
                'contact[projectType]' => 'site-vitrine',
                'contact[message]' => 'Test message',
            ]);

            $this->client->submit($form);

            // Ne devrait pas rediriger (erreur de validation)
            $this->assertResponseIsSuccessful(
                sprintf('L\'email invalide "%s" a été accepté', $invalidEmail)
            );
        }
    }

    /**
     * @test
     * Vérifie que les données sont bien persistées avec le bon status
     */
    public function testContactFormDataPersistedWithCorrectStatus(): void
    {
        $crawler = $this->client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        $uniqueEmail = 'test.status.' . uniqid() . '@example.com';

        $form = $crawler->selectButton('Envoyer')->form([
            'contact[name]' => 'Test Status',
            'contact[email]' => $uniqueEmail,
            'contact[projectType]' => 'site-vitrine',
            'contact[message]' => 'Test du status',
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects();

        // Vérifier le status en BDD
        $container = static::getContainer();
        $contactRequestRepo = $container->get(ContactRequestRepository::class);
        $savedRequest = $contactRequestRepo->findOneBy(['email' => $uniqueEmail]);

        $this->assertNotNull($savedRequest);
        $this->assertEquals('new', $savedRequest->getStatus());
        $this->assertInstanceOf(\DateTimeInterface::class, $savedRequest->getSubmittedAt());
    }
}
