<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

enum AccountType: string
{
    case CHECKING = 'checking';
    case SAVING = 'saving';
}
