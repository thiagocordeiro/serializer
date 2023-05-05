<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\ReadOnly;

class Address
{
    public readonly string $street;
    public readonly int $number;
    public readonly bool $company;
    public readonly Place $place;

    public function __construct(string $street, int $number, bool $company, Place $place)
    {
        $this->street = $street;
        $this->number = $number;
        $this->company = $company;
        $this->place = $place;
    }
}
