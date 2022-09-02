<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VehiculeRepository::class)
 */
class Vehicule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Marque;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Modele;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Categorie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Boite;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Carb;

    /**
     * @ORM\Column(type="integer")
     */
    private $Nb_Places;

    /**
     * @ORM\Column(type="integer")
     */
    private $Nb_Portes;

    /**
     * @ORM\Column(type="integer")
     */
    private $Nb_Val;

    /**
     * @ORM\Column(type="float")
     */
    private $Caut;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Clim;

    /**
     * @ORM\Column(type="text")
     */
    private $Description;

    /**
     * @ORM\Column(type="text")
     */
    private $Description_Det;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Photo_Def;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Photo_reel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Photo_Saison;

    /**
     * @ORM\OneToMany(targetEntity=Location::class, mappedBy="Vehicule")
     */
    private $Locations;

    /**
     * @ORM\ManyToOne(targetEntity=Park::class, inversedBy="Vehicules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Park;

    public function __construct()
    {
        $this->Locations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->Marque;
    }

    public function setMarque(string $Marque): self
    {
        $this->Marque = $Marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->Modele;
    }

    public function setModele(string $Modele): self
    {
        $this->Modele = $Modele;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->Categorie;
    }

    public function setCategorie(string $Categorie): self
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getBoite(): ?string
    {
        return $this->Boite;
    }

    public function setBoite(string $Boite): self
    {
        $this->Boite = $Boite;

        return $this;
    }

    public function getCarb(): ?string
    {
        return $this->Carb;
    }

    public function setCarb(string $Carb): self
    {
        $this->Carb = $Carb;

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->Nb_Places;
    }

    public function setNbPlaces(int $Nb_Places): self
    {
        $this->Nb_Places = $Nb_Places;

        return $this;
    }

    public function getNbPortes(): ?int
    {
        return $this->Nb_Portes;
    }

    public function setNbPortes(int $Nb_Portes): self
    {
        $this->Nb_Portes = $Nb_Portes;

        return $this;
    }

    public function getNbVal(): ?int
    {
        return $this->Nb_Val;
    }

    public function setNbVal(int $Nb_Val): self
    {
        $this->Nb_Val = $Nb_Val;

        return $this;
    }

    public function getCaut(): ?float
    {
        return $this->Caut;
    }

    public function setCaut(float $Caut): self
    {
        $this->Caut = $Caut;

        return $this;
    }

    public function isClim(): ?bool
    {
        return $this->Clim;
    }

    public function setClim(bool $Clim): self
    {
        $this->Clim = $Clim;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getDescriptionDet(): ?string
    {
        return $this->Description_Det;
    }

    public function setDescriptionDet(string $Description_Det): self
    {
        $this->Description_Det = $Description_Det;

        return $this;
    }

    public function getPhotoDef(): ?string
    {
        return $this->Photo_Def;
    }

    public function setPhotoDef(string $Photo_Def): self
    {
        $this->Photo_Def = $Photo_Def;

        return $this;
    }

    public function getPhotoReel(): ?string
    {
        return $this->Photo_reel;
    }

    public function setPhotoReel(string $Photo_reel): self
    {
        $this->Photo_reel = $Photo_reel;

        return $this;
    }

    public function getPhotoSaison(): ?string
    {
        return $this->Photo_Saison;
    }

    public function setPhotoSaison(string $Photo_Saison): self
    {
        $this->Photo_Saison = $Photo_Saison;

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->Locations;
    }

    public function addLocation(Location $location): self
    {
        if (!$this->Locations->contains($location)) {
            $this->Locations[] = $location;
            $location->setVehicule($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->Locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getVehicule() === $this) {
                $location->setVehicule(null);
            }
        }

        return $this;
    }

    public function getPark(): ?Park
    {
        return $this->Park;
    }

    public function setPark(?Park $Park): self
    {
        $this->Park = $Park;

        return $this;
    }
    public function __toString() : ?string {
        return $this->id;
    }
}