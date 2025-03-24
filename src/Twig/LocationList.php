<?php

namespace App\Twig;

use App\Entity\Region;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocationList extends AbstractExtension
{
    private $managerRegistry;
    private $requestStack;
    private $security;
    private $serializer;

    public function __construct(ManagerRegistry     $managerRegistry, RequestStack $requestStack, Security $security,
                                SerializerInterface $serializer)
    {
        $this->managerRegistry = $managerRegistry;
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->serializer = $serializer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('locationList', [$this, 'locationList']),
            new TwigFunction('locationCurrent', [$this, 'locationCurrent']),
        ];
    }

    public function locationList($asJson = false): string|array
    {
        $nestedRegions = $this->managerRegistry->getRepository(Region::class)->getNestedRegions();

        if ($asJson) {
            return $this->serializer->serialize(
                $nestedRegions,
                format: 'json',
                context: ['groups' => 'region']
            );
        }

        return $nestedRegions;
    }

    public function locationCurrent(): array
    {
        // Получаем текущий запрос
        $request = $this->requestStack->getCurrentRequest();
        $pathInfo = $request->getPathInfo(); // Получаем путь URL, например "/moscow/events/2023-10-01"

        preg_match('/^\/([^\/]+)\/events\/(.+)/', $pathInfo, $matches);
        $regionFromUrl = $matches[1] ?? null;

        // Если регион найден в URL, используем его
        if ($regionFromUrl) {
            $regionFromUrl = urldecode($regionFromUrl); // Декодируем URL-encoded строку
            $regionEntity = $this->managerRegistry->getRepository(Region::class)->findOneBy(['slug' => $regionFromUrl]);

            if ($regionEntity) {
                return [
                    'refresh' => false,
                    'location_id' => $regionEntity->getId(),
                    'location_slug' => $regionEntity->getSlug(),
                    'location' => $regionEntity->getName(),
                    'location_admin_name' => $regionEntity->getAdminName(),
                    'coordinates' => $regionEntity->getLng() . ', ' . $regionEntity->getLat(),
                ];
            }
        }

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        if (!empty($currentUser)) {
            if (!empty($currentUser->getRegion())) {
                return [
                    'refresh' => false,
                    'location_id' => $currentUser->getRegion()->getId(),
                    'location' => $currentUser->getRegion()->getName(),
                    'location_slug' => $currentUser->getRegion()->getSlug(),
                    'location_admin_name' => $currentUser->getRegion()->getAdminName(),
                    'coordinates' => $currentUser->getRegion()->getLng().','. $currentUser->getRegion()->getLat(),
                ];
            }
        }

        //пытаемся найти локацию из сессии
        $location = $this->requestStack->getSession()->get('location');
        $location_id = $this->requestStack->getSession()->get('location_id');
        $location_slug = $this->requestStack->getSession()->get('location_slug');
        $locationAdmin = $this->requestStack->getSession()->get('location_admin_name');

        if (empty($location)) {
            //нигде нет, пытаемся получить из браузера
            return [
                'refresh' => true,
                'location_id' => 2,
                'location' => 'Москва',
                'location_slug' => 'msk',
                'location_admin_name' => 'в Москве',
                'coordinates' => '55.76, 37.64'
            ];
        }

        $locationCoordinates = $this->managerRegistry->getRepository(Region::class)->findOneBy(criteria: ['name' => $location]);

        return [
            'refresh' => false,
            'location' => $location,
            'location_id' => $location_id,
            'location_slug' => $location_slug,
            'location_admin_name' => $locationAdmin,
            'coordinates' => $locationCoordinates ? $locationCoordinates->getLng().', '. $locationCoordinates->getLat() : '55.76, 37.64'
        ];
    }
}