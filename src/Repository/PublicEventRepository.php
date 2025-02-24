<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\PublicEvent;
use App\Entity\Region;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @extends ServiceEntityRepository<PublicEvent>
 */
class PublicEventRepository extends ServiceEntityRepository
{
    private SessionInterface $session;

    /**
     * @param ManagerRegistry $registry
     * @param RequestStack $requestStack
     */
    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
        parent::__construct($registry, PublicEvent::class);
    }

    /**
     * @param array $region
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param array|null $filters
     * @param array|null $eventsShown
     * @param Category|null $category
     * @return Query
     */
    public function getQueryByCriteria(array         $region,
                                       DateTime      $startDate = new DateTime(),
                                       DateTime      $endDate = new DateTime(),
                                       array|null    $filters = null,
                                       array|null    $eventsShown = null,
                                       Category|null $category = null
    ): Query
    {

//        var_dump($startDate->format('Y-m-d H:i:s'));
//        var_dump($endDate->format('Y-m-d H:i:s'));

        $eventsQuery = $this->createQueryBuilder('pe')
            ->leftJoin('pe.filter', 'f')
            ->leftJoin('pe.category', 'c')
            ->where('pe.region IN (:region)')
            ->andWhere("
               (:startDate >= pe.startDate AND :startDate <= DATE_ADD(pe.startDate, (pe.duration -1) , 'DAY')) 
            OR (:endDate >= pe.startDate AND :endDate <= DATE_ADD(pe.startDate, (pe.duration -1) , 'DAY'))
            OR (pe.startDate >= :startDate AND pe.startDate <= :endDate) 
            OR (DATE_ADD(pe.startDate, (pe.duration -1) , 'DAY') >= :startDate AND DATE_ADD(pe.startDate, (pe.duration -1) , 'DAY') <= :endDate) 
            ")
            ->setParameter('region', $region)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('pe.duration', 'ASC')
            ->addOrderBy('pe.startDate', 'ASC')
            ->addOrderBy('pe.views', 'DESC');

        if (!empty($eventsShown)) {
            $eventsQuery->andWhere('pe.id NOT IN (:eventsShown)')
                ->setParameter('eventsShown', $eventsShown);
        }

        if (!empty($category)) {
            $eventsQuery->andWhere('pe.category = :category')
                ->setParameter('category', $category);
        }

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $filterName = strtolower($filter->groupId);

                switch ($filterName) {
                    case 'toll':
                        switch ($filter->value) {
                            case 5001:
                                $filterValue = 'f.short = 5001';
                                break;
                            case 5000:
                                $filterValue = 'f.short IN (5000,3000,1000,\'free\')';
                                break;
                            case 3000:
                                $filterValue = 'f.short IN (3000, 1000, \'free\')';
                                break;
                            case 1000:
                                $filterValue = 'f.short IN (1000, \'free\')';
                                break;
                            case 'free':
                                $filterValue = 'f.short NOT IN (5001,5000,3000,1000)';
                                break;
                        }
                        break;
                    default:
                        $filterValue = 'f.short = :filterValue';
                        break;
                }
                $eventsQuery->andWhere('f.category = :filterCategory AND ' . $filterValue);
                $eventsQuery->setParameter(key: 'filterCategory', value: $filterName);
                if (strpos($filterValue, ':filterValue')) {
                    $eventsQuery->setParameter(key: 'filterValue', value: $filter->value);
                }
            }
        }

        return $eventsQuery->getQuery();

    }

    /**
     * @param Category|null $category
     * @param int|null $limit
     * @return mixed
     */
    public function getUpcomingEvents(Category $category = null, int $limit = null): mixed
    {
        $eventsQuery = $this->createQueryBuilder('pe')
            ->where('DATE_ADD(pe.startDate,pe.duration,\'day\')  >= :date')
            ->setParameter('date', new DateTime());

        if (!empty($category)) {
            $eventsQuery->andWhere('pe.category = :category')
                ->setParameter('category', $category);
        }

        if (!empty($limit)) {
            $eventsQuery->setMaxResults($limit);
        }

        return $eventsQuery->orderBy('pe.startDate', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param Category|null $category
     * @param int|null $limit
     * @return mixed
     */
    public function getPastEvents(Category $category = null, int $limit = null): mixed
    {
        $eventsQuery = $this->createQueryBuilder('pe')
            ->where('DATE_ADD(pe.startDate,pe.duration,\'day\')  < :date')
            ->setParameter('date', new DateTime());

        if (!empty($category)) {
            $eventsQuery->andWhere('pe.category = :category')
                ->setParameter('category', $category);
        }

        if (!empty($limit)) {
            $eventsQuery->setMaxResults($limit);
        }

        return $eventsQuery->orderBy('pe.startDate', 'ASC')
            ->getQuery()->getResult();
    }


    public function getConstantEventsQuery(): Query
    {
        $eventsQuery = $this->createQueryBuilder('pe')
            ->where('pe.constant IS NOT NULL');

        return $eventsQuery->orderBy('pe.startDate', 'ASC')
            ->getQuery();
    }

}
