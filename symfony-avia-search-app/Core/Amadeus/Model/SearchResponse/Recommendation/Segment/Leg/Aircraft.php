<?php


namespace App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg;

use JMS\Serializer\Annotation as JMS;

class Aircraft
{
    /**
     * @JMS\Type(name="string")
     */
    private string $code = '';
    /**
     * @JMS\Type(name="string")
     */
    private string $name = '';

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}