<?php

namespace App\Entity;

use App\Repository\AirlineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AirlineRepository::class)
 */
class Airline
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private ?string $iata = null;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private ?string $icao = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $region = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIata(): ?string
    {
        return $this->iata;
    }

    public function setIata(?string $iata): self
    {
        $this->iata = $iata;

        return $this;
    }

    public function getIcao(): ?string
    {
        return $this->icao;
    }

    public function setIcao(?string $icao): self
    {
        $this->icao = $icao;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }
}
