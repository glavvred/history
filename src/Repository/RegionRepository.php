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
        if ($parentRegion->getName() === 'Все регионы') {
            return $this->createQueryBuilder('r')
                ->getQuery()
                ->getResult();
        }

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

    public function getNestedRegions(): array
    {
        $regions = $this->createQueryBuilder('r')
            ->select('r')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();

        $regionMap = [];
        foreach ($regions as $region) {
            $regionMap[$region->getId()] = [
                'region' => $region,
                'children' => []
            ];
        }

        $nestedRegions = [];

        foreach ($regions as $region) {
            if ($region->getParent() === null) {
                $nestedRegions[] = &$regionMap[$region->getId()];
            } else {
                $parentId = $region->getParent()->getId();
                if (isset($regionMap[$parentId])) {
                    $regionMap[$parentId]['children'][] = &$regionMap[$region->getId()];
                }
            }
        }

        return $nestedRegions;
    }
}
