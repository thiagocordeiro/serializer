<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\ReadOnly;

class User
{
    public readonly string $name;
    public readonly int $age;
    public readonly float $height;
    public readonly ?Address $address;

    public function __construct(string $name, int $age, float $height = 1.50, ?Address $address = null)
    {
        $this->name = $name;
        $this->age = $age;
        $this->height = $height;
        $this->address = $address;
    }
}
