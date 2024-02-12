<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Custom;

use Exception;
use Serializer\Decoder;
use Serializer\Exception\MissingOrInvalidProperty;
use Test\Serializer\Fixture\Dto\Custom\Airplane;
use Test\Serializer\Fixture\Dto\Custom\Bus;
use Test\Serializer\Fixture\Dto\Custom\Trip;
use Test\Serializer\Fixture\Dto\Custom\Vehicle;
use Throwable;
use TypeError;

class CustomTripDecoder extends Decoder
{
    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function decode($data, ?string $propertyName = null): object
    {
        try {
            $object = new Trip(
                $data['from'] ?? '',
                $data['to'] ?? '',
                $data['type'] ?? '',
                $this->parseVehicle($data['type'], $data),
            );
        } catch (TypeError $e) {
            throw new MissingOrInvalidProperty($e, ['from', 'to', 'type', 'vehicle']);
        }

        return $object;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function parseVehicle(string $type, array $data): Vehicle
    {
        return match ($type) {
            'flight' => $this->serializer()->decode($data['vehicle'] ?? null, Airplane::class, 'vehicle'),
            'road' => $this->serializer()->decode($data['vehicle'] ?? null, Bus::class, 'vehicle'),
            default => throw new Exception('Parameter "vehicle" invalid'),
        };
    }
}
