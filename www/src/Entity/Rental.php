<?php

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $rentalStart = null;

    #[ORM\Column]
    private ?\DateTime $rentalEnd = null;

    #[ORM\Column]
    private ?int $rentalPrice = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    private ?User $user = null;


    #[ORM\ManyToOne(inversedBy: 'rentals')]
    private ?Boat $boat = null;

    /**
     * @var Collection<int, Formula>
     */
    #[ORM\ManyToMany(targetEntity: Formula::class, inversedBy: 'rentals')]
    private Collection $formulas;

    public function __construct()
    {
        $this->formulas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRentalStart(): ?\DateTime
    {
        return $this->rentalStart;
    }

    public function setRentalStart(\DateTime $rentalStart): static
    {
        $this->rentalStart = $rentalStart;

        return $this;
    }

    public function getRentalEnd(): ?\DateTime
    {
        return $this->rentalEnd;
    }

    public function setRentalEnd(\DateTime $rentalEnd): static
    {
        $this->rentalEnd = $rentalEnd;

        return $this;
    }

    public function getRentalPrice(): ?int
    {
        return $this->rentalPrice;
    }

    public function setRentalPrice(int $rentalPrice): static
    {
        $this->rentalPrice = $rentalPrice;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBoat(): ?Boat
    {
        return $this->boat;
    }

    public function setBoat(?Boat $boat): static
    {
        $this->boat = $boat;

        return $this;
    }

    /**
     * @return Collection<int, Formula>
     */
    public function getFormulas(): Collection
    {
        return $this->formulas;
    }

    public function addFormula(Formula $formula): static
    {
        if (!$this->formulas->contains($formula)) {
            $this->formulas->add($formula);
        }

        return $this;
    }

    public function removeFormula(Formula $formula): static
    {
        $this->formulas->removeElement($formula);

        return $this;
    }
}
