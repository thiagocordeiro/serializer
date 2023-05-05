<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use Test\Serializer\Fixture\Dto\Collection\PlaceIterableCollection;
use Test\Serializer\Fixture\Dto\Place;
use Test\Serializer\JsonSerializerTestCase;

class IterableCollectionTest extends JsonSerializerTestCase
{
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

    public function testWhenGivenJsonThenParseIntoObject(): void
    {
        $json = self::COLLECTION;

        $parsed = $this->serializer->deserialize($json, PlaceIterableCollection::class);

        $this->assertEquals(
            new PlaceIterableCollection(
                new Place('New York', 'United States'),
                new Place('Amsterdam', 'Netherlands'),
                new Place('São Paulo', 'Brazil'),
            ),
            $parsed,
        );
    }

    public function testWhenGivenObjectThenParseIntoJson(): void
    {
        $json = new PlaceIterableCollection(
            new Place('New York', 'United States'),
            new Place('Amsterdam', 'Netherlands'),
            new Place('São Paulo', 'Brazil'),
        );

        $parsed = $this->serializer->serialize($json, PlaceIterableCollection::class);

        $this->assertJsonStringEqualsJsonString(self::COLLECTION, $parsed);
    }
}
