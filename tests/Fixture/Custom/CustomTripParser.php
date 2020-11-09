<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Custom;

use Exception;
use Serializer\Exception\MissingOrInvalidProperty;
use Serializer\Parser;
use Test\Serializer\Fixture\Dto\Custom\Airplane;
use Test\Serializer\Fixture\Dto\Custom\Bus;
use Test\Serializer\Fixture\Dto\Custom\Trip;
use Test\Serializer\Fixture\Dto\Custom\Vehicle;
use Throwable;
use TypeError;

class CustomTripParser extends Parser
{
    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function decode($data, ?string $propertyName = null): object
    {
        try {
            $object = new Trip(
                $data->from ?? '',
                $data->to ?? '',
                $data->type ?? '',
                $this->parseVehicle($data->type, $data)
            );
        } catch (TypeError $e) {
            throw new MissingOrInvalidProperty($e, ['from', 'to', 'type', 'vehicle']);
        }

        return $object;
    }

    /**
     * @param object|Trip $object
     * @return string[]|mixed[]
     * @throws Throwable
     */
    public function encode(object $object)
    {
        return [
            'from' => $object->getFrom(),
            'to' => $object->getTo(),
            'type' => $object->getType(),
            'vehicle' => $this->serializer()->serializeData($object->getVehicle()),
        ];
    }

    private function parseVehicle(string $type, object $data): Vehicle
    {
        if ($type === 'flight') {
            return $this->serializer()->deserializeData($data->vehicle ?? null, Airplane::class, 'vehicle');
        }

        if ($type === 'road') {
            return $this->serializer()->deserializeData($data->vehicle ?? null, Bus::class, 'vehicle');
        }

        throw new Exception('Parameter "vehicle" invalid');
    }
}
