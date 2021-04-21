<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

class OnlyDecoder
{
    private string $foo;
    private float $bar;

    public function __construct(string $foo, float $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    /**
     * @return array<string, string|float>
     */
    public function toArray(): array
    {
        return [
            'foo' => $this->foo,
            'bar' => $this->bar,
        ];
    }
}
