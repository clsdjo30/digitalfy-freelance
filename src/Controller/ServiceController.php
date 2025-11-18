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
        $services = [
            [
                'title' => 'Développement d\'applications mobiles',
                'slug' => 'developpement-application-mobile-nimes',
                'icon' => '📱',
                'description' => 'Applications iOS et Android avec React Native'
            ],
            [
                'title' => 'Création de site internet',
                'slug' => 'creation-site-internet-nimes',
                'icon' => '🌐',
                'description' => 'Sites vitrines et professionnels optimisés SEO'
            ],
            [
                'title' => 'Solutions digitales restauration',
                'slug' => 'solutions-digitales-restauration-nimes',
                'icon' => '🍽️',
                'description' => 'Outils digitaux pour restaurants'
            ],
            [
                'title' => 'Maintenance & Support',
                'slug' => 'maintenance-support',
                'icon' => '🔧',
                'description' => 'Accompagnement et maintenance de vos solutions'
            ],
        ];

        return $this->render('service/index.html.twig', [
            'services' => $services,
        ]);
    }

    #[Route('/services/{slug}', name: 'app_service_show')]
    public function show(string $slug): Response
    {
        // Map des services disponibles
        $services = [
            'developpement-application-mobile-nimes' => [
                'title' => 'Développement d\'applications mobiles à Nîmes',
                'template' => 'service/mobile-app.html.twig',
            ],
            'creation-site-internet-nimes' => [
                'title' => 'Création de site internet à Nîmes',
                'template' => 'service/website.html.twig',
            ],
            'solutions-digitales-restauration-nimes' => [
                'title' => 'Solutions digitales pour restaurants',
                'template' => 'service/restaurant.html.twig',
            ],
            'maintenance-support' => [
                'title' => 'Maintenance & Support',
                'template' => 'service/maintenance.html.twig',
            ],
        ];

        if (!isset($services[$slug])) {
            throw $this->createNotFoundException('Service non trouvé');
        }

        $service = $services[$slug];

        return $this->render($service['template'], [
            'service' => $service,
        ]);
    }
}
