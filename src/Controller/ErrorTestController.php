<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de test pour prévisualiser les pages d'erreur
 *
 * IMPORTANT: Ce contrôleur est uniquement accessible en environnement de développement
 * pour permettre de tester l'affichage des pages d'erreur personnalisées.
 *
 * Routes disponibles:
 * - /test-errors : Index avec liens vers toutes les pages d'erreur
 * - /test-errors/403 : Page d'erreur 403 (Accès refusé)
 * - /test-errors/404 : Page d'erreur 404 (Page non trouvée)
 * - /test-errors/500 : Page d'erreur 500 (Erreur serveur)
 * - /test-errors/{code} : Page d'erreur générique pour tout autre code
 */
#[Route('/test-errors', condition: "env('APP_ENV') === 'dev'")]
class ErrorTestController extends AbstractController
{
    /**
     * Page d'index listant tous les codes d'erreur testables
     */
    #[Route('', name: 'app_test_errors_index')]
    public function index(): Response
    {
        return $this->render('test_errors/index.html.twig');
    }

    /**
     * Teste la page d'erreur 403 (Accès refusé)
     */
    #[Route('/403', name: 'app_test_error_403')]
    public function error403(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error403.html.twig')
            ->setStatusCode(403);
    }

    /**
     * Teste la page d'erreur 404 (Page non trouvée)
     */
    #[Route('/404', name: 'app_test_error_404')]
    public function error404(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig')
            ->setStatusCode(404);
    }

    /**
     * Teste la page d'erreur 500 (Erreur serveur)
     */
    #[Route('/500', name: 'app_test_error_500')]
    public function error500(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error500.html.twig')
            ->setStatusCode(500);
    }

    /**
     * Teste la page d'erreur générique avec un code personnalisé
     *
     * @param int $code Code d'erreur HTTP à tester
     */
    #[Route('/{code}', name: 'app_test_error_generic', requirements: ['code' => '\d+'])]
    public function errorGeneric(int $code): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
            'status_code' => $code,
            'status_text' => $this->getStatusText($code),
        ])->setStatusCode($code);
    }

    /**
     * Retourne le texte descriptif pour un code d'erreur HTTP
     */
    private function getStatusText(int $code): string
    {
        return match ($code) {
            400 => 'Mauvaise requête',
            401 => 'Non autorisé',
            402 => 'Paiement requis',
            405 => 'Méthode non autorisée',
            408 => 'Délai d\'attente dépassé',
            410 => 'Ressource supprimée',
            422 => 'Entité non traitable',
            429 => 'Trop de requêtes',
            502 => 'Mauvaise passerelle',
            503 => 'Service indisponible',
            504 => 'Délai d\'attente de la passerelle dépassé',
            default => 'Une erreur est survenue',
        };
    }
}
