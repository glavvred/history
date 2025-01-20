<?php

namespace App\Repository;

use App\Entity\Category;
use DateTime;
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

    public function getCategoriesWithEventCount()
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'COUNT(e.id) AS event_count')
            ->leftJoin('c.publicEvents', 'e')
            ->where('DATE_ADD(e.startDate,e.duration,\'day\')  > :date')
            ->setParameter('date', new DateTime())
            ->groupBy('c.id')
            ->orderBy('event_count', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
