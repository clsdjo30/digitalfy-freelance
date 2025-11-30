<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service de notification (ping) des moteurs de recherche
 *
 * Notifie Google et Bing lorsque le sitemap est mis à jour pour accélérer l'indexation.
 */
class SearchEnginePingService
{
    private const GOOGLE_PING_URL = 'http://www.google.com/ping';
    private const BING_PING_URL = 'http://www.bing.com/ping';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire('%env(SITE_BASE_URL)%')]
        private readonly string $siteBaseUrl,
        #[Autowire('%env(bool:SITEMAP_AUTO_PING_ENABLED)%')]
        private readonly bool $autoPingEnabled,
        #[Autowire('%kernel.environment%')]
        private readonly string $environment,
    ) {}

    /**
     * Ping tous les moteurs de recherche configurés
     *
     * @return array{success: bool, message: string, results: array<string, array{success: bool, message: string}>}
     */
    public function pingSearchEngines(): array
    {
        // Ne ping pas en environnement de développement
        if ($this->environment !== 'prod') {
            $message = 'Ping désactivé en environnement ' . $this->environment;
            $this->logger->info('[SitemapPing] ' . $message);

            return [
                'success' => true,
                'message' => $message,
                'results' => []
            ];
        }

        // Ne ping pas si désactivé via configuration
        if (!$this->autoPingEnabled) {
            $message = 'Ping automatique désactivé (SITEMAP_AUTO_PING_ENABLED=false)';
            $this->logger->info('[SitemapPing] ' . $message);

            return [
                'success' => true,
                'message' => $message,
                'results' => []
            ];
        }

        $sitemapUrl = rtrim($this->siteBaseUrl, '/') . '/sitemap.xml';
        $results = [];
        $allSuccess = true;

        // Ping Google
        $googleResult = $this->pingEngine('google', self::GOOGLE_PING_URL, $sitemapUrl);
        $results['google'] = $googleResult;
        if (!$googleResult['success']) {
            $allSuccess = false;
        }

        // Ping Bing
        $bingResult = $this->pingEngine('bing', self::BING_PING_URL, $sitemapUrl);
        $results['bing'] = $bingResult;
        if (!$bingResult['success']) {
            $allSuccess = false;
        }

        return [
            'success' => $allSuccess,
            'message' => $allSuccess
                ? 'Tous les moteurs de recherche ont été notifiés'
                : 'Certains moteurs de recherche n\'ont pas pu être notifiés',
            'results' => $results
        ];
    }

    /**
     * Ping un moteur de recherche spécifique
     *
     * @return array{success: bool, message: string}
     */
    private function pingEngine(string $engineName, string $pingUrl, string $sitemapUrl): array
    {
        try {
            $url = $pingUrl . '?sitemap=' . urlencode($sitemapUrl);

            $this->logger->info("[SitemapPing] Ping {$engineName}", [
                'url' => $url,
                'sitemap' => $sitemapUrl
            ]);

            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 10,
                'max_duration' => 15,
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode >= 200 && $statusCode < 300) {
                $message = "Ping réussi (HTTP {$statusCode})";
                $this->logger->info("[SitemapPing] {$engineName} : {$message}");

                return [
                    'success' => true,
                    'message' => $message
                ];
            } else {
                $message = "Réponse inattendue (HTTP {$statusCode})";
                $this->logger->warning("[SitemapPing] {$engineName} : {$message}");

                return [
                    'success' => false,
                    'message' => $message
                ];
            }

        } catch (TransportExceptionInterface $e) {
            $message = 'Erreur réseau : ' . $e->getMessage();
            $this->logger->error("[SitemapPing] {$engineName} : {$message}", [
                'exception' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $message
            ];

        } catch (\Exception $e) {
            $message = 'Erreur inattendue : ' . $e->getMessage();
            $this->logger->error("[SitemapPing] {$engineName} : {$message}", [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $message
            ];
        }
    }

    /**
     * Ping Google uniquement
     */
    public function pingGoogle(): array
    {
        $sitemapUrl = rtrim($this->siteBaseUrl, '/') . '/sitemap.xml';
        return $this->pingEngine('google', self::GOOGLE_PING_URL, $sitemapUrl);
    }

    /**
     * Ping Bing uniquement
     */
    public function pingBing(): array
    {
        $sitemapUrl = rtrim($this->siteBaseUrl, '/') . '/sitemap.xml';
        return $this->pingEngine('bing', self::BING_PING_URL, $sitemapUrl);
    }
}
