<?php

namespace App\Entity;

use AllowDynamicProperties;
use ApiPlatform\Metadata\Get;
use App\Filter\BirthYearFilter;
use App\Filter\DeathYearFilter;
use App\Repository\VeteranRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[AllowDynamicProperties] #[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['veteran:read']],
            uriTemplate: '/veterans',
            paginationEnabled: false,
            filters: ['App\Filter\VeteranFilter']
        ),
        // Добавленная операция Get
        new Get(
            normalizationContext: ['groups' => ['veteran:item']],
            uriTemplate: '/people/{id}',
            name: 'api_veteran_item'
        )
    ]
)]
#[ORM\Entity(repositoryClass: VeteranRepository::class)]
#[ORM\Table(name: 'veterans')]
class Veteran
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['veteran:read', 'veteran:item'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['veteran:read', 'veteran:item'])]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['veteran:read', 'veteran:item'])]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['veteran:read', 'veteran:item'])]
    private ?string $middleName = null;

    #[ORM\ManyToMany(targetEntity: Award::class)]
    #[ORM\JoinTable(name: 'veteran_awards')]
    private Collection $awards;

    #[ORM\ManyToOne(targetEntity: Rank::class)]
    private ?Rank $rank = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['veteran:item'])]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['veteran:item'])]
    private ?\DateTimeInterface $deathDate = null;

    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'veteran', cascade: ['persist', 'remove'])]
    #[Groups(['veteran:item'])]
    private Collection $media;

    #[Groups(['veteran:item'])]
    public function getImagePath(): ?string
    {
        return $this->photo ? 'uploads/photos/' . $this->photo : null;
    }


    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['veteran:read', 'veteran:item'])]
    private string $warType;

    #[Vich\UploadableField(mapping: 'veteran_photo', fileNameProperty: 'photo')]
    private ?File $photoFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['veteran:read'])]
    private ?string $photo = null;


    public const WAR_TYPES = [
        'Российско-чеченский конфликт' => 'chechen',
        'Герои СВО' => 'svo',
        'Герои ВОВ' => 'ww2',
        'Локальные конфликты' => 'local',
        'Афганская война' => 'afghan',
    ];

    public function __construct()
    {
        $this->awards = new ArrayCollection();
        $this->media = new ArrayCollection();
    }

    public function setWarType(string $warType): self
    {
        $validTypes = array_values(self::WAR_TYPES);
        if (!in_array($warType, $validTypes)) {
            throw new \InvalidArgumentException("Недопустимый тип войны. Допустимые значения: " . implode(', ', $validTypes));
        }

        $this->warType = $warType;
        return $this;
    }

    public function getWarTypeLabel(): string
    {
        return array_search($this->warType, self::WAR_TYPES) ?: $this->warType;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;
        return $this;
    }

    public function getFullName(): string
    {
        return trim($this->lastName . ' ' . $this->firstName . ' ' . $this->middleName);
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return Collection<int, Award>
     */
    #[Groups(['veteran:item'])]
    public function getAwards(): Collection
    {
        return $this->awards;
    }

    public function addAward(Award $award): self
    {
        if (!$this->awards->contains($award)) {
            $this->awards->add($award);
        }
        return $this;
    }

    public function removeAward(Award $award): self
    {
        $this->awards->removeElement($award);
        return $this;
    }

    #[Groups(['veteran:item'])]

    public function getRank(): ?Rank
    {
        return $this->rank;
    }

    public function setRank(?Rank $rank): self
    {
        $this->rank = $rank;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getDeathDate(): ?\DateTimeInterface
    {
        return $this->deathDate;
    }

    public function setDeathDate(?\DateTimeInterface $deathDate): self
    {
        $this->deathDate = $deathDate;
        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setVeteran($this);
        }
        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            if ($medium->getVeteran() === $this) {
                $medium->setVeteran(null);
            }
        }
        return $this;
    }

    public function getWarType(): string
    {
        return $this->warType;
    }

    public function setPhotoFile(?File $photoFile = null): void
    {
        $this->photoFile = $photoFile;
        if (null !== $photoFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photo ? 'uploads/photos/' . $this->photo : null;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
