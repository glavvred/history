<?php

namespace App\Repository;

use App\Entity\ExcursionRoute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExcursionRoute>
 */
class ExcursionRouteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExcursionRoute::class);
    }

        /**
         * @return ExcursionRoute[] Returns an array of ExcursionRoute objects
         */
        public function getList(): array
        {
            return $this->createQueryBuilder('e')
                ->andWhere('e.published = true')
                ->getQuery()
                ->getResult()
            ;
        }

    //    public function findOneBySomeField($value): ?ExcursionRoute
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
