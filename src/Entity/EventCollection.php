<?php

namespace App\Entity;

use App\Repository\EventCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: EventCollectionRepository::class)]
class EventCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 512)]
    private ?string $shortDescription = null;

    #[ORM\Column(length: 255)]
    private ?string $main_photo = null;

    /**
     * @var Collection<int, PublicEvent>
     */
    #[ORM\ManyToMany(targetEntity: PublicEvent::class, inversedBy: 'collections')]
    #[ORM\OrderBy(['startDate' => 'DESC'])]
    private Collection $events;

    #[ORM\Column(nullable: true)]
    private ?bool $mainPage = null;

    #[ORM\Column(nullable: true)]
    private ?bool $bottomPage = null;

    /**
     * @var string|null
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private $slug;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

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
        return $this->main_photo;
    }

    public function setMainPhoto(string $main_photo): static
    {
        $this->main_photo = $main_photo;

        return $this;
    }

    /**
     * @return Collection<int, PublicEvent>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(PublicEvent $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
        }

        return $this;
    }

    public function removeEvent(PublicEvent $event): static
    {
        $this->events->removeElement($event);

        return $this;
    }

    public function isMainPage(): ?bool
    {
        if (empty($this->mainPage)) {
            return false;
        }
        return $this->mainPage;
    }

    public function setMainPage(?bool $mainPage): static
    {
        $this->mainPage = $mainPage;

        return $this;
    }

    public function isBottomPage(): ?bool
    {
        if (empty($this->bottomPage)) {
            return false;
        }
        return $this->bottomPage;
    }

    public function setBottomPage(?bool $bottomPage): static
    {
        $this->bottomPage = $bottomPage;

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
