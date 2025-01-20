<?php

namespace App\Twig;

use App\Entity\Category;
use App\Entity\Filter;
use App\Entity\News;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FilterList extends AbstractExtension
{
    private $managerRegistry;

    public function __construct(ManagerRegistry     $managerRegistry,)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('filterList', [$this, 'filterList']),
        ];
    }

    public function filterList(): array
    {
        return $this->managerRegistry->getRepository(Filter::class)->findBy(criteria: [], orderBy: ['id' => 'DESC'] );
    }
}