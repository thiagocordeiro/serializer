<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\ValueObject;

use DateTime;
use DateTimeImmutable;

class DateTimeValueObject
{
    /** @var DateTime */
    private $expiresAt;

    /** @var DateTimeImmutable */
    private $createdAt;

    public function __construct(DateTime $expiresAt, ?DateTimeImmutable $createdAt)
    {
        $this->expiresAt = $expiresAt;
        $this->createdAt = $createdAt;
    }

    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }
}
