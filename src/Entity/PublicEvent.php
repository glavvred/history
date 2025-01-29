<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\QueryParameter;
use App\Repository\PublicEventRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use JsonSerializable;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: PublicEventRepository::class)]
#[ApiResource(
    description: 'Мероприятия',
    operations: [
        new Get(
            uriTemplate: '/event/{id}',
            requirements: ['id' => '\d+'],
            schemes: ['https'],
            shortName: "Events",
            description: 'Get event by id',
            normalizationContext: ['groups' => 'event:item'],
        ),
        new GetCollection(uriTemplate: '/events',
            schemes: ['https'],
            shortName: "Events",
            description: 'Get all events',
            normalizationContext: ['groups' => 'event:list'],

        )
    ],
    normalizationContext: [
        'skip_null_values' => false
    ],
    order: ['createdAt' => 'DESC'],
    paginationEnabled: true,
    paginationItemsPerPage: 10,
)]
class PublicEvent implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'publicEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:list', 'event:item'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['event:list', 'event:item'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    private ?DateTimeInterface $startDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(nullable: true)]
    private ?bool $constant = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    private ?string $vk = null;

    #[ORM\Column(nullable: true)]
    private ?string $tg = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, nullable: false)]
    private ?string $shortDescription = null;

    #[ORM\Column(length: 255)]
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

    /**
     * @var Collection<int, EventCollection>
     */
    #[ORM\ManyToMany(targetEntity: EventCollection::class, mappedBy: 'events')]
    private Collection $collections;

    #[ORM\ManyToOne(inversedBy: 'publicEvents')]
    private ?Organisation $organisation = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;


    /**
     * @var Collection<int, Filter>
     */
    #[ORM\ManyToMany(targetEntity: Filter::class, inversedBy: 'events')]
    private Collection $filter;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url_text = null;

    #[ORM\Column(nullable: true)]
    private ?int $views = null;

    #[ORM\Column(nullable: true)]
    private ?int $rating = null;

    #[ORM\Column(nullable: true)]
    private ?array $reviews = null;

    #[ORM\OneToOne(mappedBy: 'publicEvent', cascade: ['persist', 'remove'])]
    private ?PublicEventStatistic $statistic = null;

    /**
     * @var string|null
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    #[ORM\Column(length: 128, unique: true, nullable: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private $slug;

    public function __construct()
    {
        $this->collections = new ArrayCollection();
        $this->filter = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

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
        if (empty($this->duration))
            return 0;
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    #[Groups(['event:list', 'event:item'])]
    public function getAddress(): ?string
    {

        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getDescription(): string
    {
        if (empty($this->description))
            return '';
        return $this->description;
    }

    #[Groups(['event:list', 'event:item'])]
    public function getDescriptionFormatted(): string
    {
        if (empty($this->description))
            return '';
        return strip_tags($this->description, '<br>');
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * @param string|null $shortDescription
     */
    public function setShortDescription(?string $shortDescription): void
    {
        $this->shortDescription = $shortDescription;
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

    #[Groups(['event:list', 'event:item'])]
    public function getMainPhotoFullUrl(): ?string
    {
        return '/upload/images/' . $this->mainPhoto;
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
     * @return Collection<int, EventCollection>
     */
    public function getCollections(): Collection
    {
        return $this->collections;
    }

    public function addCollection(EventCollection $collection): static
    {
        if (!$this->collections->contains($collection)) {
            $this->collections->add($collection);
            $collection->addEvent($this);
        }

        return $this;
    }

    public function removeCollection(EventCollection $collection): static
    {
        if ($this->collections->removeElement($collection)) {
            $collection->removeEvent($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): static
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[Groups(['event:list', 'event:item'])]
    public function getCategoryName(): string
    {
        return $this->category->getName();
    }

    #[Groups(['event:list', 'event:item'])]
    public function getCategoryUrl(): string
    {
        return '/event/list/'.$this->category->getShort();
    }

    #[Groups(['event:list', 'event:item'])]
    public function getOrganisationName(): string
    {
        if (!empty($this->organisation)) {
            return $this->organisation->getName();
        }

        return $this->owner->getName();
    }

    #[Groups(['event:list', 'event:item'])]
    public function getOrganisationProfile(): ?string
    {
        if (!empty($this->organisation)) {
            return '/organisation/' . $this->organisation->getId();
        } else {
            return '/user/' . $this->owner->getId();
        }
    }

    public function getOrganisationPhoto(): ?string
    {
        if (!empty($this->organisation)) {
            return $this->organisation->getMainPhoto();
        } else {
            return $this->owner->getAvatar();
        }
    }

    public function getTags(): array
    {
        $toll = $this->getTagByToll($this->getToll());

        return [
            $this->getCategoryName() => '/event/collection/category/' . $this->getCategory()->getShort(),
            $toll[0] => '/event/collection/parameter/toll/' . $toll[1]
        ];

    }

    private function getTagByToll($toll): array
    {
        if (is_int($toll)) {
            $strToll = 'Бесплатно';
            $numToll = 0;
            if (!empty($toll)) {
                switch ($toll) {
                    case $toll > 0 && $toll < 1000:
                        $strToll = 'До 1000';
                        $numToll = 1000;
                        break;
                    case $toll > 1000 && $toll < 3000:
                        $strToll = 'До 3000';
                        $numToll = 3000;
                        break;
                    case $toll > 3000 && $toll < 5000:
                        $strToll = 'До 5000';
                        $numToll = 5000;
                        break;
                    case $toll > 5000:
                        $strToll = 'Дороже 5000';
                        $numToll = 5001;
                        break;
                }
            }
            return [$strToll, $numToll];
        } else {

        }
        return [$toll, 0];
    }

    /**
     * @return Collection<int, Filter>
     */
    public function getFilter(): Collection
    {
        return $this->filter;
    }

    public function addFilter(Filter $filter): static
    {
        if (!$this->filter->contains($filter)) {
            $this->filter->add($filter);
        }

        return $this;
    }

    public function removeFilter(Filter $filter): static
    {
        $this->filter->removeElement($filter);

        return $this;
    }

    public function getDayAndMonthName(DateTimeInterface $date): string
    {
        $m = '';
        $date = explode("-", $date->format('Y-m-j'));
        switch ($date[1]) {
            case 1:
                $m = 'января';
                break;
            case 2:
                $m = 'февраля';
                break;
            case 3:
                $m = 'марта';
                break;
            case 4:
                $m = 'апреля';
                break;
            case 5:
                $m = 'мая';
                break;
            case 6:
                $m = 'июня';
                break;
            case 7:
                $m = 'июля';
                break;
            case 8:
                $m = 'августа';
                break;
            case 9:
                $m = 'сентября';
                break;
            case 10:
                $m = 'октября';
                break;
            case 11:
                $m = 'ноября';
                break;
            case 12:
                $m = 'декабря';
                break;
        }

        if ($date[0] != (new DateTime())->format('Y')) {
            return $date[2] . ' ' . $m . ' ' . $date[0];
        }
        return $date[2] . ' ' . $m;
    }

    #[Groups(['event:list', 'event:item'])]
    public function getDates(): array
    {
        return ['start' => $this->getStartDate()->format('Y-m-d'),
            'end' => $this->getStartDate()->modify($this->getDuration() . ' days')->format('Y-m-d')];
    }

    public function getEndDate(bool $short = false): string|DateTime
    {
        $startDate = clone $this->getStartDate();
        $endDate = $startDate->modify($this->getDuration() . ' days');
        if ($short) {
            return $endDate;
        }
        return $this->getDayAndMonthName($endDate);
    }

    public function getDurationEnding(int $days): string
    {
        if ($days % 10 === 1 && $days % 100 !== 11) {
            $ending = "день";
        } else if (in_array(($days % 10), [2, 3, 4]) && !in_array(($days % 100), [12, 13, 14])) {
            $ending = "дня";
        } else {
            $ending = "дней";
        }

        if ($days === 0) {
            $ending = "Бессрочно";
        }

        return $ending;
    }

    /**
     * @return string|null
     */
    public function getVk(): ?string
    {
        return $this->vk;
    }

    /**
     * @param string|null $vk
     */
    public function setVk(?string $vk): void
    {
        $this->vk = $vk;
    }

    /**
     * @return string|null
     */
    public function getTg(): ?string
    {
        return $this->tg;
    }

    /**
     * @param string|null $tg
     */
    public function setTg(?string $tg): void
    {
        $this->tg = $tg;
    }

    public function jsonSerialize(): array
    {
        $totalChars = 0;
        $outputFilter = [];
        if (!empty($this->category)) {
            $outputFilter[] = [
                'id' => $this->category->getId(),
                'name' => $this->category->getName(),
                'status' => 'active'
            ];
            $totalChars += strlen($this->category->getName());
        }

        $filters = $this->getFilter();

        /** @var Filter $filter */
        foreach ($filters as $filter) {
            if ($totalChars > 23) {
                continue;
            }
            $outputFilter[] = [
                'id' => $filter->getId(),
                'name' => $filter->getName(),
                'short' => $filter->getShort(),
                'status' => ''
            ];
            $totalChars += strlen($filter->getName());

        }
        $startDate = clone $this->startDate;

        $duration = $this->duration . ' ' . $this->getDurationEnding($this->duration);
        if ($this->startDate->format('Y') != (new DateTime())->format('Y')) {
            $startDatePrintable = $this->startDate->format('Y') . ' ' . $this->getDayAndMonthName($this->startDate);
        } else {
            $startDatePrintable = $this->getDayAndMonthName($this->startDate);
        }


        $endDate = $startDate->modify($this->duration . ' day');

        if ($this->duration == 1) {
            $endDatePrintable = '';
        } else {
            if ($endDate->format('Y') != (new DateTime())->format('Y')) {
                $endDatePrintable = $endDate->format('Y') . ' ' . $this->getDayAndMonthName($endDate);
            } else {
                $endDatePrintable = $this->getDayAndMonthName($endDate);
            }
        }

        return [
            'id' => $this->id,
            'link' => '/event/' . $this->id,
            'img' => '/upload/images/' . $this->mainPhoto,
            'tags' => $outputFilter,
            'title' => $this->name,
            'description' => $this->shortDescription,
            'org' => !empty($this->getOrganisation()) ? [
                'img' => '/upload/images/' . $this->getOrganisation()->getMainPhoto(),
                'link' => '/organisation/' . $this->getOrganisation()->getId(),
                'title' => $this->getOrganisation()->getName()
            ] : '',
            'location' => [
                'title' => $this->address,
                'link' => "https://yandex.ru/maps/?mode=search&text='" . $this->address . "'"
            ],
            'startdate' => $startDatePrintable,
            'duration' => $duration,
            'enddate' => $endDatePrintable,
            'region' => ['id' => $this->getRegion()->getId(), 'name' => $this->getRegion()->getName(), 'admin_name' => $this->getRegion()->getAdminName()],
            'category' => ['id' => $this->getCategory()->getId(), 'name' => $this->getCategory()->getName()],
            'mainPhoto' => $this->mainPhoto,
            'toll' => $this->toll,
        ];
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUrlText(): ?string
    {
        return $this->url_text;
    }

    public function setUrlText(?string $url_text): static
    {
        $this->url_text = $url_text;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(?int $views): static
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param int|null $rating
     */
    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return array|null
     */
    public function getReviews(): ?array
    {
        return $this->reviews;
    }

    /**
     * @param array|null $reviews
     */
    public function setReviews(?array $reviews): void
    {
        $this->reviews = $reviews;
    }

    public function getStatistic(): ?PublicEventStatistic
    {
        return $this->statistic;
    }

    public function setStatistic(PublicEventStatistic $statistic): static
    {
        // set the owning side of the relation if necessary
        if ($statistic->getPublicEvent() !== $this) {
            $statistic->setPublicEvent($this);
        }

        $this->statistic = $statistic;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getConstant(): ?bool
    {
        return $this->constant;
    }

    /**
     * @param bool|null $constant
     */
    public function setConstant(?bool $constant): void
    {
        $this->constant = $constant;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }


}
