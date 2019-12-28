<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\DTO;

class Address
{
    /** @var string */
    private $street;

    /** @var int */
    private $number;

    /** @var bool */
    private $company;

    /** @var Place */
    private $place;

    public function __construct(string $street, int $number, bool $company, Place $place)
    {
        $this->street = $street;
        $this->number = $number;
        $this->company = $company;
        $this->place = $place;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function isCompany(): bool
    {
        return $this->company;
    }

    public function getPlace(): Place
    {
        return $this->place;
    }
}
