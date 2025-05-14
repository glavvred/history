<?php

namespace App\Controller;

use App\Entity\EventCollection;
use App\Entity\ExcursionRouteReport;
use App\Entity\User;
use App\Form\ExcursionReportType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ExcursionRouteRepository;
use App\Entity\ExcursionRoute;
use Symfony\Component\String\Slugger\SluggerInterface;


final class ExcursionRouteController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->entityManager = $managerRegistry->getManager();
    }

    #[Route('/routes', name: 'app_routes_list', priority: 10)]
    public function index(ExcursionRouteRepository $excursionRouteRepository): Response
    {
        return $this->render('excursion_route/map.html.twig', [
            'routes_type' => 'Все',
            'routes_collection' => $excursionRouteRepository->findAll(),
        ]);
    }

    #[Route('/routes/report', name: 'app_routes_report', methods: ['GET', 'POST'], priority: 100)]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security, SluggerInterface $slugger): Response
    {
        $routeReport = new ExcursionRouteReport();
        $form = $this->createForm(ExcursionReportType::class, $routeReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $mainPhoto */
            $mainPhoto = $form->get('mainPhotoFile')->getData();

            if ($mainPhoto) {
                $originalFilename = pathinfo($mainPhoto->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mainPhoto->guessExtension();

                try {
                    $mainPhoto->move('upload/images', $newFilename);
                } catch (FileException $e) {
                    var_dump($e->getMessage());
                }

                $routeReport->setMainPhoto($newFilename);
            }

            /** @var UploadedFile $additionalPhoto */
            $additionalPhotos = $form->get('additionalPhotosFiles')->getData();

            if (!empty($additionalPhotos)) {
                $additionalPhotoLinks = [];

                foreach ($additionalPhotos as $additionalPhoto) {
                    $originalFilename = pathinfo($additionalPhoto->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $additionalPhoto->guessExtension();

                    try {
                        $additionalPhoto->move('upload/images', $newFilename);
                    } catch (FileException $e) {
                        var_dump($e->getMessage());
                    }
                    $additionalPhotoLinks[] = $newFilename;
                }
                $routeReport->setAdditionalPhotos($additionalPhotoLinks);
            }

            $routeScript = $form->get('route')->getData();
            if (!empty($routeScript)) {
                //$scriptTag = '<script type="text/javascript" charset="utf-8" async
                // src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=mymaps%3AU-865hu9h3Nx3SGY7SXxSbST0KJBh-NP&amp;width=500&amp;height=500&amp;lang=ru_RU&amp;scroll=true"></script>';

                // Извлекаем URL из атрибута src
                preg_match('/src="([^"]+)"/', $routeReport->getRoute(), $matches);
                $url = html_entity_decode($matches[1]);

                // Извлекаем значение параметра um
                parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);
                $um = $queryParams['um'];
                $routeReport->setRoute($um);
            }


            if (true === $this->isGranted('ROLE_USER')) {
                $reporter = $security->getUser();
            } else {
                $reporter = $entityManager->getRepository(User::class)->findOneBy(['email' => 'anonymous@reporter']);
            }
            $routeReport->setReporter($reporter);
            $entityManager->persist($routeReport);
            $entityManager->flush();

            return $this->redirectToRoute('app_route_report_thanks', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('excursion_route/new.html.twig', [
            'user' => $this->getUser(),
            'event_report' => $routeReport,
            'form' => $form,
        ]);
    }


    #[Route('/routes/report/thanks', name: 'app_route_report_thanks', methods: ['GET'])]
    public function thanks(): Response
    {
        return $this->render('excursion_route/thanks.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    #[Route('/routes/{slug}', name: 'app_routes_show_slug', priority: 100)]
    public function showSlug(#[MapEntity(mapping: ['slug' => 'slug'])] ExcursionRoute $excursionRoute): Response
    {
        return $this->render('excursion_route/show.html.twig', [
            'route' => $excursionRoute,
        ]);
    }

}
