<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Region>
 */
class RegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    public function getChildren(Region $parentRegion)
    {
        return $this->createQueryBuilder('r')
            ->where('r.id = :val')
            ->orWhere('r.parent = :val')
            ->setParameter('val', $parentRegion)
            ->getQuery()
            ->getResult();
    }

    public function getTopLevel()
    {
        return $this->createQueryBuilder('r')
            ->where('r.parent is null')
            ->getQuery()
            ->getResult();
    }

}
