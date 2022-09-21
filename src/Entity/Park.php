<?php

namespace App\Entity;

use App\Repository\ParkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParkRepository::class)
 */
class Park
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Vehicule::class, mappedBy="Park")
     */
    private $Vehicules;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Nom;

    /**
     * @ORM\Column(type="date")
     */
    private $DebutHS;

    /**
     * @ORM\Column(type="date")
     */
    private $FinHS;

    /**
     * @ORM\Column(type="date")
     */
    private $DebutBS;

    /**
     * @ORM\Column(type="date")
     */
    private $FinBS;

    public function __construct()
    {
        $this->Vehicules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Vehicule>
     */
    public function getVehicules(): Collection
    {
        return $this->Vehicules;
    }

    public function addVehicule(Vehicule $vehicule): self
    {
        if (!$this->Vehicules->contains($vehicule)) {
            $this->Vehicules[] = $vehicule;
            $vehicule->setPark($this);
        }

        return $this;
    }

    public function removeVehicule(Vehicule $vehicule): self
    {
        if ($this->Vehicules->removeElement($vehicule)) {
            // set the owning side to null (unless already changed)
            if ($vehicule->getPark() === $this) {
                $vehicule->setPark(null);
            }
        }

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function __toString() : string {
        return $this->id;
    }
    public function getDebut_HS(): ?string
    {
        $newDate = $this->DebutHS->format('d/m/Y');

        return $newDate;
    }

    public function getFin_HS(): ?string
    {
        $newDate = $this->FinHS->format('d/m/Y');

        return $newDate;    
    }

    public function getDebut_BS(): ?string
    {
        $newDate = $this->DebutBS->format('d/m/Y');

        return $newDate;
    }

    public function getFin_BS(): ?string
    {
        $newDate = $this->FinBS->format('d/m/Y');

        return $newDate;
    }

    public function getDebutHS(): ?\DateTimeInterface
    {
        return $this->DebutHS;
    }

    public function setDebutHS(\DateTimeInterface $DebutHS): self
    {
        $this->DebutHS = $DebutHS;

        return $this;
    }

    public function getFinHS(): ?\DateTimeInterface
    {
        return $this->FinHS;
    }

    public function setFinHS(\DateTimeInterface $FinHS): self
    {
        $this->FinHS = $FinHS;

        return $this;
    }

    public function getDebutBS(): ?\DateTimeInterface
    {
        return $this->DebutBS;
    }

    public function setDebutBS(\DateTimeInterface $DebutBS): self
    {
        $this->DebutBS = $DebutBS;

        return $this;
    }

    public function getFinBS(): ?\DateTimeInterface
    {
        return $this->FinBS;
    }

    public function setFinBS(\DateTimeInterface $FinBS): self
    {
        $this->FinBS = $FinBS;

        return $this;
    }
}
