<?php

namespace App\EventListener;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\Project;
use App\Service\SitemapChangeTracker;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

/**
 * Écoute les événements Doctrine et marque le sitemap pour régénération
 *
 * Détecte les changements sur les entités qui affectent le sitemap :
 * - BlogPost (uniquement si published)
 * - Project (uniquement si published)
 * - Category (toujours, car affecte les URLs)
 *
 * Ne déclenche PAS la régénération immédiate, mais crée simplement un flag.
 * C'est le CRON qui se chargera de la régénération effective.
 */
#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
class SitemapDoctrineEventSubscriber
{
    public function __construct(
        private readonly SitemapChangeTracker $changeTracker,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Appelé après l'insertion d'une nouvelle entité
     */
    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->handleEntityChange($args->getObject(), 'persist');
    }

    /**
     * Appelé après la mise à jour d'une entité
     */
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->handleEntityChange($args->getObject(), 'update');
    }

    /**
     * Appelé après la suppression d'une entité
     */
    public function postRemove(PostRemoveEventArgs $args): void
    {
        $this->handleEntityChange($args->getObject(), 'remove');
    }

    /**
     * Traite le changement d'entité et marque pour régénération si nécessaire
     */
    private function handleEntityChange(object $entity, string $operation): void
    {
        $needsUpdate = false;
        $entityInfo = null;

        // BlogPost : uniquement si publié
        if ($entity instanceof BlogPost) {
            if ($entity->getStatus() === 'published') {
                $needsUpdate = true;
                $entityInfo = sprintf('BlogPost#%d (%s)', $entity->getId(), $entity->getTitle());
            }
        }

        // Project : uniquement si publié
        elseif ($entity instanceof Project) {
            if ($entity->isPublished()) {
                $needsUpdate = true;
                $entityInfo = sprintf('Project#%d (%s)', $entity->getId(), $entity->getTitle());
            }
        }

        // Category : toujours (car affecte les URLs du blog)
        elseif ($entity instanceof Category) {
            $needsUpdate = true;
            $entityInfo = sprintf('Category#%d (%s)', $entity->getId(), $entity->getName());
        }

        // Marquer pour régénération si nécessaire
        if ($needsUpdate) {
            $this->changeTracker->markAsNeedingUpdate();

            $this->logger->info('[SitemapSubscriber] Changement détecté - sitemap marqué pour régénération', [
                'operation' => $operation,
                'entity' => $entityInfo
            ]);
        }
    }
}
