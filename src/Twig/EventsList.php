<?php

namespace App\Twig;

use DateTime;
use Twig\TwigFunction;
use App\Entity\PublicEvent;
use App\Entity\Organisation;
use Twig\Extension\AbstractExtension;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;

class EventsList extends AbstractExtension
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getNextEvents', [$this, 'getNextEvents']),
            new TwigFunction('getPastEvents', [$this, 'getPastEvents']),
        ];
    }

    public function getNextEvents(Organisation $organisation): array
    {
        return $this
            ->managerRegistry
            ->getRepository(PublicEvent::class)
            ->createQueryBuilder('pe')
            ->where('pe.organisation = :organisation')
            ->andWhere('(pe.startDate >= CURRENT_DATE() - pe.duration OR pe.duration = 0) ' )
            ->setParameter('organisation', $organisation)
            ->orderBy('pe.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getPastEvents(Organisation $organisation): array
    {
        return $this
            ->managerRegistry
            ->getRepository(PublicEvent::class)
            ->createQueryBuilder('pe')
            ->where('pe.organisation = :organisation')
            ->andWhere('pe.startDate < CURRENT_DATE() - pe.duration  ' )
            ->andWhere('pe.duration != 0')
            ->setParameter('organisation', $organisation)
            ->orderBy('pe.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}