<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

use DateTime;
use DateTimeImmutable;

class DateTimeValueObject
{
    private DateTime $expiresAt;
    private ?DateTimeImmutable $createdAt;

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
