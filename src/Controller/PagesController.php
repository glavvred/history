<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Organisation;
use App\Entity\Region;
use App\Form\UserType;
use App\Security\EmailVerifier;
use DateMalformedStringException;
use DateTime;
use App\Entity\User;
use App\Entity\PublicEvent;
use App\Entity\EventCollection;
use App\Repository\PublicEventRepository;
use App\Repository\OrganisationRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Cache(maxage: 3600, public: true, mustRevalidate: true)]
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
                          PaginatorInterface     $paginator,
                          Request                $request
    ): Response
    {
        // Получаем регион из сессии или дефолтный
        $region = $this->getRegion($session);

        // Устанавливаем даты на текущую неделю
        $startDate = new DateTime();
        $endDate = (new DateTime())->modify('+7 day');

        // Формируем и отображаем страницу
        return $this->renderIndexPage($session, $entityManager, $paginator, $request, $region, $startDate, $endDate);
    }

    #[Route('/{region}/events/{dateRange}', name: 'app_index_with_params', options: ['sitemap' => true])]
    public function indexWithParams(
        SessionInterface       $session,
        Request                $request,
        EntityManagerInterface $entityManager,
        PaginatorInterface     $paginator,
        string                 $region,
        string                 $dateRange
    ): Response
    {
        // Проверяем, соответствует ли dateRange допустимому формату
        if (!preg_match('/^\d{4}-\d{2}-\d{2}(?:_\d{4}-\d{2}-\d{2})?$/', $dateRange)) {
            // Если формат неверный, перенаправляем на дефолтный роут
            return $this->redirectToRoute('app_index');
        }

        if (strpos($dateRange, '_') !== false) {
            [$startDateStr, $endDateStr] = explode('_', $dateRange);
        } else {
            // Если не содержит, то это одна дата
            $startDateStr = $dateRange;
            $endDateStr = $dateRange;
        }
        $startDate = DateTime::createFromFormat('Y-m-d', $startDateStr);
        $endDate = DateTime::createFromFormat('Y-m-d', $endDateStr);

        if (!$startDate || !$endDate) {
            return $this->redirectToRoute('app_index');
        }

        $regionEntity = $entityManager->getRepository(Region::class)->findOneBy(['slug' => ucfirst($region)]);
        if (!$regionEntity) {
            $regionEntity = $this->getRegion($session);
        }

        $session->set('location', $regionEntity->getName());
        $session->set('location_admin_name', $regionEntity->getAdminName());
        $session->set('location_id', $regionEntity->getId());
        $session->set('location_slug', $regionEntity->getSlug());
        $session->set('coordinates', $regionEntity->getLng() . ',' . $regionEntity->getLat());

        return $this->renderIndexPage($session, $entityManager, $paginator, $request, $regionEntity, $startDate, $endDate);
    }

    private function renderIndexPage(SessionInterface       $session,
                                     EntityManagerInterface $entityManager,
                                     PaginatorInterface     $paginator,
                                     Request                $request,
                                     Region                 $region,
                                     DateTime               $startDate,
                                     DateTime               $endDate
    ): Response
    {
        $regionWithChildren = $entityManager->getRepository(Region::class)->getChildren($region);
        $eventsQuery = $entityManager->getRepository(PublicEvent::class)->getQueryByCriteria($regionWithChildren, $startDate, $endDate);
        $pagination = $paginator->paginate($eventsQuery, 1, 8);

        $canLoadMore = null;
        if ($pagination->getTotalItemCount() > 8) {
            $canLoadMore = true;
        }

        $eventCollectionsTop = $this->entityManager->getRepository(EventCollection::class)->findBy(['mainPage' => true]);
        $eventCollectionsBottom = $this->entityManager->getRepository(EventCollection::class)->findBy(['bottomPage' => true]);

        $eventIds = [];
        foreach ($pagination->getItems() as $id => $event) {
            if ($id > 7) {
                continue;
            }
            $eventIds[] = $event->getId();
        }

        $session->set('eventsShown', $eventIds);

        if ($request->getHost() != 'gdeistoriya.ru') {
            $canonical = '';
        }

//        $banner = new EventCollection();
//        $banner->setName('Конкурс для организаторов мероприятий');
//        $banner->setMainPhoto('vintage_paper_background.png');
//        $banner->setTitle('title');
//        $banner->setSlug('contest');
//        array_splice($eventCollectionsTop, 3, 0, array($banner));

        return $this->render('pages/index.html.twig', [
            'canonical' => $canonical ?? null,
            'user' => $this->security->getUser(),
            'events' => $pagination,
            'constant_events' => $entityManager->getRepository(PublicEvent::class)->getConstantEventsQuery(),
            'eventCollectionsTop' => $eventCollectionsTop,
            'eventCollectionsBottom' => $eventCollectionsBottom,
            'current_day' => (new DateTime())->format('Y-m-d'),
            'canLoadMore' => $canLoadMore,
            'region' => $region,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    #[Route('/pages/contest', name: 'app_contest', options: ['sitemap' => true])]
    public function contest(Request $request): Response
    {
        if ($request->getHost() != 'gdeistoriya.ru') {
            $canonical = 'pages/contest';
        }

        return $this->render('pages/contest.html.twig', ['canonical' => $canonical ?? null]);
    }

    /**
     * @throws DateMalformedStringException
     */
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

    #[Route('/pages/about_archeo', name: 'app_about_archeo', options: ['sitemap' => true])]
    public function about_archeo(Request $request): Response
    {
        if ($request->getHost() != 'gdeistoriya.ru') {
            $canonical = 'pages/about_archeo';
        }

        return $this->render('pages/about_archeo.html.twig', ['canonical' => $canonical ?? null]);
    }

    #[Route('/pages/welcome', name: 'app_welcome', options: ['sitemap' => true])]
    public function welcome(Request $request): Response
    {
        if ($request->getHost() != 'gdeistoriya.ru') {
            $canonical = 'pages/welcome';
        }

        return $this->render('pages/welcome.html.twig', ['canonical' => $canonical ?? null]);
    }

    #[Route('/pages/about', name: 'app_about', options: ['sitemap' => true])]
    public function about(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->getHost() != 'gdeistoriya.ru') {
            $canonical = 'pages/about';
        }

        $events = $entityManager->getRepository(PublicEvent::class)->count();
        $orgs = $entityManager->getRepository(Organisation::class)->count();

        return $this->render('pages/about.html.twig', [
            'active_events' => $events,
            'active_organisations' => $orgs,
            'canonical' => $canonical ?? null]);
    }

    #[Route('/pages/partners', name: 'app_partners', options: ['sitemap' => true])]
    public function partners(Request $request): Response
    {
        if ($request->getHost() != 'gdeistoriya.ru') {
            $canonical = 'pages/partners';
        }
        return $this->render('pages/partners.html.twig', ['canonical' => $canonical ?? null]);
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

        if ($request->getHost() != 'gdeistoriya.ru') {
            $canonical = 'pages/profile';
        }

        //только админ может редактировать не свои профили
        if (in_array('ADMIN', $loggedUser->getRoles())) {
            if ($loggedUser !== $profileUser) {
                return $this->render('pages/profile.html.twig', [
                    'canonical' => $canonical ?? null,
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
                        Request                $request,
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

    /**
     */
    #[Route('/search', name: 'app_search', options: ['sitemap' => true])]
    public function search(OrganisationRepository $organisationRepository,
                           PublicEventRepository  $publicEventRepository,
                           Request                $request
    ): Response
    {
        $criteria = $request->get('criteria');

        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata(Organisation::class, 'o0_');
        $selectClause = $rsm->generateSelectClause();
        $sql = 'SELECT ' . $selectClause . ', 
                MATCH (o0_.name, o0_.description, o0_.address) AGAINST (:criteria) as score
                FROM organisation AS o0_
                WHERE o0_.verified = 1 
                    AND (
                        MATCH (o0_.name, o0_.description, o0_.address) AGAINST (:criteria) > 0
                        OR JSON_SEARCH(o0_.alternate_names, "one", :criteria) IS NOT NULL
                    )
                ORDER BY score DESC
                LIMIT 8;';

        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('criteria', $criteria);
        $organisations = $query->getResult();


        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata(PublicEvent::class, 'pe');
        $selectClause = $rsm->generateSelectClause();
        $sql = "SELECT " . $selectClause . ", MATCH (pe.name, pe.description, pe.address) AGAINST (:criteria) as score
                FROM public_event AS pe
                WHERE CURRENT_DATE() < DATE_ADD(pe.start_date, INTERVAL pe.duration DAY)
                AND pe.duration != 0
                HAVING score > 0 
                ORDER BY score DESC
                LIMIT 28;";

        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('criteria', $criteria);

        $events = $query->getResult();

        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata(PublicEvent::class, 'pe');
        $selectClause = $rsm->generateSelectClause();
        $sql = "SELECT " . $selectClause . ", MATCH (pe.name, pe.description, pe.address) AGAINST (:criteria) as score
                FROM public_event AS pe
                WHERE CURRENT_DATE() > DATE_ADD(pe.start_date, INTERVAL pe.duration DAY)
                AND pe.duration != 0
                AND MATCH (pe.name, pe.description, pe.address) AGAINST (:criteria) > 0
                HAVING score > 0 
                ORDER BY score DESC
                LIMIT 28;";

        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('criteria', $criteria);
        $pastEvents = $query->getResult();

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