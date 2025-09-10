<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\ReadOnly;

readonly class Place
{
    public function __construct(
        public string $city,
        public string $country,
        public LatLng $position,
    ) {
    }
}
