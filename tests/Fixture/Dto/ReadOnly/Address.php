<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\ReadOnly;

readonly class Address
{
    public function __construct(
        public string $street,
        public int $number,
        public bool $main,
        public Place $place,
    ) {
    }
}
