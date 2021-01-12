<?php


namespace App\Core\Amadeus\Model\SearchResponse;


use App\Core\Amadeus\Model\Price;
use App\Core\Amadeus\Model\SearchResponse\Recommendation\BagAllowance;
use App\Core\Amadeus\Model\SearchResponse\Recommendation\MiniRule;
use App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment;
use App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg;
use App\Entity\Commission;
use JMS\Serializer\Annotation as JMS;
use Money\Currency;

/**
 * Class Recommendation
 * @package App\Core\Amadeus\Model\SearchResponse
 */
class Recommendation
{
    /**
     * @JMS\Type(name="int")
     */
    private int $itemNumberId = -1;
    /**
     * @JMS\Type(name="string")
     */
    private string $key = '';
    /**
     * @JMS\Type(name="App\Core\Amadeus\Model\Price")
     */
    private ?Price $price = null;
    /**
     * @JMS\Exclude()
     */
    private ?Currency $currency = null;
    /**
     * @var Segment[]
     * @JMS\Type(name="array<App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment>")
     */
    private array $segments = [];
    /**
     * @var Commission[]
     * @JMS\Type(name="array<App\Entity\Commission>")
     */
    private array $commissions = [];
    /**
     * @JMS\Type(name="App\Core\Amadeus\Model\SearchResponse\Recommendation\BagAllowance")
     */
    private BagAllowance $bagAllowance;
    /**
     * @JMS\Type(name="array<App\Core\Amadeus\Model\SearchResponse\Recommendation\MiniRule>")
     */
    private array $miniRules = [];

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return Segment[]
     */
    public function getSegments(): array
    {
        return $this->segments;
    }

    /**
     * @param Segment[] $segments
     */
    public function setSegments(array $segments): void
    {
        $this->segments = $segments;
    }

    public function setItemNumberId($number)
    {
        $this->itemNumberId = $number;
    }

    public function getItemNumberId(): int
    {
        return $this->itemNumberId;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("currency")
     */
    public function getCurrencyCode(): string
    {
        return empty($this->getCurrency()) ? '' : $this->getCurrency()->getCode();
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

    public function getBagAllowance(): BagAllowance
    {
        return $this->bagAllowance;
    }

    public function setBagAllowance(BagAllowance $bagAllowance): void
    {
        $this->bagAllowance = $bagAllowance;
    }

    /**
     * @param array|Segment[] $segments
     * @return string
     */
    public static function makeKey(array $segments): string
    {
        $key = '';

        /** @var Segment $segment */
        foreach ($segments as $segment) {
            /** @var Leg $leg */
            foreach ($segment->getLegs() as $leg) {
                $departure = $leg->getDeparture();
                $arrival = $leg->getArrival();
                $key .=
                    $departure->getIata() . $departure->getDate() . $departure->getTime() .
                    $arrival->getIata() . $arrival->getDate() . $arrival->getTime() .
                    $leg->getMarketingCarrier() . $leg->getOperatingCarrier() . $leg->getFlightNumber();
            }
        }

        return md5($key);
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("stops_max")
     * @JMS\Type(name="int")
     */
    public function getStopsMax(): int
    {
        $stops = [];
        foreach ($this->getSegments() as $segment) {
            $stops[] = $segment->getStops();
        }

        return max($stops);
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("eft")
     * @JMS\Type(name="array<array>")
     */
    public function getEft(): array
    {
        $values = [];
        foreach ($this->getSegments() as $segment) {
            $departure = $segment->getLegs()[0]->getDeparture()->getMunicipality();
            $arrival = $segment->getLegs()[array_key_last($segment->getLegs())]->getArrival()->getMunicipality();
            $values[] = [$departure, $arrival, $segment->getEft()->getMinutes()];
        }

        return $values;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("eft_total")
     * @JMS\Type(name="int")
     */
    public function getEftTotal(): int
    {
        return array_reduce($this->getSegments(), function ($carry, $segment) {
            $carry += $segment->getEft()->getMinutes();
            return $carry;
        });
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("dat")
     * @JMS\Type(name="array<array>")
     */
    public function getDepartureArrivalTimes(): array
    {
        $values = [];
        foreach ($this->getSegments() as $segment) {
            $departure = $segment->getLegs()[0]->getDeparture();
            $arrival = $segment->getLegs()[array_key_last($segment->getLegs())]->getArrival();
            $values[] = [
                $departure->getMunicipality(),
                $arrival->getMunicipality(),
                $departure->getDatetime()->getTimestamp(),
                $arrival->getDatetime()->getTimestamp(),
            ];
        }

        return $values;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("airlines")
     * @JMS\Type(name="array<string>")
     */
    public function getAirlines(): array
    {
        $values = [];
        foreach ($this->getSegments() as $segment) {
            foreach ($segment->getLegs() as $leg) {
                $values[] = empty($leg->getOperatingCarrier()) ? $leg->getMarketingCarrier() : $leg->getOperatingCarrier();
            }
        }

        return array_unique($values);
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("airports")
     * @JMS\Type(name="array<string>")
     */
    public function getAirports(): array
    {
        $values = [];
        foreach ($this->getSegments() as $segment) {
            foreach ($segment->getLegs() as $leg) {
                $values[] = $leg->getDeparture()->getIata();
                $values[] = $leg->getArrival()->getIata();
            }
        }

        return array_unique($values);
    }

    /**
     * @return array|MiniRule[]
     */
    public function getMiniRules(): array
    {
        return $this->miniRules;
    }

    /**
     * @param array|MiniRule[] $miniRules
     */
    public function setMiniRules(array $miniRules): void
    {
        $this->miniRules = $miniRules;
    }
}