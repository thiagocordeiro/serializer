<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\ValueObject\Custom;

interface Vehicle
{
    public function getMaxPassengers(): int;
}
