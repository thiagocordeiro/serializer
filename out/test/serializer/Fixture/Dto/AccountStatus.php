<?php

namespace Test\Serializer\Fixture\Dto;

enum AccountStatus: string
{
    case ACTIVE = 'Active';
    case FINALISED = 'Finalized';
}
