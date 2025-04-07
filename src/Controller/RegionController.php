<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

#[Cache(maxage: 3600, public: true, mustRevalidate: true)]
class RegionController extends AbstractController
{
    #[Route('/region/set', name: 'app_region_set', methods: ['POST'])]
    public function setLocation(Request          $request,
                                SessionInterface $session,
                                ManagerRegistry  $managerRegistry,
                                Security         $security): JsonResponse
    {
        $location = (int)$request->getPayload()->get('location');
        $location = $managerRegistry->getRepository(Region::class)->find($location);
        if (empty($location)) {
            $location = $managerRegistry->getRepository(Region::class)->find(2);
        }

        $session->set('location', $location->getName());
        $session->set('location_admin_name', $location->getAdminName());
        $session->set('location_id', $location->getId());
        $session->set('location_slug', $location->getSlug());
        $session->set('coordinates', $location->getLng() . ',' . $location->getLat());


        /** @var User $currentUser */
        $currentUser = $security->getUser();

        if (!empty($currentUser)) {
            $currentUser->setRegion($location);
            $managerRegistry->getManager()->persist($currentUser);
            $managerRegistry->getManager()->flush();
        }

        return new JsonResponse(
            [
                'location' => $location->getName(),
                'location_id' => $location->getId(),
                'location_admin_name' => $location->getAdminName(),
                'location_slug' => $location->getSlug(),
                'coordinates' => $location->getLng() . ',' . $location->getLat()
            ]);
    }

    #[Route('/region/get', name: 'app_region_get')]
    public function getLocation(SessionInterface $session): Response
    {
        return new JsonResponse(
            [
                'location' => $session->get('location', 'not set'),
                'location_id' => $session->get('location_id', 'not set'),
                'location_admin_name' => $session->get('location_admin_name', 'not set'),
                'location_slug' => $session->get('location_slug', 'msk'),
                'coordinates' => $session->get('coordinates', 'not set'),
            ]);
    }
}
