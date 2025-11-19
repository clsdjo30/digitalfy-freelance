<?php

namespace App\EventListener;

use App\Repository\BlogPostRepository;
use App\Repository\ProjectRepository;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapListener implements EventSubscriberInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private BlogPostRepository $blogPostRepo,
        private ProjectRepository $projectRepo,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        // Page d'accueil
        $event->getUrlContainer()->addUrl(
            new UrlConcrete(
                $this->urlGenerator->generate('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
                priority: 1.0,
                changefreq: UrlConcrete::CHANGEFREQ_WEEKLY
            ),
            'default'
        );

        // Pages de services
        $services = [
            'developpement-application-mobile-nimes',
            'creation-site-internet-nimes',
            'solutions-digitales-restauration-nimes',
            'maintenance-support'
        ];
        foreach ($services as $serviceSlug) {
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate('app_service_show', ['slug' => $serviceSlug], UrlGeneratorInterface::ABSOLUTE_URL),
                    priority: 0.9,
                    changefreq: UrlConcrete::CHANGEFREQ_MONTHLY
                ),
                'default'
            );
        }

        // Page À propos
        $event->getUrlContainer()->addUrl(
            new UrlConcrete(
                $this->urlGenerator->generate('app_about', [], UrlGeneratorInterface::ABSOLUTE_URL),
                priority: 0.7,
                changefreq: UrlConcrete::CHANGEFREQ_MONTHLY
            ),
            'default'
        );

        // Page Contact
        $event->getUrlContainer()->addUrl(
            new UrlConcrete(
                $this->urlGenerator->generate('app_contact', [], UrlGeneratorInterface::ABSOLUTE_URL),
                priority: 0.8,
                changefreq: UrlConcrete::CHANGEFREQ_MONTHLY
            ),
            'default'
        );

        // Liste des articles de blog
        $event->getUrlContainer()->addUrl(
            new UrlConcrete(
                $this->urlGenerator->generate('app_blog', [], UrlGeneratorInterface::ABSOLUTE_URL),
                priority: 0.8,
                changefreq: UrlConcrete::CHANGEFREQ_WEEKLY
            ),
            'default'
        );

        // Articles de blog publiés
        $posts = $this->blogPostRepo->findBy(['status' => 'published']);
        foreach ($posts as $post) {
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate('app_blog_show', ['slug' => $post->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    lastmod: $post->getUpdatedAt(),
                    priority: 0.7,
                    changefreq: UrlConcrete::CHANGEFREQ_WEEKLY
                ),
                'default'
            );
        }

        // Catégories de blog
        foreach ($this->blogPostRepo->findAll() as $post) {
            if ($post->getCategory()) {
                $event->getUrlContainer()->addUrl(
                    new UrlConcrete(
                        $this->urlGenerator->generate('app_blog_category', ['slug' => $post->getCategory()->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                        priority: 0.6,
                        changefreq: UrlConcrete::CHANGEFREQ_WEEKLY
                    ),
                    'default'
                );
            }
        }

        // Page liste des projets
        $event->getUrlContainer()->addUrl(
            new UrlConcrete(
                $this->urlGenerator->generate('app_projects', [], UrlGeneratorInterface::ABSOLUTE_URL),
                priority: 0.8,
                changefreq: UrlConcrete::CHANGEFREQ_MONTHLY
            ),
            'default'
        );

        // Projets
        $projects = $this->projectRepo->findBy(['published' => true]);
        foreach ($projects as $project) {
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate('app_project_show', ['slug' => $project->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    priority: 0.8,
                    changefreq: UrlConcrete::CHANGEFREQ_MONTHLY
                ),
                'default'
            );
        }
    }
}
