<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\ContactRequest;
use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Digitalfy Admin')
            ->setFaviconPath('favicon.ico');
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
        yield MenuItem::linkToRoute('Voir le site', 'fa fa-eye', 'home');
    }
}
