<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\ReadOnly;

use Test\Serializer\Fixture\Dto\AccountType;

readonly class BankAccount
{
    public function __construct(
        public string $number,
        public AccountType $type,
    ) {
    }
}
