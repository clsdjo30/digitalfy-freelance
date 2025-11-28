<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\ContactRequest;
use App\Entity\Image;
use App\Entity\Project;
use App\Repository\BlogPostRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProjectRepository;
use App\Repository\ContactRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;

#[IsGranted('ROLE_ADMIN')]
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    protected EntityManagerInterface $em;
    protected BlogPostRepository $blogPostRepository;
    protected ProjectRepository $projectRepository;
    protected CategoryRepository $categoryRepository;
    protected ContactRequestRepository $contactRequestRepository;

    public function __construct(
        EntityManagerInterface $em,
        BlogPostRepository $blogPostRepository,
        ProjectRepository $projectRepository,
        CategoryRepository $categoryRepository,
        ContactRequestRepository $contactRequestRepository
    ) {
        $this->em = $em;
        $this->blogPostRepository = $blogPostRepository;
        $this->projectRepository = $projectRepository;
        $this->categoryRepository = $categoryRepository;
        $this->contactRequestRepository = $contactRequestRepository;
    }

    public function index(): Response
    {
        // Mettre à jour la dernière connexion
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
            $user->setLastLoginAt(new \DateTimeImmutable());
            $this->em->flush();
        }

        return $this->render('admin/dashboard.html.twig', [
            'blogPostCount' => $this->blogPostRepository->count([]),
            'projectCount' => $this->projectRepository->count([]),
            'categoryCount' => $this->categoryRepository->count([]),
            'contactRequestCount' => $this->contactRequestRepository->count([]),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Digitalfy - Administration')
            ->setFaviconPath('/favicon.ico');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Afficher les informations de l'utilisateur connecté
        return UserMenu::new()
            ->displayUserName(true)
            ->displayUserAvatar(false)
            ->setName($user->getUserIdentifier())
            ->setAvatarUrl(null)
            ->addMenuItems([
                MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out'),
            ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Blog');
        yield MenuItem::linkToCrud('Articles', 'fa fa-file-text', BlogPost::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tags', Category::class);
        yield MenuItem::linkToCrud('Galerie d\'images', 'fa fa-images', Image::class);

        yield MenuItem::section('Projets');
        yield MenuItem::linkToCrud('Projets', 'fa fa-briefcase', Project::class);

        yield MenuItem::section('Contact');
        yield MenuItem::linkToCrud('Demandes', 'fa fa-envelope', ContactRequest::class);

        yield MenuItem::section('Site');
        yield MenuItem::linkToRoute('Voir le site', 'fa fa-eye', 'app_home');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}
