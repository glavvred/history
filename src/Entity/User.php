<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_NAME', fields: ['name'])]
#[UniqueEntity(fields: ['name'], message: 'There is already an account with this name')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_USER = 'ROLE_USER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $name = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column(nullable: true)]
    private ?string $password;

    /**
     * @var Collection<int, PublicEvent>
     */
    #[ORM\OneToMany(targetEntity: PublicEvent::class, mappedBy: 'owner')]
    private Collection $publicEvents;

    /**
     * @var Collection<int, PublicEvent>
     */
    #[ORM\OneToMany(targetEntity: EventReport::class, mappedBy: 'reporter')]
    private Collection $eventReports;

    /**
     * @var Collection<int, PublicEvent>
     */
    #[ORM\OneToMany(targetEntity: ExcursionRouteReport::class, mappedBy: 'reporter')]
    private Collection $routeReports;


    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    /**
     * @var Collection<int, Organisation>
     */
    #[ORM\ManyToMany(targetEntity: Organisation::class, mappedBy: 'owner')]
    private Collection $organisations;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Region $region = null;

    #[ORM\Column(nullable: true)]
    private ?bool $newsletter = null;

    public function __construct()
    {
        $this->publicEvents = new ArrayCollection();
        $this->organisations = new ArrayCollection();
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->name;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'PUBLIC_ACCESS';
        if (in_array('ROLE_SUPER_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
        }
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function isAdmin(): bool
    {
        if (in_array(self::ROLE_ADMIN, $this->roles)) {
            return true;
        }
        if (in_array(self::ROLE_SUPER_ADMIN, $this->roles)) {
            return true;
        }
        return false;

    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        if (!is_null($password)) {
            $this->password = $password;
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $publicEvent->setOwner($this);
        }

        return $this;
    }

    public function removePublicEvent(PublicEvent $publicEvent): static
    {
        if ($this->publicEvents->removeElement($publicEvent)) {
            // set the owning side to null (unless already changed)
            if ($publicEvent->getOwner() === $this) {
                $publicEvent->setOwner(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEventReports(): Collection
    {
        return $this->eventReports;
    }

    /**
     * @param Collection $eventReports
     */
    public function setEventReports(Collection $eventReports): void
    {
        $this->eventReports = $eventReports;
    }

    /**
     * @return Collection<int, Organisation>
     */
    public function getOrganisations(): Collection
    {
        return $this->organisations;
    }

    public function addOrganisation(Organisation $organisation): static
    {
        if (!$this->organisations->contains($organisation)) {
            $this->organisations->add($organisation);
            $organisation->addOwner($this);
        }

        return $this;
    }

    public function removeOrganisation(Organisation $organisation): static
    {
        if ($this->organisations->removeElement($organisation)) {
            $organisation->removeOwner($this);
        }

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

    public function isNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(?bool $newsletter): static
    {
        $this->newsletter = $newsletter;

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
