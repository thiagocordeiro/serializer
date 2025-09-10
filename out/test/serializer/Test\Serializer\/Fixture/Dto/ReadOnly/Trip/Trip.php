<?php

namespace Test\Serializer\Fixture\Dto\ReadOnly\Trip;

use Test\Serializer\Fixture\Dto\ReadOnly\Place;
use Test\Serializer\Fixture\Dto\ReadOnly\User;

readonly class Trip
{
    /**
     * @param Place[] $stops
     * @param TripStatus[] $status
     * @param string[] $remarks
     */
    public function __construct(
        public User $driver,
        public array $stops,
        public array $status,
        public array $remarks = [],
        public string $description = '',
    ) {
    }
}
