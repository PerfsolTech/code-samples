<?php


namespace App\Core\Amadeus\Model\BookRequest;


use JMS\Serializer\Annotation as JMS;

class Passenger
{
    public const PAX_TYPE_ADT = 'ADT';
    public const PAX_TYPE_CHD = 'CHD';
    public const PAX_TYPE_INF = 'INF';

    /**
     * @JMS\Type(name="string")
     */
    private string $type = "";
    /**
     * @JMS\Type(name="string")
     */
    private string $gender = "m";
    /**
     * @JMS\Type(name="string")
     */
    private string $firstName = "";
    /**
     * @JMS\Type(name="string")
     */
    private ?string $middleName = "";
    /**
     * @JMS\Type(name="string")
     */
    private string $lastName = "";
    /**
     * @JMS\Type(name="DateTime<'Y-m-d'>")
     */
    private ?\DateTime $dateOfBirth = null;
    /**
     * @JMS\Type(name="string")
     */
    private ?string $passportNumber = "";
    /**
     * @JMS\Type(name="DateTime<'Y-m-d'>")
     */
    private ?\DateTime $passportExp = null;
    /**
     * @JMS\Type(name="string")
     */
    private ?string $programName = "";
    /**
     * @JMS\Type(name="string")
     */
    private ?string $membershipNumber = "";

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): void
    {
        $this->middleName = $middleName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTime $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getPassportNumber(): ?string
    {
        return $this->passportNumber;
    }

    public function setPassportNumber(?string $passportNumber): void
    {
        $this->passportNumber = $passportNumber;
    }

    public function getProgramName(): ?string
    {
        return $this->programName;
    }

    public function setProgramName(?string $programName): void
    {
        $this->programName = $programName;
    }

    public function getMembershipNumber(): ?string
    {
        return $this->membershipNumber;
    }

    public function setMembershipNumber(?string $membershipNumber): void
    {
        $this->membershipNumber = $membershipNumber;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function getPassportExp(): ?\DateTime
    {
        return $this->passportExp;
    }

    public function setPassportExp(?\DateTime $passportExp): void
    {
        $this->passportExp = $passportExp;
    }
}