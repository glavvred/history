<?php

namespace App\Entity;

use App\Repository\PublicEventStatisticRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicEventStatisticRepository::class)]
class PublicEventStatistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'statistic', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PublicEvent $publicEvent = null;

    #[ORM\Column(nullable: true)]
    private ?int $map = null;

    #[ORM\Column(nullable: true)]
    private ?int $organisation = null;

    #[ORM\Column(nullable: true)]
    private ?int $button = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicEvent(): ?PublicEvent
    {
        return $this->publicEvent;
    }

    public function setPublicEvent(PublicEvent $publicEvent): static
    {
        $this->publicEvent = $publicEvent;

        return $this;
    }

    public function getMap(): ?string
    {
        return $this->map;
    }

    public function setMap(?int $map): static
    {
        $this->map = $map;

        return $this;
    }

    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    public function getManagingOrganisation(): ?Organisation
    {
        return $this->publicEvent->getOrganisation();
    }

    public function setOrganisation(?int $organisation): static
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getButton(): ?string
    {
        return $this->button;
    }

    public function setButton(?int $button): static
    {
        $this->button = $button;

        return $this;
    }

    public function __toString(): string
    {
        return 'map'. $this->getMap(). ' ';
    }
}
