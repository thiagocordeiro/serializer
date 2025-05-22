<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

readonly class AccountTypeList
{
    /**
     * @param list<AccountType> $types
     */
    public function __construct(public array $types)
    {
    }
}
