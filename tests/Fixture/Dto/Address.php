<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

class Address
{
    private string $street;
    private int $number;
    private bool $company;
    private Place $place;

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
