<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

enum AccountType: string
{
    case checking = 'checking';
    case saving = 'saving';
}
