<?php

namespace App\Repository;

use App\Dto\PackageSearchFilter;
use App\Entity\Package;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Package>
 */
class PackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

//    /**
//     * @return Package[] Returns an array of Package objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
    public function findAvailable(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.consumer_order', 'o')
            ->where('o.id IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findByFilter(PackageSearchFilter $filter) : array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.business', 'b')
            ->leftJoin('b.business_type', 'bt');

        if($filter->name)
        {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%'.$filter->name.'%');
        }

        if($filter->minPrice)
        {
            $qb->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $filter->minPrice);
        }

        if($filter->maxPrice)
        {
            $qb->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $filter->maxPrice);
        }

        if($filter->category)
        {
            $qb->andWhere('c.id = :category')
                ->setParameter('category', $filter->category->getId());
        }

        if($filter->businessType)
        {
            $qb->andWhere('bt.id = :business_type')
                ->setParameter('business_type', $filter->businessType->getId());
        }

        if($filter->business)
        {
            $qb->andWhere('b.id = :business')
                ->setParameter('business', $filter->business->getId());
        }

        if($filter->city)
        {
            $qb->andWhere('b.city LIKE :city')
                ->setParameter('city', '%'.$filter->city.'%');
        }

        return $qb->getQuery()->getResult();
    }

//    public function findOneBySomeField($value): ?Package
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
