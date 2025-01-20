<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity()]
#[ApiResource(
    description: 'Сообщение о новом мероприятии',
    operations: [
        new Post(
            uriTemplate: '/eventReport/new',
            schemes: ['https'],
            shortName: "EventReport",
            description: 'Post new event to moderated list',
            normalizationContext: ['groups' => 'eventreport:item'],
        ),
    ],
    normalizationContext: [
        'skip_null_values' => false
    ],
    order: ['createdAt' => 'DESC'],
    paginationEnabled: true,
    paginationItemsPerPage: 10,
)]
class EventReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'eventReports')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['eventreport:item'])]
    private ?User $reporter = null;

    #[ORM\Column(length: 255)]
    #[Groups(['eventreport:item'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['eventreport:item'])]
    private ?DateTimeInterface $startDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(length: 255)]
    #[Groups(['eventreport:item'])]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['eventreport:item'])]
    private ?string $link = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mainPhoto = null;

    #[ORM\Column(nullable: true)]
    private ?array $additionalPhotos = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $prequisites = null;

    #[ORM\Column(nullable: true)]
    private ?string $toll = null;

    #[ORM\ManyToOne(inversedBy: 'publicEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'publicEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Region $region = null;

    #[ORM\Column(nullable: false)]
    private bool $used = false;

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

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
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

    public function getPrequisites(): ?string
    {
        return $this->prequisites;
    }

    public function setPrequisites(?string $prequisites): static
    {
        $this->prequisites = $prequisites;

        return $this;
    }

    public function getToll(): ?string
    {
        return $this->toll;
    }

    public function setToll(?string $toll): static
    {
        $this->toll = $toll;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

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

    /**
     * @return User|null
     */
    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    /**
     * @param User|null $reporter
     */
    public function setReporter(?User $reporter): void
    {
        $this->reporter = $reporter;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): static
    {
        $this->used = $used;

        return $this;
    }

}
