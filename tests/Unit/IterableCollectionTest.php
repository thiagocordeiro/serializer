<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\TestCase;
use Serializer\Builder\Decoder\DecoderFactory;
use Serializer\Builder\Decoder\FileLoader\PipelineDecoderFileLoader;
use Serializer\Builder\Encoder\EncoderFactory;
use Serializer\Builder\Encoder\FileLoader\PipelineEncoderFileLoader;
use Serializer\JsonSerializer;
use Serializer\Serializer;
use Test\Serializer\Fixture\Dto\Collection\PlaceIterableCollection;
use Test\Serializer\Fixture\Dto\Place;

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

    private Serializer $serializer;

    protected function setUp(): void
    {
        $encoder = new EncoderFactory(PipelineEncoderFileLoader::full(self::CACHE_DIR));
        $decoder = new DecoderFactory(PipelineDecoderFileLoader::full(self::CACHE_DIR));

        $this->serializer = new JsonSerializer($encoder, $decoder);
    }

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
