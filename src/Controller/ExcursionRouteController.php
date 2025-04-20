<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ExcursionRouteRepository;
use App\Entity\ExcursionRoute;


final class ExcursionRouteController extends AbstractController
{
    #[Route('/routes', name: 'app_routes_list', priority: 10)]
    public function index(ExcursionRouteRepository $excursionRouteRepository): Response
    {
        return $this->render('excursion_route/index.html.twig', [
            'routes_type' => 'Все',
            'routes_collection' => $excursionRouteRepository->findAll(),
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
