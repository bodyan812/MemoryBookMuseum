<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\RankRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['rank:read']],
            uriTemplate: '/ranks',
            paginationEnabled: false
        )
    ]
)]
#[ORM\Entity(repositoryClass: RankRepository::class)]
#[ORM\Table(name: 'ranks')]
class Rank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['veteran:item', 'rank:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['veteran:item', 'rank:read'])]
    private string $title;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
