<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @JMS\ExclusionPolicy(policy="ALL")
 */
class Location
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose()
     */
    private ?string $airportName = null;

    /**
     * @ORM\Column(type="string", length=33, nullable=true)
     */
    private ?string $airportType = null;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private ?string $continentIso = null;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private ?string $countryIso = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose()
     */
    private ?string $countryName = null;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private ?string $regionIso = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $regionName = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose()
     */
    private ?string $municipality = null;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $gpsCode;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     * @JMS\Expose()
     */
    private ?string $iataCode = null;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private ?string $localCode = null;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private bool $enabled = false;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getAirportName(): ?string
    {
        return $this->airportName;
    }

    /**
     * @param string $airportName
     * @return $this
     */
    public function setAirportName(string $airportName): self
    {
        $this->airportName = $airportName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAirportType(): ?string
    {
        return $this->airportType;
    }

    /**
     * @param string $airportType
     * @return $this
     */
    public function setAirportType(string $airportType): self
    {
        $this->airportType = $airportType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegionName(): ?string
    {
        return $this->regionName;
    }

    /**
     * @param string $regionName
     * @return $this
     */
    public function setRegionName(string $regionName): self
    {
        $this->regionName = $regionName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     * @return $this
     */
    public function setCountryName(string $countryName): self
    {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIataCode(): ?string
    {
        return $this->iataCode;
    }

    /**
     * @param string $iataCode
     * @return $this
     */
    public function setIataCode(string $iataCode): self
    {
        $this->iataCode = $iataCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocalCode(): ?string
    {
        return $this->localCode;
    }

    /**
     * @param string|null $localCode
     * @return $this
     */
    public function setLocalCode(?string $localCode): self
    {
        $this->localCode = $localCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContinentIso()
    {
        return $this->continentIso;
    }

    /**
     * @param mixed $continentIso
     * @return Location
     */
    public function setContinentIso($continentIso): self
    {
        $this->continentIso = $continentIso;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountryIso()
    {
        return $this->countryIso;
    }

    /**
     * @param mixed $countryIso
     * @return Location
     */
    public function setCountryIso($countryIso): self
    {
        $this->countryIso = $countryIso;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGpsCode()
    {
        return $this->gpsCode;
    }

    /**
     * @param mixed $gpsCode
     * @return Location
     */
    public function setGpsCode($gpsCode): self
    {
        $this->gpsCode = $gpsCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegionIso(): ?string
    {
        return $this->regionIso;
    }

    /**
     * @param mixed $regionIso
     * @return Location
     */
    public function setRegionIso($regionIso): self
    {
        $this->regionIso = $regionIso;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMunicipality()
    {
        return $this->municipality;
    }

    /**
     * @param string $municipality
     * @return Location
     */
    public function setMunicipality($municipality)
    {
        $this->municipality = $municipality;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
