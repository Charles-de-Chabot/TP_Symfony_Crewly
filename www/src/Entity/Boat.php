<?php

namespace App\Entity;

use App\Repository\BoatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoatRepository::class)]
class Boat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $maxUser = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column]
    private ?float $boatLength = null;

    #[ORM\Column]
    private ?float $boatWidth = null;

    #[ORM\Column]
    private ?float $boatDraught = null;

    #[ORM\Column(nullable: true)]
    private ?int $cabineNumber = null;

    #[ORM\Column(nullable: true)]
    private ?int $bedNumber = null;

    #[ORM\Column(length: 100)]
    private ?string $fuel = null;

    #[ORM\Column(length: 100)]
    private ?string $powerEngine = null;

    #[ORM\ManyToOne(inversedBy: 'boats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    #[ORM\ManyToOne(inversedBy: 'boats')]
    private ?Model $model = null;

    #[ORM\ManyToOne(inversedBy: 'boats')]
    private ?Adress $adress = null;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'boat')]
    private Collection $media;

    /**
     * @var Collection<int, Rental>
     */
    #[ORM\OneToMany(targetEntity: Rental::class, mappedBy: 'boat')]
    private Collection $rentals;

    /**
     * @var Collection<int, Formula>
     */
    #[ORM\ManyToMany(targetEntity: Formula::class, inversedBy: 'boats')]
    private Collection $formula;

    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->rentals = new ArrayCollection();
        $this->formula = new ArrayCollection();
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMaxUser(): ?int
    {
        return $this->maxUser;
    }

    public function setMaxUser(int $maxUser): static
    {
        $this->maxUser = $maxUser;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getBoatLength(): ?float
    {
        return $this->boatLength;
    }

    public function setBoatLength(float $boatLength): static
    {
        $this->boatLength = $boatLength;

        return $this;
    }

    public function getBoatWidth(): ?float
    {
        return $this->boatWidth;
    }

    public function setBoatWidth(float $boatWidth): static
    {
        $this->boatWidth = $boatWidth;

        return $this;
    }

    public function getBoatDraught(): ?float
    {
        return $this->boatDraught;
    }

    public function setBoatDraught(float $boatDraught): static
    {
        $this->boatDraught = $boatDraught;

        return $this;
    }

    public function getCabineNumber(): ?int
    {
        return $this->cabineNumber;
    }

    public function setCabineNumber(?int $cabineNumber): static
    {
        $this->cabineNumber = $cabineNumber;

        return $this;
    }

    public function getBedNumber(): ?int
    {
        return $this->bedNumber;
    }

    public function setBedNumber(?int $bedNumber): static
    {
        $this->bedNumber = $bedNumber;

        return $this;
    }

    public function getFuel(): ?string
    {
        return $this->fuel;
    }

    public function setFuel(string $fuel): static
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getPowerEngine(): ?string
    {
        return $this->powerEngine;
    }

    public function setPowerEngine(string $powerEngine): static
    {
        $this->powerEngine = $powerEngine;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getAdress(): ?Adress
    {
        return $this->adress;
    }

    public function setAdress(?Adress $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setBoat($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getBoat() === $this) {
                $medium->setBoat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): static
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals->add($rental);
            $rental->setBoat($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getBoat() === $this) {
                $rental->setBoat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formula>
     */
    public function getFormula(): Collection
    {
        return $this->formula;
    }

    public function addFormula(Formula $formula): static
    {
        if (!$this->formula->contains($formula)) {
            $this->formula->add($formula);
        }

        return $this;
    }

    public function removeFormula(Formula $formula): static
    {
        $this->formula->removeElement($formula);

        return $this;
    }
}
