<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Vo;

use InvalidArgumentException;

class IpAddress
{
    private string $address;

    public function __construct(string $address)
    {
        if (false === filter_var($address, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException('Invalid ip address');
        }

        $this->address = $address;
    }

    public function __toString(): string
    {
        return $this->address;
    }
}
