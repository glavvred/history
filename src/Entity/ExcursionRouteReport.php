<?php

namespace App\Entity;

use App\Repository\ExcursionRouteReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ExcursionRouteReportRepository::class)]
class ExcursionRouteReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'routeReports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reporter = null;

    #[ORM\ManyToOne(inversedBy: 'routeReports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Region $region = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;
    #[ORM\Column(type: Types::TEXT, length: 255)]
    private ?string $shortDescription = null;

    #[ORM\Column(length: 255)]
    private ?string $mainPhoto = null;

    #[ORM\Column(nullable: true)]
    private ?array $additionalPhotos = null;

    #[ORM\Column(length: 255)]
    private ?string $route = null;

    #[ORM\Column(nullable: false)]
    private bool $used = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coordinates = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMainPhoto(): ?string
    {
        return $this->mainPhoto;
    }

    public function setMainPhoto(string $mainPhoto): static
    {
        $this->mainPhoto = $mainPhoto;

        return $this;
    }

    public function getAdditionalPhotos(): ?array
    {
        return $this->additionalPhotos;
    }

    public function setAdditionalPhotos(?array $additionalPhotos): static
    {
        $this->additionalPhotos = $additionalPhotos;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): void
    {
        $this->used = $used;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): void
    {
        $this->reporter = $reporter;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): void
    {
        $this->region = $region;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }

    public function getCoordinates(): ?string
    {
        return $this->coordinates;
    }

    public function setCoordinates(?string $coordinates): static
    {
        $this->coordinates = $coordinates;

        return $this;
    }
}
