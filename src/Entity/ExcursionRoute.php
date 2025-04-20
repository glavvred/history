<?php

namespace App\Entity;

use App\Repository\ExcursionRouteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExcursionRouteRepository::class)]
class ExcursionRoute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $main_photo = null;

    #[ORM\Column(nullable: true)]
    private ?array $additional_photos = null;

    #[ORM\Column(length: 2048)]
    private ?string $route = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?bool $published = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $shortDescription = null;

    #[ORM\ManyToOne(inversedBy: 'excursionRoutes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Region $region = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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
        return $this->main_photo;
    }

    public function setMainPhoto(string $main_photo): static
    {
        $this->main_photo = $main_photo;

        return $this;
    }

    public function getAdditionalPhotos(): ?array
    {
        return $this->additional_photos;
    }

    public function setAdditionalPhotos(?array $additional_photos): static
    {
        $this->additional_photos = $additional_photos;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(?bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }
}
