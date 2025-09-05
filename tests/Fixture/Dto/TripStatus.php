<?php

namespace Test\Serializer\Fixture\Dto;

enum TripStatus: string
{
    case STARTED = 'Started';
    case FINALIZED = 'Finalized';
}
