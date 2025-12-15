<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

readonly class DefaultValues
{
    /**
     * @param int[] $array
     */
    public function __construct(
        public bool $bool = true,
        public int $int = 15,
        public string $string = 'foo-bar',
        public float $float = 100.99,
        public array $array = [],
        public AccountType $type = AccountType::SAVING,
    ) {
    }
}
