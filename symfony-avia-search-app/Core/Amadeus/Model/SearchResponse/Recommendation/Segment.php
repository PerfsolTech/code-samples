<?php


namespace App\Core\Amadeus\Model\SearchResponse\Recommendation;


use App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Eft;
use App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Fare;
use App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg;
use App\Entity\Commission;
use JMS\Serializer\Annotation as JMS;

class Segment
{
    /**
     * @JMS\Type(name="int")
     */
    private int $ref = -1;
    /**
     * Elapse Flying Time
     * @JMS\Type(name="App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Eft")
     */
    private ?Eft $eft = null;
    /**
     * Majority carrier
     * @JMS\Type(name="string")
     */
    private string $mcx = '';
    /**
     * @var Leg[]
     * @JMS\Type(name="array<App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg>")
     */
    private array $legs = [];

    /**
     * @var Fare[]
     * @JMS\Type(name="array<App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Fare>")
     */
    private array $fares = [];

    /**
     * @var Commission[]
     * @JMS\Type(name="array<App\Entity\Commission>")
     */
    private array $commissions = [];

    public function getEft(): ?Eft
    {
        return $this->eft;
    }

    public function setEft(?Eft $eft): void
    {
        $this->eft = $eft;
    }

    public function getMcx(): string
    {
        return $this->mcx;
    }

    public function setMcx(string $mcx): void
    {
        $this->mcx = $mcx;
    }

    /**
     * @return Leg[]
     */
    public function getLegs(): array
    {
        return $this->legs;
    }

    /**
     * @param Leg[] $legs
     */
    public function setLegs(array $legs): void
    {
        $this->legs = $legs;
    }

    public function getDeparture()
    {
        return $this->getLegs()[0]->getDeparture();
    }

    public function getArrival()
    {
        return $this->getLegs()[count($this->getLegs()) - 1]->getArrival();
    }

    /**
     * @return Fare[]
     */
    public function getFares(): array
    {
        return $this->fares;
    }

    /**
     * @param Fare[] $fares
     */
    public function setFares(array $fares): void
    {
        $this->fares = $fares;
    }

    public function getRef(): int
    {
        return $this->ref;
    }

    public function setRef(int $ref): void
    {
        $this->ref = $ref;
    }

    /**
     * @return Commission[]
     */
    public function getCommissions(): array
    {
        return $this->commissions;
    }

    /**
     * @param Commission[] $commissions
     */
    public function setCommissions(array $commissions): void
    {
        $this->commissions = $commissions;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("stops")
     * @JMS\Type(name="int")
     */
    public function getStops(): int
    {
        return count($this->getLegs()) - 1;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("cabin_class")
     * @JMS\Type(name="string")
     */
    public function getCabinClass(): string
    {
        $map = ['M' => "Economy", 'W' => "Economy", 'Y' => "Economy", 'C' => "Business", 'F' => "First", '' => ''];
        $fare = $this->getFares()[0] ?? null;
        $key = $fare->getCabinClass() ?? '';

        return $map[$key];
    }
}