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

    #[ORM\OneToOne(targetEntity: Organisation::class, inversedBy: 'statistic', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Organisation $managingOrganisation = null;

    #[ORM\Column(name: 'map', nullable: true)]
    private ?int $mapClick = null;

    #[ORM\Column(name: 'org', nullable: true)]
    private ?int $organisationClick = null;

    #[ORM\Column(name: 'button', nullable: true)]
    private ?int $buttonClick = null;


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

    public function getMapClick(): ?string
    {
        return $this->mapClick;
    }

    public function getButtonClick(): ?string
    {
        return $this->buttonClick;
    }

    public function getOrganisationClick(): ?int
    {
        return $this->organisationClick;
    }


    /**
     * @return Organisation|null
     */
    public function getManagingOrganisation(): ?Organisation
    {
        return $this->managingOrganisation;
    }

    public function __toString(): string
    {
        return 'map ';
    }
}
