<?php

namespace App\Entity;

use App\Repository\OrganisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
#[ORM\Index(name: 'organisation_fulltext_idx', columns: ['name', 'description'], flags: ['fulltext'])]
class Organisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]

    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coordinates = null;

    #[ORM\Column(length: 255)]
    private ?string $main_photo = null;

    #[ORM\Column(nullable: true)]
    private ?array $additional_photos = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?array $contacts = null;

    #[ORM\Column(nullable: true)]
    private bool|null $verified = null;

    private array $mainImageDimensions;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'organisations')]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $owner;

    #[ORM\ManyToOne(inversedBy: 'organisations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OrganisationCategory $category = null;

    /**
     * @var Collection<int, PublicEvent>
     */
    #[ORM\OneToMany(targetEntity: PublicEvent::class, mappedBy: 'organisation')]
    private Collection $publicEvents;

    #[ORM\Column(length: 255)]
    private ?string $short_description = null;

    /**
     * @var string|null
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    #[ORM\Column(length: 128, unique: true, nullable: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private $slug;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoDescription = null;

    #[ORM\Column(nullable: true)]
    private ?array $keywords = null;

    public function __construct()
    {
        $this->owner = new ArrayCollection();
        $this->publicEvents = new ArrayCollection();
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCoordinates(): ?string
    {
        return $this->coordinates;
    }

    /**
     * @return string|null
     */
    public function swapCoordinates(): ?string
    {
        $coordinates = explode(',', $this->coordinates);
        return trim($coordinates[1]) . ',' . trim($coordinates[0]);
    }

    /**
     * @param string|null $coordinates
     */
    public function setCoordinates(?string $coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    /**
     * @return Collection<int, User>
     */
    public function getOwner(): Collection
    {
        return $this->owner;
    }

    public function addOwner(User $owner): static
    {
        if (!$this->owner->contains($owner)) {
            $this->owner->add($owner);
        }

        return $this;
    }

    public function removeOwner(User $owner): static
    {
        $this->owner->removeElement($owner);

        return $this;
    }

    public function getCategory(): ?OrganisationCategory
    {
        return $this->category;
    }

    public function setCategory(?OrganisationCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, PublicEvent>
     */
    public function getPublicEvents(): Collection
    {
        return $this->publicEvents;
    }

    public function addPublicEvent(PublicEvent $publicEvent): static
    {
        if (!$this->publicEvents->contains($publicEvent)) {
            $this->publicEvents->add($publicEvent);
            $publicEvent->setOrganisation($this);
        }

        return $this;
    }

    public function removePublicEvent(PublicEvent $publicEvent): static
    {
        if ($this->publicEvents->removeElement($publicEvent)) {
            // set the owning side to null (unless already changed)
            if ($publicEvent->getOrganisation() === $this) {
                $publicEvent->setOrganisation(null);
            }
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    /**
     * @param bool|null $verified
     */
    public function setVerified(?bool $verified): void
    {
        $this->verified = $verified;
    }

    public function __toString(): string
    {
        return '(' . $this->getId() . ') ' . $this->getName();
    }

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(string $short_description): static
    {
        $this->short_description = $short_description;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getContacts(): ?array
    {
        return $this->contacts;
    }

    /**
     * @param array|null $contacts
     */
    public function setContacts(?array $contacts): void
    {
        $this->contacts = $contacts;
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

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getSeoDescription(): ?string
    {
        return $this->seoDescription;
    }

    /**
     * @param string|null $seoDescription
     */
    public function setSeoDescription(?string $seoDescription): void
    {
        $this->seoDescription = $seoDescription;
    }

    /**
     * @return array|null
     */
    public function getKeywords(): ?array
    {
        return $this->keywords;
    }

    /**
     * @param array|null $keywords
     */
    public function setKeywords(?array $keywords): void
    {
        $this->keywords = $keywords;
    }

    

}
