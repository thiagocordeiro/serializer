<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

use DateTimeImmutable;

final class SnakeUser
{
    public function __construct(public readonly string $user_name, public readonly DateTimeImmutable $started_at)
    {
    }
}
