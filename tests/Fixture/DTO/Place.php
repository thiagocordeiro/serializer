<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\DTO;

class Place
{
    /** @var string */
    private $city;

    /** @var string string */
    private $country;

    public function __construct(string $city, string $country = 'Netherlands')
    {
        $this->city = $city;
        $this->country = $country;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
