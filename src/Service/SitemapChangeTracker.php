<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Service de suivi des changements nécessitant une régénération du sitemap
 *
 * Utilise un fichier flag dans var/ pour indiquer qu'une régénération est nécessaire.
 * Cela évite de régénérer le sitemap inutilement lors de chaque exécution du CRON.
 */
class SitemapChangeTracker
{
    private const FLAG_FILENAME = 'sitemap_needs_update.flag';

    private readonly string $flagPath;

    public function __construct(
        private readonly LoggerInterface $logger,
        #[Autowire('%kernel.project_dir%')]
        string $projectDir,
    ) {
        $this->flagPath = $projectDir . '/var/' . self::FLAG_FILENAME;
    }

    /**
     * Marque le sitemap comme nécessitant une mise à jour
     */
    public function markAsNeedingUpdate(): void
    {
        try {
            $directory = dirname($this->flagPath);

            // Créer le répertoire var/ s'il n'existe pas
            if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                $this->logger->error('[SitemapTracker] Impossible de créer le répertoire', [
                    'directory' => $directory
                ]);
                return;
            }

            // Créer le fichier flag avec un timestamp
            $content = date('Y-m-d H:i:s');
            if (file_put_contents($this->flagPath, $content) === false) {
                $this->logger->error('[SitemapTracker] Impossible de créer le flag', [
                    'path' => $this->flagPath
                ]);
                return;
            }

            $this->logger->info('[SitemapTracker] Flag de mise à jour créé', [
                'path' => $this->flagPath,
                'time' => $content
            ]);

        } catch (\Exception $e) {
            $this->logger->error('[SitemapTracker] Erreur lors de la création du flag', [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Vérifie si une mise à jour est nécessaire
     */
    public function needsUpdate(): bool
    {
        return file_exists($this->flagPath);
    }

    /**
     * Marque le sitemap comme à jour (supprime le flag)
     */
    public function markAsUpdated(): void
    {
        if (!file_exists($this->flagPath)) {
            return;
        }

        try {
            if (unlink($this->flagPath)) {
                $this->logger->info('[SitemapTracker] Flag de mise à jour supprimé', [
                    'path' => $this->flagPath
                ]);
            } else {
                $this->logger->warning('[SitemapTracker] Impossible de supprimer le flag', [
                    'path' => $this->flagPath
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->error('[SitemapTracker] Erreur lors de la suppression du flag', [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Retourne la date de dernière modification du flag (si existe)
     */
    public function getLastChangeTime(): ?\DateTimeImmutable
    {
        if (!file_exists($this->flagPath)) {
            return null;
        }

        $content = file_get_contents($this->flagPath);
        if ($content === false) {
            return null;
        }

        try {
            return new \DateTimeImmutable($content);
        } catch (\Exception $e) {
            $this->logger->warning('[SitemapTracker] Format de date invalide dans le flag', [
                'content' => $content
            ]);
            return null;
        }
    }

    /**
     * Retourne le chemin du fichier flag
     */
    public function getFlagPath(): string
    {
        return $this->flagPath;
    }
}
