<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\ContactRequest;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        // Mettre à jour la dernière connexion
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
            $user->setLastLoginAt(new \DateTimeImmutable());
            $this->em->flush();
        }

        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Digitalfy - Administration')
            ->setFaviconPath('favicon.ico');
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

        yield MenuItem::section('Projets');
        yield MenuItem::linkToCrud('Projets', 'fa fa-briefcase', Project::class);

        yield MenuItem::section('Contact');
        yield MenuItem::linkToCrud('Demandes', 'fa fa-envelope', ContactRequest::class);

        yield MenuItem::section('Site');
        yield MenuItem::linkToRoute('Voir le site', 'fa fa-eye', 'app_home');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}
