<?php

namespace App\Twig;

use App\Entity\Category;
use App\Entity\Organisation;
use App\Entity\OrganisationCategory;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoriesList extends AbstractExtension
{
    private $managerRegistry;

    public function __construct(ManagerRegistry     $managerRegistry,)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('categoriesList', [$this, 'categoriesList']),
            new TwigFunction('organisationCategoriesList', [$this, 'organisationCategoriesList']),
        ];
    }

    public function categoriesList(): array
    {
        return $this->managerRegistry->getRepository(Category::class)->getCategoriesWithEventCount();
    }

    public function organisationCategoriesList(): array
    {
        return $this->managerRegistry->getRepository(OrganisationCategory::class)->getCategoriesWithOrganisationCount();
    }
}