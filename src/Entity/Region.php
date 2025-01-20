<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RegionRepository;
use Symfony\Component\Serializer\Attribute\Groups;


#[ORM\Entity]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['region'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['region'])]
    private ?string $name = null;

    /**
     * @var Collection<int, PublicEvent>
     */
    #[ORM\OneToMany(targetEntity: PublicEvent::class, mappedBy: 'region')]
    private Collection $publicEvents;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'parent')]
    private ?self $children = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'children')]
    private Collection $parent;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['region'])]
    private ?string $lng = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['region'])]
    private ?string $lat = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['region'])]
    private ?string $admin_name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'region')]
    private Collection $users;

    public function __construct()
    {
        $this->publicEvents = new ArrayCollection();
        $this->parent = new ArrayCollection();
        $this->users = new ArrayCollection();
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
            $publicEvent->setRegion($this);
        }

        return $this;
    }

    public function removePublicEvent(PublicEvent $publicEvent): static
    {
        if ($this->publicEvents->removeElement($publicEvent)) {
            // set the owning side to null (unless already changed)
            if ($publicEvent->getRegion() === $this) {
                $publicEvent->setRegion(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getChildren(): ?self
    {
        return $this->children;
    }

    public function setChildren(?self $children): static
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getParent(): Collection
    {
        return $this->parent;
    }

    public function addParent(self $parent): static
    {
        if (!$this->parent->contains($parent)) {
            $this->parent->add($parent);
            $parent->setChildren($this);
        }

        return $this;
    }

    public function removeParent(self $parent): static
    {
        if ($this->parent->removeElement($parent)) {
            // set the owning side to null (unless already changed)
            if ($parent->getChildren() === $this) {
                $parent->setChildren(null);
            }
        }

        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(?string $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getAdminName(): ?string
    {
        return $this->admin_name;
    }

    public function setAdminName(?string $admin_name): static
    {
        $this->admin_name = $admin_name;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setRegion($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRegion() === $this) {
                $user->setRegion(null);
            }
        }

        return $this;
    }

}
