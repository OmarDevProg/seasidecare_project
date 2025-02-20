<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Finds products based on the given criteria.
     *
     * @param array $criteria The filtering criteria.
     * @return array Array of matching products.
     */
    /**
     * Finds products based on the given criteria.
     *
     * @param array $criteria The filtering criteria.
     * @return array Array of matching products.
     */
    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('p');

        // Filtrer par gamme
        if (isset($criteria['gamme'])) {
            $qb->andWhere('p.gamme = :gamme')
                ->setParameter('gamme', $criteria['gamme']);
        }

        // Filtrer par sous-catégorie
        if (isset($criteria['subcategorie'])) {
            $qb->andWhere('p.subcategorie = :subcategorie')
                ->setParameter('subcategorie', $criteria['subcategorie']);
        }

        // Filtrer par marque (si fourni et sous forme de tableau)
        if (isset($criteria['brand']) && is_array($criteria['brand']) && count($criteria['brand']) > 0) {
            $qb->andWhere('p.brand IN (:brand)')
                ->setParameter('brand', $criteria['brand']);
        }

        // Filtrer par prix minimum
        if (isset($criteria['price_min'])) {
            $qb->andWhere('p.price >= :priceMin')
                ->setParameter('priceMin', (float) $criteria['price_min']);
        }

        // Filtrer par prix maximum
        if (isset($criteria['price_max'])) {
            $qb->andWhere('p.price <= :priceMax')
                ->setParameter('priceMax', (float) $criteria['price_max']);
        }

        // Ajouter un tri par défaut (exemple : prix croissant)
        $qb->orderBy('p.price', 'ASC');

        return $qb->getQuery()->getResult();
    }
    /**
     * Finds distinct marque values from the database.
     *
     * @return array Array of distinct marque values.
     */
    public function findDistinctMarques(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.brand')
            ->where('p.brand IS NOT NULL')
            ->andWhere('p.brand != \'\'') // Exclure les marques vides
            ->getQuery()
            ->getScalarResult();
    }

}