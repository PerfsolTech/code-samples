<?php


namespace App\Core\Amadeus\Model\SearchResponse\FiltersBoundary;

use JMS\Serializer\Annotation as JMS;

class Airport
{
    /**
     * @JMS\Type(name="string")
     */
    private string $iata;
    /**
     * @JMS\Type(name="string")
     */
    private string $name;

    public function __construct(string $iata = '', string $name = '')
    {
        $this->iata = $iata;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIata(): string
    {
        return $this->iata;
    }

    /**
     * @param string $iata
     */
    public function setIata(string $iata): void
    {
        $this->iata = $iata;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}