<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Region;
use App\Form\UserType;
use App\Security\EmailVerifier;
use DateTime;
use App\Entity\User;
use App\Entity\PublicEvent;
use App\Entity\EventCollection;
use App\Repository\PublicEventRepository;
use App\Repository\OrganisationRepository;
use Doctrine\DBAL\Query;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class PagesController extends AbstractController
{

    public function __construct(private readonly Security               $security,
                                private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function getRegion(SessionInterface $session): Region
    {
        /** @var User $user */
        $user = $this->security->getUser();
        //локация в сессии
        if (!empty($session->get('location'))) {
            $region = $this->entityManager->getRepository(Region::class)
                ->findOneBy(criteria: ['name' => $session->get('location')]);
        } else {
            if (!empty($user)) {
                //локация в юзере
                if (!empty($user->getRegion())) {
                    $region = $user->getRegion();
                }
            }
        }
        //дефолт москва
        if (empty($region)) {
            $region = $this->entityManager->getRepository(Region::class)->find(2);
        }

        return $region;
    }

    #[Route('/', name: 'app_index', options: ['sitemap' => true])]
    public function index(SessionInterface       $session,
                          EntityManagerInterface $entityManager,
                          PaginatorInterface     $paginator): Response
    {
        $eventIds = [];
        $region = $this->getRegion($session);

        /** @var User $user */
        $user = $this->security->getUser();

        $startDate = new DateTime();
        $endDate = (new DateTime())->modify('+7 day');

        $eventsQuery = $entityManager->getRepository(PublicEvent::class)->getConstantEventsQuery();
        $constantEvents = $paginator->paginate($eventsQuery, 1, 8);

        $regionWithChildren = $entityManager->getRepository(Region::class)->getChildren($region);

        $eventsQuery = $entityManager->getRepository(PublicEvent::class)->getQueryByCriteria($regionWithChildren, $startDate, $endDate);
        $pagination = $paginator->paginate($eventsQuery, 1, 8);

        $canLoadMore = null;
        if ($pagination->getTotalItemCount() > 8) {
            $canLoadMore = true;
        }

        $eventCollectionsTop = $entityManager->getRepository(EventCollection::class)->findBy(['mainPage' => true]);
        $eventCollectionsBottom = $entityManager->getRepository(EventCollection::class)->findBy(['bottomPage' => true]);

        foreach ($pagination->getItems() as $id => $event) {
            if ($id > 7) {
                continue;
            }
            $eventIds[] = $event->getId();
        }

        $session->set('eventsShown', $eventIds);

        return $this->render('pages/index.html.twig', [
            'user' => $user,
            'events' => $pagination,
            'constant_events' => $constantEvents,
            'eventCollectionsTop' => $eventCollectionsTop,
            'eventCollectionsBottom' => $eventCollectionsBottom,
            'current_day' => (new DateTime())->format('Y-m-d'),
            'canLoadMore' => $canLoadMore
        ]);
    }

    #[Route('/loadmore', name: 'app_load_more')]
    public function loadMore(SessionInterface       $session,
                             Request                $request,
                             EntityManagerInterface $entityManager,
                             PaginatorInterface     $paginator
    ): JsonResponse
    {
        $startDate = $endDate = new DateTime();
        $region = $this->getRegion($session);
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');
        $page = $request->get('page');
        $category = $request->get('category');
        $category = $entityManager->getRepository(Category::class)->findOneBy(['short' => $category]);
        $filters = json_decode($request->get('f'));

        if (!empty($dateFrom)) {
            $startDate = DateTime::createFromFormat('Y-m-d', $dateFrom)->modify('today');
        }

        if (!empty($dateTo)) {
            $endDate = DateTime::createFromFormat('Y-m-d', $dateTo)->modify('today');
        }

        $eventsShown = [];
        if ($page > 1) {
            $eventsShown = $session->get('eventsShown');
        }

        $regionWithChildren = $entityManager->getRepository(Region::class)->getChildren($region);

        /** @var Query $eventsQuery */
        $eventsQuery = $entityManager->getRepository(PublicEvent::class)->getQueryByCriteria($regionWithChildren, $startDate, $endDate, $filters, $eventsShown, $category);
        $paginated = $paginator->paginate($eventsQuery, $page, 8);

        $total = ceil($paginated->getTotalItemCount() / 8);
        $items = $paginated->getItems();

        foreach ($items as $id => $event) {
            if ($id > 7) {
                continue;
            }
            $eventsShown[] = $event->getId();
        }
        $session->set('eventsShown', $eventsShown);

        return new JsonResponse(['data' => $items,
            'lastPage' => $total,
            'page' => (int)$page,
//            '_comment' => $eventsQuery->getSql(),
            'region' => $region->getId(),
            'startdate' => $startDate,
            'enddate' => $endDate,
            'eventsShown' => $eventsShown
        ]);
    }

    #[Route('/pages/about', name: 'app_about', options: ['sitemap' => true])]
    public function about(): Response
    {
        return $this->render('pages/about.html.twig', []);
    }

    #[Route('/pages/partners', name: 'app_partners', options: ['sitemap' => true])]
    public function partners(): Response
    {
        return $this->render('pages/partners.html.twig', []);
    }

    #[Route('/profile/{profileUser}', name: 'app_profile', requirements: ['userId' => '\d+'], methods: ['GET', 'POST'])]
    public function profile(User                   $profileUser,
                            Request                $request,
                            Security               $security,
                            SluggerInterface       $slugger,
                            EntityManagerInterface $entityManager
    ): Response
    {
        /** @var User $loggedUser */
        $loggedUser = $security->getUser();
        $profileUserUnmodified = clone $profileUser;
        $form = $this->createForm(UserType::class, $profileUser);
        $form->handleRequest($request);

        //только админ может редактировать не свои профили
        if (in_array('ADMIN', $loggedUser->getRoles())) {
            if ($loggedUser !== $profileUser) {
                return $this->render('pages/profile.html.twig', [
                    'form' => $form,
                    'edit' => false,
                    'user' => $loggedUser,
                    'profile_user' => $profileUser,
                    'controller_name' => 'PagesController',
                ]);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $mainPhoto */
            $mainPhoto = $form->get('avatarFile')->getData();
            if ($mainPhoto) {
                $originalFilename = pathinfo($mainPhoto->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mainPhoto->guessExtension();

                try {
                    $mainPhoto->move('upload/images', $newFilename);
                } catch (FileException $e) {
                    var_dump($e->getMessage());
                }

                $profileUser->setAvatar($newFilename);
            }

            $email = $form->get('email')->getData();
            if (!empty($email)) {
                if ($email != $profileUserUnmodified->getEmail()) {
                    $userExists = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                    if (!empty($userExists)) {
                        $form
                            ->get('email')
                            ->addError(new FormError('Пользователь с таким адресом почты уже существует'));

                        return $this->render('pages/profile.html.twig', [
                            'form' => $form,
                            'user' => $loggedUser,
                            'profile_user' => $profileUser,
                            'controller_name' => 'PagesController',
                        ]);
                    }
                    $profileUser->setEmail($email);
                }
            }

            $username = $form->get('name')->getData();
            if (!empty($username) && $username != $profileUser->getName()) {
                $userExists = $entityManager->getRepository(User::class)->findOneBy(['name' => $username]);
                if (!empty($userExists)) {
                    $form->get('name')
                        ->addError(new FormError('Пользователь с таким логином уже существует'));

                    return $this->render('pages/profile.html.twig', [
                        'form' => $form,
                        'user' => $loggedUser,
                        'profile_user' => $profileUser,
                        'controller_name' => 'PagesController',
                    ]);
                }
                $profileUser->setName($username);
            }

            $newsletter = $form->get('newsletter')->getData();
            if (!empty($newsletter)) {
                $profileUser->setNewsletter(true);
            } else {
                $profileUser->setNewsletter(false);
            }

            $entityManager->persist($profileUser);
            $entityManager->flush();

            return $this->render('pages/profile.html.twig', [
                'form' => $form,
                'message' => 'Успешно изменен профиль',
                'user' => $loggedUser,
                'profile_user' => $profileUser,
                'controller_name' => 'PagesController',
            ]);
        }

        return $this->render('pages/profile.html.twig', [
            'form' => $form,
            'user' => $loggedUser,
            'profile_user' => $profileUser,
            'controller_name' => 'PagesController',
        ]);
    }

    #[Route('/map/{type}', name: 'app_map', options: ['sitemap' => true])]
    public function map(OrganisationRepository $organisationRepository,
                        EntityManagerInterface $entityManager,
                        string                 $type = 'all'): Response
    {
        $user = $this->getUser();
        $eventCollectionsBottom = $entityManager->getRepository(EventCollection::class)->findBy(['bottomPage' => true]);

        return $this->render('pages/map.html.twig', [
            'user' => $user,
            'organisations' => $organisationRepository->findBy(criteria: ['verified' => true], orderBy: ['id' => 'DESC']),
            'controller_name' => 'PagesController',
            'eventCollectionsBottom' => $eventCollectionsBottom,
        ]);
    }

    #[Route('/search', name: 'app_search', options: ['sitemap' => true])]
    public function search(OrganisationRepository $organisationRepository,
                           PublicEventRepository  $publicEventRepository,
                           Request                $request
    ): Response
    {
        $criteria = $request->get('criteria');

        $organisations = $organisationRepository->createQueryBuilder('o')
            ->where('o.verified = true')
            ->andWhere('lower(o.name) LIKE lower(:name) OR lower(o.short_description) LIKE lower(:name)')
            ->setParameter('name', '%' . $criteria . '%')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        $events = $publicEventRepository->createQueryBuilder('pe')
            ->where('pe.organisation IN (:organisations)')
            ->orWhere('lower(pe.name) LIKE lower(:name)')
            ->orWhere('lower(pe.shortDescription) LIKE lower(:name)')
            ->orWhere('lower(pe.description) LIKE lower(:name)')
            ->andWhere("CURRENT_DATE() < DATE_ADD(pe.startDate, pe.duration, 'day')")
            ->andWhere('pe.duration != 0')
            ->setParameter('name', '%' . $criteria . '%')
            ->setParameter('organisations', $organisations)
            ->orderBy('pe.createdAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        $pastEvents = $publicEventRepository->createQueryBuilder('pe')
            ->where('pe.organisation IN (:organisations)')
            ->orWhere('lower(pe.name) LIKE lower(:name)')
            ->orWhere('lower(pe.shortDescription) LIKE lower(:name)')
            ->orWhere('lower(pe.description) LIKE lower(:name)')
            ->andWhere("CURRENT_DATE() > DATE_ADD(pe.startDate, pe.duration, 'day')")
            ->andWhere('pe.duration != 0')
            ->setParameter('name', '%' . $criteria . '%')
            ->setParameter('organisations', $organisations)
            ->orderBy('pe.createdAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();


        return $this->render('pages/search.html.twig', [
            'criteria' => $criteria,
            'organisations' => $organisations,
            'events' => $events,
            'pastEvents' => $pastEvents,
        ]);
    }


    private function generateDateInterval(int $plusMinus): array
    {
        $dates = [];
        $ru_weekdays = [1 => 'Пн', 2 => 'Вт', 3 => 'Ср', 4 => 'Чт', 5 => 'Птн', 6 => 'Сб', 7 => 'Вс'];

        $currentDate = new DateTime();
        $startDate = clone $currentDate;
        $endDate = clone $currentDate;

        $startDate->modify("-$plusMinus days");
        $endDate->modify("+$plusMinus days");

        while ($startDate <= $endDate) {

            $dates[] = [
                'date' => $startDate->format('Y-m-d'),
                'day' => $startDate->format('d'),
                'day_of_week' => $ru_weekdays[date('N', strtotime($startDate->format('Y-m-d')))]
            ];
            $startDate->modify('+1 day');
        }

        return $dates;
    }
}
