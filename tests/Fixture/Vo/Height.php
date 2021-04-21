<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Vo;

use InvalidArgumentException;

class Height
{
    private float $height;

    public function __construct(float $height)
    {
        if ($height < 0.02) {
            throw new InvalidArgumentException('No humans should have less then 2cm');
        }

        $this->height = $height;
    }

    public function __toString(): string
    {
        return (string) $this->height;
    }
}
