<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\TestCase;
use Serializer\ClassFactory;
use Serializer\JsonSerializer;
use Serializer\Serializer;
use Test\Serializer\Fixture\ValueObject\Collection\PlaceIterableCollection;
use Test\Serializer\Fixture\ValueObject\Place;

class IterableCollectionTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const COLLECTION = <<<JSON
    [
      {
        "country": "United States",
        "city": "New York"
      },
      {
        "country": "Netherlands",
        "city": "Amsterdam"
      },
      {
        "country": "Brazil",
        "city": "São Paulo"
      }
    ]
JSON;

    /** @var Serializer */
    private $serializer;

    protected function setUp(): void
    {
        $this->serializer = new JsonSerializer(
            new ClassFactory(self::CACHE_DIR, true)
        );
    }

    public function testWhenGivenJsonThenParseIntoObject(): void
    {
        $json = self::COLLECTION;

        $parsed = $this->serializer->deserialize($json, PlaceIterableCollection::class);

        $this->assertEquals(
            new PlaceIterableCollection(
                new Place('New York', 'United States'),
                new Place('Amsterdam', 'Netherlands'),
                new Place('São Paulo', 'Brazil')
            ),
            $parsed
        );
    }

    public function testWhenGivenObjectThenParseIntoJson(): void
    {
        $json = new PlaceIterableCollection(
            new Place('New York', 'United States'),
            new Place('Amsterdam', 'Netherlands'),
            new Place('São Paulo', 'Brazil')
        );

        $parsed = $this->serializer->serialize($json, PlaceIterableCollection::class);

        $this->assertJsonStringEqualsJsonString(self::COLLECTION, $parsed);
    }
}
