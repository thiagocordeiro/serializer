<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\DTO;

class User
{
    /** @var string */
    private $name;

    /** @var int */
    private $age;

    /** @var float */
    private $height;

    /** @var Address|null */
    private $address;

    public function __construct(string $name, int $age, float $height = 1.50, ?Address $address = null)
    {
        $this->name = $name;
        $this->age = $age;
        $this->height = $height;
        $this->address = $address;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function setHeight(float $height): void
    {
        $this->height = $height;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): void
    {
        $this->address = $address;
    }
}
