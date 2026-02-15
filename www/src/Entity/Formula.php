<?php

namespace App\Entity;

use App\Repository\FormulaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormulaRepository::class)]
class Formula
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null;


    /**
     * @var Collection<int, Boat>
     */
    #[ORM\ManyToMany(targetEntity: Boat::class, mappedBy: 'formula')]
    private Collection $boats;

    /**
     * @var Collection<int, Rental>
     */
    #[ORM\ManyToMany(targetEntity: Rental::class, mappedBy: 'formulas')]
    private Collection $rentals;

    public function __construct()
    {
        $this->rentals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Boat>
     */
    public function getBoats(): Collection
    {
        return $this->boats;
    }

    public function addBoat(Boat $boat): static
    {
        if (!$this->boats->contains($boat)) {
            $this->boats->add($boat);
            $boat->addFormula($this);
        }

        return $this;
    }

    public function removeBoat(Boat $boat): static
    {
        if ($this->boats->removeElement($boat)) {
            $boat->removeFormula($this);
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
            $rental->addFormula($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            $rental->removeFormula($this);
        }

        return $this;
    }
}
