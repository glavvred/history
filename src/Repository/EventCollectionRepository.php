<?php

namespace App\Repository;

use App\Entity\EventCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventCollection>
 */
class EventCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventCollection::class);
    }

    public function getAllCollections(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.id != :val and (e.mainPage != 0 or e.bottomPage != 0)')
            ->setParameter('val', 2) //убираем лучшее
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return EventCollection[] Returns an array of EventCollection objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EventCollection
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
