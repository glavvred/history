<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Repository\RegionRepository;


#[ORM\Entity(repositoryClass: RegionRepository::class)]
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
     * @var Collection<PublicEvent>|null
     */
    #[ORM\OneToMany(targetEntity: PublicEvent::class, mappedBy: 'region')]
    private ?Collection $publicEvents;

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

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parent = null;

    #[ORM\Column(length: 255)]
    #[Groups(['region'])]
    private ?string $slug = null;

    /**
     * @var Collection<ExcursionRouteReport>
     */
    #[ORM\OneToMany(targetEntity: ExcursionRouteReport::class, mappedBy: 'region')]
    private Collection $routeReports;

    /**
     * @var Collection<ExcursionRoute>
     */
    #[ORM\OneToMany(targetEntity: ExcursionRoute::class, mappedBy: 'region')]
    private Collection $excursionRoutes;

    public function __construct()
    {
        $this->publicEvents = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->excursionRoutes = new ArrayCollection();
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function addParent(self $parent): static
    {
        if (!$this->parent->contains($parent)) {
            $this->parent->add($parent);
            $parent->setParent($this);
        }

        return $this;
    }

    public function removeParent(self $parent): static
    {
        if ($this->parent->removeElement($parent)) {
            // set the owning side to null (unless already changed)
            if ($parent->getParent() === $this) {
                $parent->setParent(null);
            }
        }

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

    /**
     * @return Collection<int, ExcursionRoute>
     */
    public function getExcursionRoutes(): Collection
    {
        return $this->excursionRoutes;
    }

    public function addExcursionRoute(ExcursionRoute $excursionRoute): static
    {
        if (!$this->excursionRoutes->contains($excursionRoute)) {
            $this->excursionRoutes->add($excursionRoute);
            $excursionRoute->setRegion($this);
        }

        return $this;
    }

    public function removeExcursionRoute(ExcursionRoute $excursionRoute): static
    {
        if ($this->excursionRoutes->removeElement($excursionRoute)) {
            // set the owning side to null (unless already changed)
            if ($excursionRoute->getRegion() === $this) {
                $excursionRoute->setRegion(null);
            }
        }

        return $this;
    }

    public function getRouteReports(): Collection
    {
        return $this->routeReports;
    }

    public function setRouteReports(Collection $routeReports): void
    {
        $this->routeReports = $routeReports;
    }


}
