<?php

namespace App\Controller;

use App\Entity\PublicEvent;
use App\Entity\PublicEventStatistic;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StatisticController extends AbstractController
{
    #[Route('/click/{id}/{zone}', name: 'public_event_zone_click_counter', methods: ['GET'])]
    public function setLocation(int             $id,
                                string          $zone,
                                ManagerRegistry $managerRegistry,
    ): JsonResponse
    {
        $publicEvent = $managerRegistry->getRepository(PublicEvent::class)->find($id);
        $statistics = $publicEvent->getStatistic();
        if (empty($statistics)){
            $statistics = new PublicEventStatistic();
            $statistics->setPublicEvent($publicEvent);
        }

        switch ($zone) {
            case 'map':
                $statistics->setMap($statistics->getMap() + 1);
                break;
            case 'button':
                $statistics->setButton($statistics->getButton() + 1);
                break;
            case 'org':
                $statistics->setOrganisation($statistics->getOrganisation() + 1);
                break;
            default:
                return new JsonResponse(
                    [
                        'not' => 'good'
                    ]);

        }

        $managerRegistry->getManager()->persist($statistics);
        $managerRegistry->getManager()->flush();

        return new JsonResponse(
            [
                'all' => 'good'
            ]);
    }
}