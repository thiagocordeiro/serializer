<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Custom;

use Serializer\Encoder;
use Test\Serializer\Fixture\Dto\Custom\Trip;
use Throwable;

class CustomTripEncoder extends Encoder
{
    /**
     * @param object|Trip $object
     * @return string[]|mixed[]
     * @throws Throwable
     */
    public function encode(object $object): array
    {
        return [
            'from' => $object->getFrom(),
            'to' => $object->getTo(),
            'type' => $object->getType(),
            'vehicle' => $this->serializer()->encode($object->getVehicle()),
            'custom' => 'foo:bar',
        ];
    }
}
