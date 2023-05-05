<?php

namespace Test\Serializer\Unit\ReadOnly;

use PHPUnit\Framework\TestCase;
use Serializer\Builder\Decoder\DecoderFactory;
use Serializer\Builder\Decoder\FileLoader\PipelineDecoderFileLoader;
use Serializer\Builder\Encoder\EncoderFactory;
use Serializer\Builder\Encoder\FileLoader\PipelineEncoderFileLoader;
use Serializer\JsonSerializer;
use Test\Serializer\Fixture\Dto\ReadOnly\Address;
use Test\Serializer\Fixture\Dto\ReadOnly\Place;
use Test\Serializer\Fixture\Dto\ReadOnly\User;
use Throwable;

class DataClassSerializerTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const USER = <<<JSON
    {
      "name": "Chuck Norris",
      "age": 109,
      "height": 1.75,
      "address": {
        "street": "Times Square",
        "number": 500,
        "company": false,
        "place": {
          "country": "United States",
          "city": "New York"
        }
      }
    }
    JSON;

    protected function setUp(): void
    {
        $encoder = new EncoderFactory(PipelineEncoderFileLoader::full(self::CACHE_DIR));
        $decoder = new DecoderFactory(PipelineDecoderFileLoader::full(self::CACHE_DIR));

        $this->serializer = new JsonSerializer($encoder, $decoder);
    }

    /**
     * @throws Throwable
     */
    public function testWhenGivenJsonWithNestedObjectsThenDeserialize(): void
    {
        $json = self::USER;

        $parsed = $this->serializer->deserialize($json, User::class);

        $this->assertEquals(
            new User(
                'Chuck Norris',
                109,
                1.75,
                new Address('Times Square', 500, false, new Place('New York', 'United States')),
            ),
            $parsed,
        );
    }

    public function testWhenGivenObjectsWithNestedObjectsThenSerialize(): void
    {
        $object = new User(
            'Chuck Norris',
            109,
            1.75,
            new Address('Times Square', 500, false, new Place('New York', 'United States')),
        );

        $serialized = $this->serializer->serialize($object);

        $this->assertJsonStringEqualsJsonString(self::USER, $serialized);
    }
}
