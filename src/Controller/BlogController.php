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
    public function index(
        Request $request,
        BlogPostRepository $postRepo,
        CategoryRepository $categoryRepo
    ): Response {
        $page = $request->query->getInt('page', 1);
        $limit = 12;

        $posts = $postRepo->findPublishedPaginated($page, $limit);
        $totalPosts = $postRepo->countPublished();

        // Données pour la sidebar
        $categories = $categoryRepo->findAll();
        $latestPosts = $postRepo->findLatest(3);

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => ceil($totalPosts / $limit),
            'categories' => $categories,
            'latestPosts' => $latestPosts,
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
        $limit = 12;

        $posts = $postRepo->findByCategory($category, $page, $limit);
        $totalPosts = $postRepo->categoryCount($category);

        // Données pour la sidebar
        $categories = $categoryRepo->findAll();
        $latestPosts = $postRepo->findLatest(3);

        return $this->render('blog/category.html.twig', [
            'category' => $category,
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => ceil($totalPosts / $limit),
            'categories' => $categories,
            'latestPosts' => $latestPosts,
        ]);
    }

    #[Route('/blog/{slug}', name: 'app_blog_show')]
    public function show(
        string $slug,
        BlogPostRepository $postRepo,
        CategoryRepository $categoryRepo
    ): Response {
        $post = $postRepo->findOneBy(['slug' => $slug, 'status' => 'published']);

        if (!$post) {
            throw $this->createNotFoundException();
        }

        // Articles suggérés (même catégorie)
        $relatedPosts = $postRepo->findRelated($post, 3);

        // Données pour la sidebar
        $categories = $categoryRepo->findAll();
        $latestPosts = $postRepo->findLatest(3);

        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'categories' => $categories,
            'latestPosts' => $latestPosts,
        ]);
    }
}
