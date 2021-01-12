<?php


namespace App\Core\Amadeus\Model\SearchResponse\FiltersBoundary;

use JMS\Serializer\Annotation as JMS;

class DepartureArrivalTimes
{
    /**
     * @JMS\Type(name="string")
     */
    private string $key;
    /**
     * @JMS\Type(name="string")
     */
    private string $departure;
    /**
     * @JMS\Type(name="string")
     */
    private string $arrival;
    /**
     * @JMS\Type(name="array<int>")
     */
    private array $outbounds;
    /**
     * @JMS\Type(name="array<int>")
     */
    private array $inbounds;

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getDeparture(): string
    {
        return $this->departure;
    }

    public function setDeparture(string $departure): void
    {
        $this->departure = $departure;
    }

    public function getArrival(): string
    {
        return $this->arrival;
    }

    public function setArrival(string $arrival): void
    {
        $this->arrival = $arrival;
    }

    public function getOutbounds(): array
    {
        return $this->outbounds;
    }

    public function setOutbounds(array $outbounds): void
    {
        $this->outbounds = $outbounds;
    }

    public function addOutboundTimestamp(int $ts): void
    {
        $this->outbounds[] = $ts;
    }

    public function getInbounds(): array
    {
        return $this->inbounds;
    }

    public function setInbounds(array $inbounds): void
    {
        $this->inbounds = $inbounds;
    }

    public function addInboundTimestamp(int $ts): void
    {
        $this->inbounds[] = $ts;
    }
}