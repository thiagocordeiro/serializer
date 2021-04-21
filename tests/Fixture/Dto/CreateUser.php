<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

use Test\Serializer\Fixture\Vo\Age;
use Test\Serializer\Fixture\Vo\Email;
use Test\Serializer\Fixture\Vo\Height;
use Test\Serializer\Fixture\Vo\IpAddress;

class CreateUser
{
    private string $name;
    private IpAddress $ipAddress;
    private Email $email;
    private Age $age;
    private Height $height;

    public function __construct(string $name, IpAddress $ipAddress, Email $email, Age $age, Height $height)
    {
        $this->name = $name;
        $this->ipAddress = $ipAddress;
        $this->email = $email;
        $this->age = $age;
        $this->height = $height;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIpAddress(): IpAddress
    {
        return $this->ipAddress;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getAge(): Age
    {
        return $this->age;
    }

    public function getHeight(): Height
    {
        return $this->height;
    }
}
