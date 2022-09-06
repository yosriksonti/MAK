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

    public function __toString() : ?string {
        return $this->id;
    }
}
