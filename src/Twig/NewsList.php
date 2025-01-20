<?php

namespace App\Twig;

use App\Entity\Category;
use App\Entity\News;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NewsList extends AbstractExtension
{
    private $managerRegistry;

    public function __construct(ManagerRegistry     $managerRegistry,)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('newsList', [$this, 'newsList']),
        ];
    }

    public function newsList(): array
    {
        return $this->managerRegistry->getRepository(News::class)->findBy(['published' => true], orderBy: ['createdAt' => 'DESC'], limit: 3);
    }
}