<?php

namespace App\Entity;

use App\Repository\FilterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilterRepository::class)]
class Filter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, PublicEvent>
     */
    #[ORM\ManyToMany(targetEntity: PublicEvent::class, mappedBy: 'filter')]
    private Collection $events;

    #[ORM\Column(length: 255)]
    private ?string $short = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

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
            $event->addFilter($this);
        }

        return $this;
    }

    public function removeEvent(PublicEvent $event): static
    {
        if ($this->events->removeElement($event)) {
            $event->removeFilter($this);
        }

        return $this;
    }


    public function __toString(): string
    {
        return $this->getName();
    }

    public function getShort(): ?string
    {
        return $this->short;
    }

    public function setShort(string $short): static
    {
        $this->short = $short;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

}
