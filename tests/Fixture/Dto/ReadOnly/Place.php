<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\ReadOnly;

class Place
{
    public readonly string $city;
    public readonly string $country;

    public function __construct(string $city, string $country = 'Netherlands')
    {
        $this->city = $city;
        $this->country = $country;
    }
}
