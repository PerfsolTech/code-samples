<?php


namespace App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg;

use JMS\Serializer\Annotation as JMS;

/**
 * Class Location
 * @package App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg
 */
class Destination
{
    /**
     * @JMS\Type(name="string")
     */
    private string $iata = '';
    /**
     * @JMS\Type(name="string")
     */
    private ?string $airport = null;
    /**
     * @JMS\Type(name="string")
     */
    private ?string $municipality = null;
    /**
     * @JMS\Type(name="string")
     */
    private ?string $country = null;
    /**
     * @JMS\Type(name="string")
     */
    private string $date = '';
    /**
     * @JMS\Type(name="string")
     */
    private string $time = '';
    /**
     * @JMS\Type(name="DateTime")
     */
    private ?\DateTime $datetime = null;
    /**
     * @JMS\Type(name="string")
     */
    private string $terminal = '';
    /**
     * @JMS\Exclude
     */
    private string $continent;

    public function getIata(): string
    {
        return $this->iata;
    }

    public function setIata(string $iata): void
    {
        $this->iata = $iata;
    }

    public function getAirport(): ?string
    {
        return $this->airport;
    }

    public function setAirport(?string $airport): void
    {
        $this->airport = $airport;
    }

    public function getMunicipality(): string
    {
        return $this->municipality;
    }

    public function setMunicipality(?string $municipality): void
    {
        $this->municipality = $municipality;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getTime(): string
    {
        return $this->time;
    }

    public function setTime(string $time): void
    {
        $this->time = $time;
    }

    public function getDatetime(): \DateTime
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTime $datetime): void
    {
        $this->datetime = $datetime;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("date_gia")
     */
    public function getDateTimeGia()
    {
        return $this->getDatetime()->format('g:ia');
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("date_DjSM")
     */
    public function getDateTimeDjSM(): string
    {
        return $this->getDatetime()->format('D jS M');
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("date_DjSMY")
     */
    public function getDateTimeDjSMY(): string
    {
        return $this->getDatetime()->format('D jS M Y');
    }

    public function getTerminal(): string
    {
        return $this->terminal;
    }

    public function setTerminal(string $terminal): void
    {
        $this->terminal = $terminal;
    }

    public function setContinent(string $continent)
    {
        $this->continent = $continent;
    }

    public function getContinent(): string
    {
        return $this->continent;
    }
}