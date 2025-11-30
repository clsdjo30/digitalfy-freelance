<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Trouve toutes les catégories qui ont au moins un article publié
     *
     * Utilise un JOIN pour éviter le N+1 problem et ne retourne que les catégories
     * contenant effectivement des articles publiés.
     *
     * @return Category[] Returns an array of Category objects
     */
    public function findCategoriesWithPublishedPosts(): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.blogPosts', 'p')
            ->where('p.status = :status')
            ->setParameter('status', 'published')
            ->groupBy('c.id')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
