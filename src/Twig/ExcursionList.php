<?php

namespace App\Twig;

use App\Entity\Category;
use App\Entity\ExcursionRoute;
use App\Entity\Organisation;
use App\Entity\OrganisationCategory;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ExcursionList extends AbstractExtension
{
    private $managerRegistry;

    public function __construct(ManagerRegistry     $managerRegistry,)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('excursionList', [$this, 'excursionList']),
//            new TwigFunction('excursionCategoriesList', [$this, 'excursionCategoriesList']),
        ];
    }

    public function excursionList(): array
    {
        return $this->managerRegistry->getRepository(ExcursionRoute::class)->getList();
    }

}