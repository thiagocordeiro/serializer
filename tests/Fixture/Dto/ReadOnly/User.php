<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\ReadOnly;

readonly class User
{
    public function __construct(
        public string $name,
        public int $age,
        public float $height = 1.50,
        public ?Address $address = null,
    ) {
    }
}
