<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\TestCase;
use Serializer\ClassFactory;
use Serializer\JsonSerializer;
use Serializer\Serializer;
use Test\Serializer\Fixture\Dto\Custom\Airplane;
use Test\Serializer\Fixture\Dto\Custom\Bus;
use Test\Serializer\Fixture\Dto\Custom\Trip;

class CustomFactoriesTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';
    private const CUSTOM_NAMESPACE = 'Test\Serializer\Fixture\Custom';

    private const AIRPLANE_TRIP = <<<JSON
    {
        "from": "Amsterdam",
        "to": "New York",
        "type": "flight",
        "vehicle": {
            "airline": "KLM Royal Dutch Airlines",
            "aircraft": "Boeing 777-306ER",
            "registration": "PH-BVF",
            "maxPassengers": 408
        }
    }
JSON;

    private const AIRPLANE_BUS = <<<JSON
    {
        "from": "Amsterdam",
        "to": "Berlin",
        "type": "road",
        "vehicle": {
            "company": "Flixbus",
            "model": "AEC Routemaster",
            "maxPassengers": 64
        }
    }
JSON;

    private const INVALID_TRIP_TYPE = <<<JSON
    {
        "from": "Amsterdam",
        "to": "Berlin",
        "type": "rail",
        "vehicle": {
            "company": "NS",
            "model": "Train",
            "maxPassengers": 700
        }
    }
JSON;

    /** @var Serializer */
    private $serializer;

    protected function setUp(): void
    {
        $this->serializer = new JsonSerializer(
            new ClassFactory(self::CACHE_DIR, true, self::CUSTOM_NAMESPACE)
        );
    }

    public function testWhenGivenACustomAirplaneJsonThenParseIntoObject(): void
    {
        $json = self::AIRPLANE_TRIP;

        $parsed = $this->serializer->deserialize($json, Trip::class);

        $this->assertEquals(
            new Trip(
                'Amsterdam',
                'New York',
                'flight',
                new Airplane('KLM Royal Dutch Airlines', 'Boeing 777-306ER', 'PH-BVF', 408)
            ),
            $parsed
        );
    }

    public function testWhenGivenACustomBusJsonThenParseIntoObject(): void
    {
        $json = self::AIRPLANE_BUS;

        $parsed = $this->serializer->deserialize($json, Trip::class);

        $this->assertEquals(
            new Trip(
                'Amsterdam',
                'Berlin',
                'road',
                new Bus('Flixbus', 'AEC Routemaster', 64)
            ),
            $parsed
        );
    }

    public function testWhenGivenAnInvalidVehicleThenParseThrowException(): void
    {
        $json = self::INVALID_TRIP_TYPE;

        $this->expectExceptionMessage('Parameter "vehicle" invalid');

        $this->serializer->deserialize($json, Trip::class);
    }

    public function testWhenGivenACustomAirplaneTripThenParseIntoJson(): void
    {
        $trip = new Trip(
            'Amsterdam',
            'New York',
            'flight',
            new Airplane('KLM Royal Dutch Airlines', 'Boeing 777-306ER', 'PH-BVF', 408)
        );

        $parsed = $this->serializer->serialize($trip);

        $this->assertJsonStringEqualsJsonString(self::AIRPLANE_TRIP, $parsed);
    }

    public function testWhenGivenACustomBusTripThenParseIntoJson(): void
    {
        $trip = new Trip(
            'Amsterdam',
            'Berlin',
            'road',
            new Bus('Flixbus', 'AEC Routemaster', 64)
        );

        $parsed = $this->serializer->serialize($trip);

        $this->assertJsonStringEqualsJsonString(self::AIRPLANE_BUS, $parsed);
    }
}
