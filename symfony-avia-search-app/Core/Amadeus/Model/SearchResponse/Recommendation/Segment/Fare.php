<?php


namespace App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment;

use JMS\Serializer\Annotation as JMS;

class Fare
{
    /**
     * @JMS\Type(name="string")
     */
    private string $fareBasis = '';

    /**
     * @JMS\Type(name="string")
     */
    private string $fareType = '';

    /**
     * @JMS\Type(name="int")
     */
    private int $segRef = -1;

    /**
     * @JMS\Type(name="string")
     */
    private string $bookingClass = '';

    /**
     * @JMS\Type(name="string")
     */
    private string $cabinClass = '';

    /**
     * @JMS\Type(name="string")
     */
    private string $avl = '';

    /**
     * @JMS\Type(name="string")
     */
    private string $passengerType = '';

    public function getFareBasis(): string
    {
        return $this->fareBasis;
    }

    public function setFareBasis(string $fareBasis): void
    {
        $this->fareBasis = $fareBasis;
    }

    public function getSegRef(): int
    {
        return $this->segRef;
    }

    public function setSegRef(int $segRef): void
    {
        $this->segRef = $segRef;
    }

    public function getFareType(): string
    {
        return $this->fareType;
    }

    public function setFareType(string $fareType): void
    {
        $this->fareType = $fareType;
    }

    public function getBookingClass(): string
    {
        return $this->bookingClass;
    }

    public function setBookingClass(string $bookingClass): void
    {
        $this->bookingClass = $bookingClass;
    }

    public function getCabinClass(): string
    {
        return $this->cabinClass;
    }

    public function setCabinClass(string $cabinClass): void
    {
        $this->cabinClass = $cabinClass;
    }

    public function getAvl(): string
    {
        return $this->avl;
    }

    public function setAvl(string $avl): void
    {
        $this->avl = $avl;
    }

    public function getPassengerType(): string
    {
        return $this->passengerType;
    }

    public function setPassengerType(string $passengerType): void
    {
        $this->passengerType = $passengerType;
    }
}