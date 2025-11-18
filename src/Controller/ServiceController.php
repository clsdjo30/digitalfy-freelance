<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServiceController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function index(): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => [],
        ]);
    }

    #[Route('/services/{slug}', name: 'app_service_show')]
    public function show(string $slug): Response
    {
        return $this->render('service/show.html.twig', [
            'slug' => $slug,
        ]);
    }
}
