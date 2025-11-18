<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    #[Route('/projets', name: 'app_projects')]
    public function index(): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => [],
        ]);
    }

    #[Route('/projets/{slug}', name: 'app_project_show')]
    public function show(string $slug): Response
    {
        return $this->render('project/show.html.twig', [
            'slug' => $slug,
        ]);
    }
}
