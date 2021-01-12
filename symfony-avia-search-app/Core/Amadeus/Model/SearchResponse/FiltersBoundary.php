<?php


namespace App\Core\Amadeus\Model\SearchResponse;


use JMS\Serializer\Annotation as JMS;

class FiltersBoundary
{
    /**
     * @JMS\Type(name="array<string,App\Core\Amadeus\Model\SearchResponse\FiltersBoundary\Airport>")
     */
    private array $airports;
    /**
     * @JMS\Type(name="array<string,App\Core\Amadeus\Model\SearchResponse\FiltersBoundary\Airline>")
     */
    private array $airlines;
    /**
     * @JMS\Type(name="array<int>")
     */
    private array $stops;
    /**
     * @JMS\Type(name="array<App\Core\Amadeus\Model\SearchResponse\FiltersBoundary\Eft>")
     */
    private array $eft;
    /**
     * @JMS\Type(name="array<App\Core\Amadeus\Model\SearchResponse\FiltersBoundary\DepartureArrivalTimes>")
     * @JMS\SerializedName("dat")
     */
    private array $departureArrivalTimes;

    public function getAirports(): array
    {
        return $this->airports;
    }

    public function setAirports(array $airports): void
    {
        $this->airports = $airports;
    }

    public function getAirlines(): array
    {
        return $this->airlines;
    }

    public function setAirlines(array $airlines): void
    {
        $this->airlines = $airlines;
    }

    public function getStops(): array
    {
        return $this->stops;
    }

    public function setStops(array $stops): void
    {
        $this->stops = $stops;
    }

    public function getEft(): array
    {
        return $this->eft;
    }

    public function setEft(array $eft): void
    {
        $this->eft = $eft;
    }

    public function getDepartureArrivalTimes(): array
    {
        return $this->departureArrivalTimes;
    }

    public function setDepartureArrivalTimes(array $departureArrivalTimes): void
    {
        $this->departureArrivalTimes = $departureArrivalTimes;
    }
}