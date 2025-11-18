<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/mentions-legales', name: 'app_legal')]
    public function legal(): Response
    {
        return $this->render('page/legal.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/politique-confidentialite', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('page/privacy.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
}
