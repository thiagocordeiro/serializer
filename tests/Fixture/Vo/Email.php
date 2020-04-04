<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Vo;

use InvalidArgumentException;

class Email
{
    /** @var string */
    private $email;

    public function __construct(string $email)
    {
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address');
        }

        $this->email = $email;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
