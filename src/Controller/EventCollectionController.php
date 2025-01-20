<?php

namespace App\Controller;

use DateTime;
use App\Entity\EventCollection;
use App\Repository\CategoryRepository;
use App\Repository\FilterRepository;
use App\Repository\PublicEventRepository;
use Doctrine\Common\Collections\Criteria;
use App\Repository\EventCollectionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventCollectionController extends AbstractController
{
    #[Route('/collections', name: 'app_event_collection_index', methods: ['GET'])]
    public function index(EventCollectionRepository $eventCollectionRepository): Response
    {
        return $this->render('event_collection/index.html.twig', [
            'event_collections' => $eventCollectionRepository->getAllCollections(),
        ]);
    }

    #[Route('/collection/{id}', name: 'app_event_collection_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/collection/{slug}', name: 'app_event_collection_show_slug', methods: ['GET'])]
    public function show(EventCollection $eventCollection): Response
    {
        $eventCollectionSorted = $this->getNextAndPastEvents($eventCollection);

        return $this->render('event_collection/show.html.twig', [
            'event_collection' => $eventCollection,
            'nextEvents' => $eventCollectionSorted['nextEvents'],
            'pastEvents' => $eventCollectionSorted['pastEvents'],
        ]);
    }

    #[Route('/filter/{parameterName}', name: 'app_event_collection_parameter', methods: ['GET'])]
    #[Route('/filter/{parameterName}/{parameterValue}', name: 'app_event_collection_parameter', methods: ['GET'])]
    public function parameter(string                $parameterName,
                              PublicEventRepository $publicEventRepository,
                              CategoryRepository    $categoryRepository,
                              FilterRepository      $filterRepository,
                              string                $parameterValue = ''): Response
    {
        $eventCollection = new EventCollection();
        $criteriaName = '';


//   придет когда нибудь время и это будет сделано. или нет.
//        switch ($parameterName) {
//            case 0:
//                $criteriaName = 'Возраст 0+';
//                $ageFilter = [0, 6, 12, 18, 21];
//                break;
//            case 6:
//                $criteriaName = 'Возраст 6+';
//                $ageFilter = [6, 12, 18, 21];
//                break;
//            case 12:
//                $criteriaName = 'Возраст 12+';
//                $ageFilter = [12, 18, 21];
//                break;
//            case 18:
//                $criteriaName = 'Возраст 18+';
//                $ageFilter = [18, 21];
//                break;
//            case 21:
//                $criteriaName = 'Возраст 21+';
//                $ageFilter = [21];
//                break;
//        }


        $filter = $filterRepository->findOneBy(['short' => $parameterName]);
        $category = $categoryRepository->findOneBy(['short' => $parameterName]);

        switch (true) {
            case !empty($category):
                $criteriaName = $category->getName();
                $events = $category->getPublicEvents();
                break;
            case !empty($filter):
                $criteriaName = $filter->getName();
                $events = $filter->getEvents();
                break;
            case $parameterName == 'toll':
                $criteria = new Criteria();
                switch ($parameterValue) {
                    case 0:
                        $criteria->where(Criteria::expr()->eq('toll', 0));
                        $criteriaName = 'Бесплатные';
                        break;
                    case 1000:
                        $criteria->where(Criteria::expr()->gt('toll', 0));
                        $criteria->where(Criteria::expr()->lt('toll', 1000));
                        $criteriaName = 'До 1000';
                        break;
                    case 3000:
                        $criteria->where(Criteria::expr()->gt('toll', 1000));
                        $criteria->where(Criteria::expr()->lt('toll', 3000));
                        $criteriaName = 'До 3000';
                        break;
                    case 5000:
                        $criteria->where(Criteria::expr()->gt('toll', 3000));
                        $criteria->where(Criteria::expr()->lt('toll', 5000));
                        $criteriaName = 'До 5000';
                        break;
                    case 5001:
                        $criteria->where(Criteria::expr()->gt('toll', 5000));
                        $criteriaName = 'Больше 5000';
                        break;
                }
                $criteria->orderBy(['startDate' => 'DESC']);
                $events = $publicEventRepository->matching($criteria);
                break;
            default:
                break;
        }

        $eventCollection->setName($criteriaName);

        foreach ($events as $event) {
            $eventCollection->addEvent($event);
        }

        $eventCollectionSorted = $this->getNextAndPastEvents($eventCollection);

        return $this->render('event_collection/show.html.twig', [
            'event_collection' => $eventCollection,
            'nextEvents' => $eventCollectionSorted['nextEvents'],
            'pastEvents' => $eventCollectionSorted['pastEvents']
        ]);
    }

    public function getNextAndPastEvents(EventCollection $eventCollection): array
    {
        $nextEvents = $pastEvents = [];

        foreach ($eventCollection->getEvents() as $event) {
            if ($event->getEndDate(true) > (new DateTime())) {
                $nextEvents[] = $event;
            } else {
                $pastEvents[] = $event;
            }
        }

        usort($nextEvents, fn($a, $b) => $a->getStartDate() > $b->getStartDate());
        usort($pastEvents, fn($a, $b) => $a->getStartDate() > $b->getStartDate());

        return ['nextEvents' => $nextEvents,
            'pastEvents' => $pastEvents
        ];
    }

}
