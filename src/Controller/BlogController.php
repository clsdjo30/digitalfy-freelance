<?php

namespace App\Controller;

use App\Repository\BlogPostRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(Request $request, BlogPostRepository $repo): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 12;

        $posts = $repo->findPublishedPaginated($page, $limit);
        $totalPosts = $repo->countPublished();

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => ceil($totalPosts / $limit),
        ]);
    }

    #[Route('/blog/categorie/{slug}', name: 'app_blog_category')]
    public function category(
        string $slug,
        Request $request,
        CategoryRepository $categoryRepo,
        BlogPostRepository $postRepo
    ): Response {
        $category = $categoryRepo->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException();
        }

        $page = $request->query->getInt('page', 1);
        $posts = $postRepo->findByCategory($category, $page);

        return $this->render('blog/category.html.twig', [
            'category' => $category,
            'posts' => $posts,
            'currentPage' => $page,
        ]);
    }

    #[Route('/blog/{slug}', name: 'app_blog_show')]
    public function show(string $slug, BlogPostRepository $repo): Response
    {
        $post = $repo->findOneBy(['slug' => $slug, 'status' => 'published']);

        if (!$post) {
            throw $this->createNotFoundException();
        }

        // Articles suggérés (même catégorie)
        $relatedPosts = $repo->findRelated($post, 3);

        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
