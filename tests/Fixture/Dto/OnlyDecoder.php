<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

class OnlyDecoder
{
    /** @var string */
    private $foo;

    /** @var float */
    private $bar;

    public function __construct(string $foo, float $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            'foo' => $this->foo,
            'bar' => $this->bar,
        ];
    }
}
