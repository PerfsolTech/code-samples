<?php


namespace App\Core\Amadeus\Model;


use App\Core\Amadeus\Model\BookRequest\Passenger;
use JMS\Serializer\Annotation as JMS;

class BookRequest
{
    /**
     * @var Passenger[]
     * @JMS\Type(name="array<App\Core\Amadeus\Model\BookRequest\Passenger>")
     */
    private array $passengers = [];
    /**
     * @JMS\Type(name="bool")
     */
    private bool $forMe = true;
    /**
     * @JMS\Type(name="string")
     */
    private string $email = "";
    /**
     * @JMS\Type(name="string")
     */
    private string $phone = "";
    /**
     * @JMS\Type(name="string")
     */
    private string $ccType = "";
    /**
     * @JMS\Type(name="string")
     */
    private string $ccNumber = "";
    /**
     * @JMS\Type(name="string")
     */
    private string $ccExp = "";
    /**
     * @JMS\Type(name="string")
     */
    private string $ccCvc = "";
    /**
     * @JMS\Type(name="string")
     */
    private string $ccHolderName = "";

    /**
     * @return Passenger[]
     */
    public function getPassengers(): array
    {
        return $this->passengers;
    }

    /**
     * @param Passenger[] $passengers
     */
    public function setPassengers(array $passengers): void
    {
        $this->passengers = $passengers;
    }

    public function addPassenger(Passenger $passenger)
    {
        $this->passengers[] = $passenger;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getCcType(): string
    {
        return $this->ccType;
    }

    public function setCcType(string $ccType): void
    {
        $this->ccType = $ccType;
    }

    public function getCcNumber(): string
    {
        return $this->ccNumber;
    }

    public function setCcNumber(string $ccNumber): void
    {
        $this->ccNumber = $ccNumber;
    }

    public function getCcExp(): string
    {
        return $this->ccExp;
    }

    public function setCcExp(string $ccExp): void
    {
        $this->ccExp = $ccExp;
    }

    public function getCcCvc(): string
    {
        return $this->ccCvc;
    }

    public function setCcCvc(string $ccCvc): void
    {
        $this->ccCvc = $ccCvc;
    }

    public function getCcHolderName(): string
    {
        return $this->ccHolderName;
    }

    public function setCcHolderName(string $ccHolderName): void
    {
        $this->ccHolderName = $ccHolderName;
    }

    public static function make($pasAdtCnt = 0, $paxChdCnt = 0, $paxInfCnt = 0)
    {
        $obj = new self();

        for ($i = 0; $i < $pasAdtCnt; $i++) {
            $pax = new BookRequest\Passenger();
            $pax->setType(BookRequest\Passenger::PAX_TYPE_ADT);
            $obj->addPassenger($pax);
        }
        for ($i = 0; $i < $paxChdCnt; $i++) {
            $pax = new BookRequest\Passenger();
            $pax->setType(BookRequest\Passenger::PAX_TYPE_CHD);
            $obj->addPassenger($pax);
        }
        for ($i = 0; $i < $paxInfCnt; $i++) {
            $pax = new BookRequest\Passenger();
            $pax->setType(BookRequest\Passenger::PAX_TYPE_INF);
            $obj->addPassenger($pax);
        }

        return $obj;
    }

    public function getForMe(): bool
    {
        return $this->forMe;
    }

    public function setForMe(bool $forMe): void
    {
        $this->forMe = $forMe;
    }

    public function paxCount()
    {
        $doCount = function ($type) {
            $closure = function ($carry, $pax) use ($type) {
                /** @var Passenger $pax */
                if ($pax->getType() == $type) {
                    $carry += 1;
                }

                return $carry;
            };

            return array_reduce($this->getPassengers(), $closure);
        };

        $adtCnt = $doCount(Passenger::PAX_TYPE_ADT);
        $chdCnt = $doCount(Passenger::PAX_TYPE_CHD);
        $infCnt = $doCount(Passenger::PAX_TYPE_INF);

        return ($infCnt > $adtCnt ? $infCnt : $adtCnt) + $chdCnt;
    }
}