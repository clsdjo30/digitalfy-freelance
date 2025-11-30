<?php

namespace App\Service;

use App\Repository\BlogPostRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProjectRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Service de génération du sitemap XML statique
 *
 * Génère un fichier sitemap.xml dans public/ contenant toutes les URLs publiques du site.
 * Utilise une écriture atomique (fichier temporaire + rename) pour éviter la corruption.
 */
class SitemapGeneratorService
{
    private const SITEMAP_PATH = '/sitemap.xml';
    private const SITEMAP_TEMP_PATH = '/sitemap.tmp.xml';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly BlogPostRepository $blogPostRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly LoggerInterface $logger,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {}

    /**
     * Génère le sitemap et l'écrit dans public/sitemap.xml
     *
     * @return array{success: bool, urlCount: int, message: string}
     */
    public function generateStaticSitemap(): array
    {
        try {
            $publicDir = $this->projectDir . '/public';
            $sitemapPath = $publicDir . self::SITEMAP_PATH;
            $tempPath = $publicDir . self::SITEMAP_TEMP_PATH;

            // Vérifier les permissions d'écriture
            if (!is_writable($publicDir)) {
                $message = "Le répertoire public/ n'est pas accessible en écriture";
                $this->logger->error('[Sitemap] ' . $message, ['dir' => $publicDir]);
                return ['success' => false, 'urlCount' => 0, 'message' => $message];
            }

            // Générer les données du sitemap
            $urls = $this->getSitemapUrls();
            $urlCount = count($urls);

            // Générer le XML
            $xml = $this->generateXml($urls);

            // Écriture atomique : écrire dans fichier temporaire puis renommer
            $bytesWritten = file_put_contents($tempPath, $xml);

            if ($bytesWritten === false) {
                $message = 'Échec de l\'écriture du fichier temporaire';
                $this->logger->error('[Sitemap] ' . $message, ['path' => $tempPath]);
                return ['success' => false, 'urlCount' => 0, 'message' => $message];
            }

            // Renommer atomiquement
            if (!rename($tempPath, $sitemapPath)) {
                $message = 'Échec du renommage du fichier temporaire';
                $this->logger->error('[Sitemap] ' . $message);
                @unlink($tempPath); // Nettoyer le fichier temporaire
                return ['success' => false, 'urlCount' => 0, 'message' => $message];
            }

            $this->logger->info('[Sitemap] Génération réussie', [
                'urlCount' => $urlCount,
                'size' => filesize($sitemapPath),
                'path' => $sitemapPath
            ]);

            return [
                'success' => true,
                'urlCount' => $urlCount,
                'message' => "Sitemap généré avec succès : {$urlCount} URLs"
            ];

        } catch (\Exception $e) {
            $message = 'Erreur lors de la génération du sitemap : ' . $e->getMessage();
            $this->logger->error('[Sitemap] ' . $message, [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Nettoyer le fichier temporaire en cas d'erreur
            if (isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }

            return ['success' => false, 'urlCount' => 0, 'message' => $message];
        }
    }

    /**
     * Récupère toutes les URLs à inclure dans le sitemap
     *
     * @return array<int, array{loc: string, lastmod: ?\DateTimeInterface, priority: float, changefreq: string}>
     */
    public function getSitemapUrls(): array
    {
        $urls = [];

        // Page d'accueil
        $urls[] = [
            'loc' => $this->urlGenerator->generate('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'lastmod' => null,
            'priority' => 1.0,
            'changefreq' => 'weekly'
        ];

        // Pages de services
        $services = [
            'developpement-application-mobile-nimes',
            'creation-site-internet-nimes',
            'solutions-digitales-restauration-nimes',
            'maintenance-support'
        ];
        foreach ($services as $serviceSlug) {
            $urls[] = [
                'loc' => $this->urlGenerator->generate('app_service_show', ['slug' => $serviceSlug], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => null,
                'priority' => 0.9,
                'changefreq' => 'monthly'
            ];
        }

        // Page À propos
        $urls[] = [
            'loc' => $this->urlGenerator->generate('app_about', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'lastmod' => null,
            'priority' => 0.7,
            'changefreq' => 'monthly'
        ];

        // Page Contact
        $urls[] = [
            'loc' => $this->urlGenerator->generate('app_contact', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'lastmod' => null,
            'priority' => 0.8,
            'changefreq' => 'monthly'
        ];

        // Liste des articles de blog
        $urls[] = [
            'loc' => $this->urlGenerator->generate('app_blog', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'lastmod' => null,
            'priority' => 0.8,
            'changefreq' => 'weekly'
        ];

        // Articles de blog publiés
        $posts = $this->blogPostRepository->findBy(['status' => 'published'], ['publishedAt' => 'DESC']);
        foreach ($posts as $post) {
            $urls[] = [
                'loc' => $this->urlGenerator->generate('app_blog_show', ['slug' => $post->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => $post->getUpdatedAt(),
                'priority' => 0.7,
                'changefreq' => 'weekly'
            ];
        }

        // Catégories de blog (optimisé : uniquement celles avec articles publiés)
        $categories = $this->categoryRepository->findCategoriesWithPublishedPosts();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => $this->urlGenerator->generate('app_blog_category', ['slug' => $category->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => null,
                'priority' => 0.6,
                'changefreq' => 'weekly'
            ];
        }

        // Page liste des projets
        $urls[] = [
            'loc' => $this->urlGenerator->generate('app_projects', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'lastmod' => null,
            'priority' => 0.8,
            'changefreq' => 'monthly'
        ];

        // Projets publiés
        $projects = $this->projectRepository->findBy(['published' => true]);
        foreach ($projects as $project) {
            $urls[] = [
                'loc' => $this->urlGenerator->generate('app_project_show', ['slug' => $project->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => $project->getCreatedAt(),
                'priority' => 0.8,
                'changefreq' => 'monthly'
            ];
        }

        return $urls;
    }

    /**
     * Génère le XML du sitemap à partir des URLs
     *
     * @param array<int, array{loc: string, lastmod: ?\DateTimeInterface, priority: float, changefreq: string}> $urls
     */
    private function generateXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $urlData) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($urlData['loc'], ENT_XML1, 'UTF-8') . '</loc>' . PHP_EOL;

            if ($urlData['lastmod'] instanceof \DateTimeInterface) {
                $xml .= '    <lastmod>' . $urlData['lastmod']->format('Y-m-d\TH:i:sP') . '</lastmod>' . PHP_EOL;
            }

            $xml .= '    <changefreq>' . $urlData['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . number_format($urlData['priority'], 1) . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>' . PHP_EOL;

        return $xml;
    }

    /**
     * Vérifie si le fichier sitemap existe
     */
    public function sitemapExists(): bool
    {
        return file_exists($this->projectDir . '/public' . self::SITEMAP_PATH);
    }

    /**
     * Retourne le chemin absolu du sitemap
     */
    public function getSitemapPath(): string
    {
        return $this->projectDir . '/public' . self::SITEMAP_PATH;
    }
}
