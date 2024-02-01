<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

class PartiallyReadOnlyValue
{
    public function __construct(private int $id, public readonly string $foo, public readonly string $bar)
    {
    }

    public function id(): int
    {
        return $this->id;
    }
}
