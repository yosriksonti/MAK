<?php

namespace App\Entity;

use DateTimeInterface;
use App\Repository\LocationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 */
class Location
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $Num;

     /**
     * @ORM\Column(type="string", length=255)
     */
    private $IP;

    /**
     * @ORM\Column(type="date")
     */
    private $Date_Res;

    /**
     * @ORM\Column(type="date")
     */
    private $Date_Loc;

    /**
     * @ORM\Column(type="date")
     */
    private $Date_Retour;

    /**
     * @ORM\Column(type="float")
     */
    private $Montant;

    /**
     * @ORM\Column(type="float")
     */
    private $Avance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Etat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Status;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="Locations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Client;

    /**
     * @ORM\ManyToOne(targetEntity=Vehicule::class, inversedBy="Locations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Vehicule;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNum(): ?string
    {
        return $this->IP;
    }

    public function setNum(string $Num): self
    {
        $this->Num = $Num;

        return $this;
    }

    public function getIP(): ?string
    {
        return $this->IP;
    }

    public function setIP(string $IP): self
    {
        $this->IP = $IP;

        return $this;
    }

    public function getDateRes(): ?DateTimeInterface
    {
        return $this->Date_Res;
    }

    public function setDateRes(DateTimeInterface $dateTime): self
    {
        $this->Date_Res = $dateTime;

        return $this;
    }

    public function getDateLoc(): ?DateTimeInterface
    {
        return $this->Date_Loc;
    }

    public function setDateLoc(DateTimeInterface $dateTime): self
    {
        $this->Date_Loc = $dateTime;

        return $this;
    }

    public function getDateRetour(): ?DateTimeInterface
    {
        return $this->Date_Retour;
    }

    public function setDateRetour(DateTimeInterface $dateTime): self
    {
        $this->Date_Retour = $dateTime;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->Montant;
    }

    public function setMontant(float $Montant): self
    {
        $this->Montant = $Montant;

        return $this;
    }

    public function getAvance(): ?float
    {
        return $this->Avance;
    }

    public function setAvance(float $Avance): self
    {
        $this->Avance = $Avance;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(string $Etat): self
    {
        $this->Etat = $Etat;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->Status;
    }

    public function setStatus(string $Status): self
    {
        $this->Status = $Status;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->Client;
    }

    public function setClient(?Client $Client): self
    {
        $this->Client = $Client;

        return $this;
    }

    public function getVehicule(): ?Vehicule
    {
        return $this->Vehicule;
    }

    public function setVehicule(?Vehicule $Vehicule): self
    {
        $this->Vehicule = $Vehicule;

        return $this;
    }


    /*public function __toString()
    {
        return $this->Num;
    }*/
}
