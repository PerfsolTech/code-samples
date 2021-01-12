<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommissionRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class Commission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Type(name="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=2)
     * @JMS\Type(name="string")
     */
    private ?string $airline = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Type(name="string")
     */
    private ?string $airlineName = null;

    /**
     * @ORM\Column(type="text")
     * @JMS\Type(name="string")
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="string", length=13)
     * @JMS\Type(name="string")
     */
    private ?string $destType = null;

    /**
     * @ORM\Column(type="date",  nullable=true)
     * @JMS\Type(name="DateTime")
     */
    private ?\DateTimeInterface $departFrom = null;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @JMS\Type(name="DateTime")
     */
    private ?\DateTimeInterface $departTo = null;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @JMS\Type(name="DateTime")
     */
    private ?\DateTimeInterface $ticketFrom = null;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @JMS\Type(name="DateTime")
     */
    private ?\DateTimeInterface $ticketTo = null;

    /**
     * @ORM\Column(type="string", length=13)
     * @JMS\Type(name="string")
     */
    private ?string $codeshare = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Type(name="string")
     */
    private ?string $operatedBy = null;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type(name="boolean")
     */
    private ?bool $ccPermited = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Type(name="string")
     */
    private ?string $cabinClass = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Type(name="string")
     */
    private ?string $bookingClass = null;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Type(name="integer")
     */
    private ?int $bspRate = null;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Type(name="integer")
     */
    private ?int $agentRate = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Type(name="float")
     */
    private ?float $serviceFee = null;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type(name="boolean")
     */
    private ?bool $applyToAdult = null;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type(name="boolean")
     */
    private ?bool $applyToChild = null;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type(name="boolean")
     */
    private ?bool $applyToInfant = null;

    /**
     * @ORM\Column(type="string", length=65, nullable=true)
     * @JMS\Type(name="string")
     */
    private ?string $origin = null;

    /**
     * @ORM\Column(type="string", length=65, nullable=true)
     * @JMS\Type(name="string")
     */
    private ?string $dest = null;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type(name="boolean")
     */
    private ?bool $viceVersa = null;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type(name="boolean")
     */
    private ?bool $applyToTourCode = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Type(name="string")
     */
    private ?string $tourCode = null;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type(name="boolean")
     */
    private ?bool $applyToFareWithAdditionalCarriers = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @JMS\Type(name="string")
     */
    private ?string $additionalCarrier = null;

    /**
     * @ORM\Column(type="string", length=13)
     * @JMS\Type(name="string")
     */
    private ?string $carrierType = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Type(name="string")
     */
    private ?string $fareBasis = null;

    /**
     * @ORM\Column(type="string", length=2)
     * @JMS\Type(name="string")
     */
    private ?string $platingCarrier = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Type(name="string")
     */
    private ?string $permittedFlight = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Type(name="string")
     */
    private ?string $notPermittedFlight = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Type(name="string")
     */
    private ?string $requiredFlight = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PriceRule", inversedBy="commission", fetch="LAZY")
     * @JMS\Exclude
     */
    private ?PriceRule $priceRule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAirline(): ?string
    {
        return $this->airline;
    }

    public function setAirline(string $airline): self
    {
        $this->airline = $airline;

        return $this;
    }

    public function getAirlineName(): ?string
    {
        return $this->airlineName;
    }

    public function setAirlineName(string $airlineName): self
    {
        $this->airlineName = $airlineName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDestType(): ?string
    {
        return $this->destType;
    }

    public function setDestType(string $destType): self
    {
        $this->destType = $destType;

        return $this;
    }

    public function getDepartFrom(): ?\DateTimeInterface
    {
        return $this->departFrom;
    }

    public function setDepartFrom(?\DateTimeInterface $departFrom): self
    {
        $this->departFrom = $departFrom;

        return $this;
    }

    public function getDepartTo(): ?\DateTimeInterface
    {
        return $this->departTo;
    }

    public function setDepartTo(?\DateTimeInterface $departTo): self
    {
        $this->departTo = $departTo;

        return $this;
    }

    public function getTicketFrom(): ?\DateTimeInterface
    {
        return $this->ticketFrom;
    }

    public function setTicketFrom(?\DateTimeInterface $ticketFrom): self
    {
        $this->ticketFrom = $ticketFrom;

        return $this;
    }

    public function getTicketTo(): ?\DateTimeInterface
    {
        return $this->ticketTo;
    }

    public function setTicketTo(?\DateTimeInterface $ticketTo): self
    {
        $this->ticketTo = $ticketTo;

        return $this;
    }

    public function getCodeshare(): ?string
    {
        return $this->codeshare;
    }

    public function setCodeshare(string $codeshare): self
    {
        $this->codeshare = $codeshare;

        return $this;
    }

    public function getOperatedBy(): ?string
    {
        return $this->operatedBy;
    }

    public function setOperatedBy(?string $operatedBy): self
    {
        $this->operatedBy = $operatedBy;

        return $this;
    }

    public function getCcPermited(): ?bool
    {
        return $this->ccPermited;
    }

    public function setCcPermited(bool $ccPermited): self
    {
        $this->ccPermited = $ccPermited;

        return $this;
    }

    public function getCabinClass(): ?string
    {
        return $this->cabinClass;
    }

    public function setCabinClass(string $cabinClass): self
    {
        $this->cabinClass = $cabinClass;

        return $this;
    }

    public function getBookingClass(): ?string
    {
        return $this->bookingClass;
    }

    public function setBookingClass(?string $bookingClass): self
    {
        $this->bookingClass = $bookingClass;

        return $this;
    }

    public function getBspRate(): ?int
    {
        return $this->bspRate;
    }

    public function setBspRate(int $bspRate): self
    {
        $this->bspRate = $bspRate;

        return $this;
    }

    public function getAgentRate(): ?int
    {
        return $this->agentRate;
    }

    public function setAgentRate(int $agentRate): self
    {
        $this->agentRate = $agentRate;

        return $this;
    }

    public function getServiceFee(): ?float
    {
        return $this->serviceFee;
    }

    public function setServiceFee(float $serviceFee): self
    {
        $this->serviceFee = $serviceFee;

        return $this;
    }

    public function getApplyToAdult(): ?bool
    {
        return $this->applyToAdult;
    }

    public function setApplyToAdult(bool $applyToAdult): self
    {
        $this->applyToAdult = $applyToAdult;

        return $this;
    }

    public function getApplyToChild(): ?bool
    {
        return $this->applyToChild;
    }

    public function setApplyToChild(bool $applyToChild): self
    {
        $this->applyToChild = $applyToChild;

        return $this;
    }

    public function getApplyToInfant(): ?bool
    {
        return $this->applyToInfant;
    }

    public function setApplyToInfant(bool $applyToInfant): self
    {
        $this->applyToInfant = $applyToInfant;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getDest(): ?string
    {
        return $this->dest;
    }

    public function setDest(?string $dest): self
    {
        $this->dest = $dest;

        return $this;
    }

    public function getViceVersa(): ?bool
    {
        return $this->viceVersa;
    }

    public function setViceVersa(bool $viceVersa): self
    {
        $this->viceVersa = $viceVersa;

        return $this;
    }

    public function getApplyToTourCode(): ?bool
    {
        return $this->applyToTourCode;
    }

    public function setApplyToTourCode(bool $applyToTourCode): self
    {
        $this->applyToTourCode = $applyToTourCode;

        return $this;
    }

    public function getTourCode(): ?string
    {
        return $this->tourCode;
    }

    public function setTourCode(?string $tourCode): self
    {
        $this->tourCode = $tourCode;

        return $this;
    }

    public function getApplyToFareWithAdditionalCarriers(): ?bool
    {
        return $this->applyToFareWithAdditionalCarriers;
    }

    public function setApplyToFareWithAdditionalCarriers(bool $applyToFareWithAdditionalCarriers): self
    {
        $this->applyToFareWithAdditionalCarriers = $applyToFareWithAdditionalCarriers;

        return $this;
    }

    public function getAdditionalCarrier(): ?string
    {
        return $this->additionalCarrier;
    }

    public function setAdditionalCarrier(?string $additionalCarrier): self
    {
        $this->additionalCarrier = $additionalCarrier;

        return $this;
    }

    public function getCarrierType(): ?string
    {
        return $this->carrierType;
    }

    public function setCarrierType(string $carrierType): self
    {
        $this->carrierType = $carrierType;

        return $this;
    }

    public function getFareBasis(): ?string
    {
        return $this->fareBasis;
    }

    public function setFareBasis(string $fareBasis): self
    {
        $this->fareBasis = $fareBasis;

        return $this;
    }

    public function getPlatingCarrier(): ?string
    {
        return $this->platingCarrier;
    }

    public function setPlatingCarrier(string $platingCarrier): self
    {
        $this->platingCarrier = $platingCarrier;

        return $this;
    }

    public function getPermittedFlight(): ?string
    {
        return $this->permittedFlight;
    }

    public function setPermittedFlight(?string $permittedFlight): self
    {
        $this->permittedFlight = $permittedFlight;

        return $this;
    }

    public function getNotPermittedFlight(): ?string
    {
        return $this->notPermittedFlight;
    }

    public function setNotPermittedFlight(?string $notPermittedFlight): self
    {
        $this->notPermittedFlight = $notPermittedFlight;

        return $this;
    }

    public function getRequiredFlight(): ?string
    {
        return $this->requiredFlight;
    }

    public function setRequiredFlight(?string $requiredFlight): self
    {
        $this->requiredFlight = $requiredFlight;

        return $this;
    }

    public function getPriceRule(): ?PriceRule
    {
        return $this->priceRule;
    }

    public function setPriceRule(?PriceRule $priceRule): self
    {
        $this->priceRule = $priceRule;

        return $this;
    }

    public function __toString()
    {
        return "{$this->getId()}|{$this->getPlatingCarrier()}|{$this->getAdditionalCarrier()}|{$this->getOrigin()}|{$this->getDest()}|{$this->getCabinClass()}";
    }
}
