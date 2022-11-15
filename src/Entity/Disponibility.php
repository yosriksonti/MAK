<?php

namespace App\Entity;

use DateTimeInterface;
use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

class Disponibility {
    private $start;
    private $end;
    public function __construct() {

    }

    public function getStart(): ?string
    {
        return $this->start;
    }
    public function getEnd(): ?string
    {
        return $this->end;
    }
    public function setStart(string $start): self
    {
        $this->start = $start;

        return $this;
    }
    public function setEnd(string $end): self
    {
        $this->end = $end;

        return $this;
    }
}