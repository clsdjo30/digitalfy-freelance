<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    #[Route('/projets', name: 'app_projects')]
    public function index(ProjectRepository $repo): Response
    {
        $projects = $repo->findBy(['published' => true], ['id' => 'DESC']);

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/projets/{slug}', name: 'app_project_show')]
    public function show(string $slug, ProjectRepository $repo): Response
    {
        $project = $repo->findOneBy(['slug' => $slug, 'published' => true]);

        if (!$project) {
            throw $this->createNotFoundException();
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }
}
