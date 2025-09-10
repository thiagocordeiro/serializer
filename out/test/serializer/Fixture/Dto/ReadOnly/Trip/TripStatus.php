<?php

namespace Test\Serializer\Fixture\Dto\ReadOnly\Trip;

enum TripStatus: string
{
    case STARTED = 'Started';
    case FINALIZED = 'Finalized';
}
