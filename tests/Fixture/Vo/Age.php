<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Vo;

use InvalidArgumentException;

class Age
{
    private int $age;

    public function __construct(int $age)
    {
        if ($age < 18 || $age > 90) {
            throw new InvalidArgumentException('Age out of range');
        }

        $this->age = $age;
    }

    public function __toString(): string
    {
        return (string) $this->age;
    }
}
