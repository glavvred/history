<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\PublicEvent;
use App\Repository\CategoryRepository;
use App\Repository\PublicEventRepository;
use App\Repository\RegionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class PublicEventController extends AbstractController
{
    #[Route('/event/geo', name: 'app_events_geo', methods: ['GET'])]
    public function geo(RegionRepository $regionRepository): Response
    {
        $regions = $regionRepository->createQueryBuilder('r')->getQuery()->getResult();
        return $this->json($regions, 200, [], ['groups' => 'region']);
    }

    #[Route('/event/best', name: 'app_public_event_best', methods: ['GET'])]
    public function best(PublicEventRepository $publicEventRepository, EntityManagerInterface $entityManager): Response
    {
        $navbarSecond = $entityManager->getRepository(Category::class)->getCategoriesWithEventCount();

        $events = $publicEventRepository->createQueryBuilder('pe')
            ->where('pe.views is not null')
            ->andWhere('DATE_ADD(pe.startDate,pe.duration,\'day\')  > :date')
            ->setParameter('date', new DateTime())
            ->orderBy('pe.views', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();

        $pastEvents = $publicEventRepository->createQueryBuilder('pe')
            ->where('pe.views is not null')
            ->andWhere('DATE_ADD(pe.startDate,pe.duration,\'day\')  < :date')
            ->setParameter('date', new DateTime())
            ->orderBy('pe.views', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
        return $this->render('public_event/list.html.twig', [
            'event_type' => 'best',
            'navbar_second' => $navbarSecond,
            'events' => $events,
            'pastEvents' => $pastEvents
        ]);
    }


    #[Route('/event/{id}', name: 'app_public_event_show', methods: ['GET'])]
    public function show(PublicEvent $publicEvent, PublicEventRepository $publicEventRepository, EntityManagerInterface $entityManager): Response
    {
        $publicEvent->setViews($publicEvent->getViews() + 1);
        $entityManager->persist($publicEvent);
        $entityManager->flush();

        $relatedEvents = $publicEventRepository
            ->createQueryBuilder('pe')
            ->where('pe.category = :category')
            ->setParameter('category', $publicEvent->getCategory())
            ->andWhere('DATE_ADD(pe.startDate,pe.duration,\'day\')  > :date')
            ->setParameter('date', new DateTime())
            ->orderBy('pe.startDate', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();

        return $this->render('public_event/show.html.twig', [
            'related_events' => $relatedEvents,
            'public_event' => $publicEvent,
            'statistics' => $publicEvent->getStatistic()
        ]);
    }


    #[Route('/{category}/{slug}', name: 'app_public_event_show_slug', methods: ['GET'])]
    public function showBySlug(string $category,
                               string $slug,
                               CategoryRepository $categoryRepository,
                               PublicEventRepository $publicEventRepository,
                               EntityManagerInterface $entityManager): Response
    {
        /** @var Category $category */
        $category = $categoryRepository->findByShort($category);
        if (empty($category)){
            throw $this->createNotFoundException();
        }

        $publicEvent = $publicEventRepository
            ->createQueryBuilder('pe')
            ->where('pe.category = :category')
            ->setParameter('category', $category)
            ->andWhere('pe.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('pe.startDate', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getOneOrNullResult();

        if (empty($publicEvent)){
            throw $this->createNotFoundException();
        }


        $publicEvent->setViews($publicEvent->getViews() + 1);
        $entityManager->persist($publicEvent);
        $entityManager->flush();

        $relatedEvents = $publicEventRepository
            ->createQueryBuilder('pe')
            ->where('pe.category = :category')
            ->setParameter('category', $category)
            ->andWhere('DATE_ADD(pe.startDate,pe.duration,\'day\')  > :date')
            ->setParameter('date', new DateTime())
            ->orderBy('pe.startDate', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();

        return $this->render('public_event/show.html.twig', [
            'related_events' => $relatedEvents,
            'public_event' => $publicEvent,
            'statistics' => $publicEvent->getStatistic()
        ]);
    }


    #[Route('/event/list/{category}', name: 'app_event_list', requirements: ['category' => '\w+'], methods: ['GET'])]
    public function list(SessionInterface       $session,
                         PagesController        $pagesController,
                         PublicEventRepository  $publicEventRepository,
                         EntityManagerInterface $entityManager,
                         PaginatorInterface     $paginator,
                         ?string                $category = null): Response
    {
        $navbarSecond = $entityManager->getRepository(Category::class)->getCategoriesWithEventCount();

        if (!empty($category)) {
            $typeFound = $entityManager->getRepository(Category::class)->findOneBy(['short' => $category]);

            if (!$typeFound) {
                return $this->render('public_event/list.html.twig', [
                    'flash' => 'У нас пока нет такого типа мероприятий. Возвращаем вас в общий список',
                    'event_type' => 'Все',
                    'navbar_second' => $navbarSecond,
                    'events' => $publicEventRepository->getUpcomingEvents(null, 8),
                    'pastEvents' => $publicEventRepository->getPastEvents(null, 8),
                ]);
            }

            $eventIds = [];
            $region = $pagesController->getRegion($session);

            $startDate = new DateTime();
            $endDate = (new DateTime())->modify('+7 day');

            $eventsQuery = $entityManager->getRepository(PublicEvent::class)->getQueryByCriteria($region, $startDate, $endDate, null, null, $typeFound);
            $pagination = $paginator->paginate($eventsQuery, 1, 8);

            $canLoadMore = null;
            if ($pagination->getTotalItemCount() > 8) {
                $canLoadMore = true;
            }

            foreach ($pagination->getItems() as $id => $event) {
                if ($id > 7) {
                    continue;
                }
                $eventIds[] = $event->getId();
            }

            $eventTags = [
                [
                    'short' => 'hema',
                    'name' => 'HEMA'
                ],
                [
                    'short' => 'hmb',
                    'name' => 'ИСБ'
                ],
                [
                    'short' => 'msf',
                    'name' => 'СМБ'
                ]
            ];


            $session->set('eventsShown', $eventIds);

            return $this->render('public_event/list.html.twig', [
                'eventTags' => $eventTags,
                'canLoadMore' => $canLoadMore,
                'event_type' => $typeFound->getName(),
                'navbar_second' => $navbarSecond,
                'events' => $pagination->getItems(),
                'pastEvents' => $publicEventRepository->getPastEvents($typeFound),
                'categoryObject' => $typeFound,
                'category' => $category
            ]);
        }

        return $this->render('public_event/list.html.twig', [
            'event_type' => 'Все',
            'navbar_second' => $navbarSecond,
            'events' => $publicEventRepository->getUpcomingEvents(null, 8),
            'pastEvents' => $publicEventRepository->getPastEvents(null, 8),
        ]);
    }

}
