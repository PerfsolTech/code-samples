<?php


namespace App\Core\Amadeus\Model\SearchResponse\FiltersBoundary;

use JMS\Serializer\Annotation as JMS;

class Eft
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
    private array $minutes;

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

    /**
     * @return string
     */
    public function getArrival(): string
    {
        return $this->arrival;
    }

    /**
     * @param string $arrival
     */
    public function setArrival(string $arrival): void
    {
        $this->arrival = $arrival;
    }

    public function getMinutes(): array
    {
        return $this->minutes;
    }

    public function setMinutes(array $minutes): void
    {
        $this->minutes = $minutes;
    }

    public function addMinutes(int $minutes): void
    {
        $this->minutes[] = $minutes;
    }

}