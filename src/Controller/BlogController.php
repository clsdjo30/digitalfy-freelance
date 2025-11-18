<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'posts' => [],
        ]);
    }

    #[Route('/blog/categorie/{slug}', name: 'app_blog_category')]
    public function category(string $slug): Response
    {
        return $this->render('blog/category.html.twig', [
            'slug' => $slug,
        ]);
    }

    #[Route('/blog/{slug}', name: 'app_blog_show')]
    public function show(string $slug): Response
    {
        return $this->render('blog/show.html.twig', [
            'slug' => $slug,
        ]);
    }
}
