<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

class BankAccount
{
    private string $number;
    private AccountType $type;

    public function __construct(string $number, AccountType $type)
    {
        $this->number = $number;
        $this->type = $type;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getType(): AccountType
    {
        return $this->type;
    }
}
