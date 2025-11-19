<?php

namespace App\Controller;

use App\Repository\BlogPostRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        BlogPostRepository $blogPostRepo,
        ProjectRepository $projectRepo
    ): Response {
        // Récupérer les 3 derniers articles publiés
        $recentPosts = $blogPostRepo->findBy(
            ['status' => 'published'],
            ['publishedAt' => 'DESC'],
            3
        );

        // Récupérer les 3 projets publiés les plus récents
        $featuredProjects = $projectRepo->findBy(
            ['published' => true],
            ['createdAt' => 'DESC'],
            3
        );

        return $this->render('home/index.html.twig', [
            'recentPosts' => $recentPosts,
            'featuredProjects' => $featuredProjects,
        ]);
    }
}
