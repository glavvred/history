<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\NewsRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/news/{id}',
            requirements: ['id' => '\d+'],
            schemes: ['https'],
            normalizationContext: ['groups' => 'news:item'],
        ),
        new GetCollection(uriTemplate: '/news',
            schemes: ['https'],
            normalizationContext: ['groups' => 'news:list'],

        )
    ],
    normalizationContext: [
        'skip_null_values' => false
    ],
    order: ['createdAt' => 'DESC'],
    paginationEnabled: false,
)]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['news:list', 'news:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['news:list', 'news:item'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['news:list', 'news:item'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['news:list', 'news:item'])]
    private ?string $url = null;

    #[ORM\Column]
    private ?bool $published = null;

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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }
}
