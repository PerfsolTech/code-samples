<?php


namespace App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment;

use JMS\Serializer\Annotation as JMS;

class Eft
{
    /**
     * @JMS\Type(name="string")
     */
    private ?string $hm = null;
    /**
     * @JMS\Type(name="int")
     */
    private ?int $minutes = null;

    public function __construct(int $hours = 0, int $minutes = 0)
    {
        $this->setMinutes(($hours * 60) + $minutes);
        $this->setHm(sprintf("%sh %sm", $hours, $minutes));
    }

    /**
     * @return string|null
     */
    public function getHm(): ?string
    {
        return $this->hm;
    }

    /**
     * @param string|null $hm
     */
    public function setHm(?string $hm): void
    {
        $this->hm = $hm;
    }

    /**
     * @return int|null
     */
    public function getMinutes(): ?int
    {
        return $this->minutes;
    }

    /**
     * @param int|null $minutes
     */
    public function setMinutes(?int $minutes): void
    {
        $this->minutes = $minutes;
    }
}