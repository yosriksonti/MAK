<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
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
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Pays;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Telephone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Add1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Add2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Permis;

    /**
     * @ORM\Column(type="date")
     */
    private $Date_Permis;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $CIN;

    /**
     * @ORM\Column(type="date")
     */
    private $Date_CIN;

    /**
     * @ORM\Column(type="date")
     */
    private $Date_Naissance;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): self
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->Pays;
    }

    public function setPays(string $Pays): self
    {
        $this->Pays = $Pays;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->Telephone;
    }

    public function setTelephone(string $Telephone): self
    {
        $this->Telephone = $Telephone;

        return $this;
    }

    public function getAdd1(): ?string
    {
        return $this->Add1;
    }

    public function setAdd1(string $Add1): self
    {
        $this->Add1 = $Add1;

        return $this;
    }

    public function getAdd2(): ?string
    {
        return $this->Add2;
    }

    public function setAdd2(string $Add2): self
    {
        $this->Add2 = $Add2;

        return $this;
    }

    public function getPermis(): ?string
    {
        return $this->Permis;
    }

    public function setPermis(string $Permis): self
    {
        $this->Permis = $Permis;

        return $this;
    }

    public function getDatePermis(): ?\DateTimeInterface
    {
        return $this->Date_Permis;
    }

    public function setDatePermis(\DateTimeInterface $Date_Permis): self
    {
        $this->Date_Permis = $Date_Permis;

        return $this;
    }

    public function getCIN(): ?string
    {
        return $this->CIN;
    }

    public function setCIN(string $CIN): self
    {
        $this->CIN = $CIN;

        return $this;
    }

    public function getDateCIN(): ?\DateTimeInterface
    {
        return $this->Date_CIN;
    }

    public function setDateCIN(\DateTimeInterface $Date_CIN): self
    {
        $this->Date_CIN = $Date_CIN;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->Date_Naissance;
    }

    public function setDateNaissance(\DateTimeInterface $Date_Naissance): self
    {
        $this->Date_Naissance = $Date_Naissance;

        return $this;
    }
}
