<?php

namespace App\Entity;

use App\Repository\VeteranRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VeteranRepository::class)]
#[ORM\Table(name: 'veterans')]
class Veteran
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 100)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\ManyToMany(targetEntity: Award::class)]
    #[ORM\JoinTable(name: 'veteran_awards')]
    private Collection $awards;

    #[ORM\ManyToOne(targetEntity: Rank::class)]
    private ?Rank $rank = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $deathDate = null;

    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'veteran', cascade: ['persist', 'remove'])]
    private Collection $media;

    #[ORM\Column(type: 'string', length: 50)]
    private string $warType;

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
            // set the owning side to null (unless already changed)
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

    public function setWarType(string $warType): self
    {
        if (!in_array($warType, array_values(self::WAR_TYPES))) {
            throw new \InvalidArgumentException("Invalid war type");
        }
        $this->warType = $warType;
        return $this;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
