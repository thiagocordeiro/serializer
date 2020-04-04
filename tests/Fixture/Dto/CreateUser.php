<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

use Test\Serializer\Fixture\Vo\Email;
use Test\Serializer\Fixture\Vo\IpAddress;

class CreateUser
{
    /** @var string */
    private $name;

    /** @var IpAddress */
    private $ipAddress;

    /** @var Email */
    private $email;

    public function __construct(string $name, IpAddress $ipAddress, Email $email)
    {
        $this->name = $name;
        $this->ipAddress = $ipAddress;
        $this->email = $email;
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
}
