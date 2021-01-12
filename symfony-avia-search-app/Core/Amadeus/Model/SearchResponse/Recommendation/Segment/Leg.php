<?php


namespace App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment;


use App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg\Aircraft;
use App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg\Destination;
use App\Entity\Commission;
use JMS\Serializer\Annotation as JMS;

class Leg
{
    /**
     * @JMS\Type(name="App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg\Destination")
     */
    private ?Destination $departure = null;
    /**
     * @JMS\Type(name="App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg\Destination")
     */
    private ?Destination $arrival = null;
    /**
     * @JMS\Type(name="string")
     */
    private string $marketingCarrier = '';
    /**
     * @JMS\Type(name="string")
     */
    private string $operatingCarrier = '';
    /**
     * Elapse Flying Time
     * @JMS\Type(name="string")
     */
    private string $eft = '';
    /**
     * @JMS\Type(name="string")
     */
    private string $flightNumber = '';
    /**
     * @JMS\Type(name="App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg\Aircraft")
     */
    private ?Aircraft $aircraft = null;
    /**
     * @JMS\Type(name="string")
     */
    private ?string $layover = null;
    /**
     * @JMS\Type(name="string")
     */
    private ?string $layoverMinutes = null;

    /**
     * @var Commission[]
     * @JMS\Type(name="array<App\Entity\Commission>")
     */
    private array $commissions = [];

    public function getDeparture(): ?Destination
    {
        return $this->departure;
    }

    public function setDeparture(Destination $departure): void
    {
        $this->departure = $departure;
    }

    public function getArrival(): ?Destination
    {
        return $this->arrival;
    }

    public function setArrival(Destination $arrival): void
    {
        $this->arrival = $arrival;
    }

    public function getMarketingCarrier(): string
    {
        return $this->marketingCarrier;
    }

    public function setMarketingCarrier(string $marketingCarrier): void
    {
        $this->marketingCarrier = $marketingCarrier;
    }

    public function getOperatingCarrier(): string
    {
        return $this->operatingCarrier;
    }

    public function setOperatingCarrier(string $operatingCarrier): void
    {
        $this->operatingCarrier = $operatingCarrier;
    }

    public function getEft(): string
    {
        return $this->eft;
    }

    public function setEft(string $eft): void
    {
        $this->eft = $eft;
    }

    public function getFlightNumber(): string
    {
        return $this->flightNumber;
    }

    public function setFlightNumber(string $flightNumber): void
    {
        $this->flightNumber = $flightNumber;
    }

    public function getAircraft(): ?Aircraft
    {
        return $this->aircraft;
    }

    public function setAircraft(Aircraft $aircraft): void
    {
        $this->aircraft = $aircraft;
    }

    public function getLayover(): ?string
    {
        return $this->layover;
    }

    public function setLayover(string $layover): void
    {
        $this->layover = $layover;
    }

    /**
     * @return Commission[]
     */
    public function getCommissions(): array
    {
        return $this->commissions;
    }

    public function setCommissions(array $commissions): void
    {
        $this->commissions = $commissions;
    }

    public function getLayoverMinutes(): ?string
    {
        return $this->layoverMinutes;
    }

    public function setLayoverMinutes(?string $layoverMinutes): void
    {
        $this->layoverMinutes = $layoverMinutes;
    }
}