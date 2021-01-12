<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AircraftRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class Aircraft
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private ?string $icao = null;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private ?string $iata = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $modelName = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $title = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIata(): ?string
    {
        return $this->iata;
    }

    public function setIata(?string $iata): self
    {
        $this->iata = $iata;

        return $this;
    }

    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    public function setModelName(?string $modelName): self
    {
        $this->modelName = $modelName;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
