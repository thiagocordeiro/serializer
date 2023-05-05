<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

class Order
{
    /**
     * @param string[] $tags
     */
    public function __construct(public readonly array $tags)
    {
        // promoted
    }
}
