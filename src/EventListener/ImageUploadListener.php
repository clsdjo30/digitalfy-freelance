<?php

namespace App\EventListener;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Image::class)]
class ImageUploadListener
{
    public function prePersist(Image $image): void
    {
        // Définir la date d'upload lors de la création
        if ($image->getUploadedAt() === null) {
            $image->setUploadedAt(new \DateTimeImmutable());
        }
    }
}
