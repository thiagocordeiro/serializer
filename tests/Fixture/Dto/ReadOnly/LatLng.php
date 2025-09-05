<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\ReadOnly;

readonly class LatLng
{
    public function __construct(
        public float $lat,
        public float $lng,
    ) {
    }
}
