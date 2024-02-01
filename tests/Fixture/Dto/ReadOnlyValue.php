<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

readonly class ReadOnlyValue
{
    public function __construct(public string $foo, public string $bar)
    {
    }
}
