<?php

namespace App\Repository;

use App\Entity\OrganisationCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrganisationCategory>
 */
class OrganisationCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganisationCategory::class);
    }

    public function getCategoriesWithOrganisationCount()
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'COUNT(o.id) AS org_count')
            ->leftJoin('c.organisations', 'o')
            ->groupBy('c.id')
            ->orderBy('org_count', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
