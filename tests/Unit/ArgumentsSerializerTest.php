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
use Test\Serializer\Fixture\Dto\ArgumentsValueObject;
use Test\Serializer\Fixture\Dto\Place;
use Test\Serializer\Fixture\Dto\User;

class ArgumentsSerializerTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const ARGS = <<<JSON
    {
      "user": {
        "name": "Tony Stark",
        "age": 42
      },
      "places": [
        {
          "country": "United States",
          "city": "New York"
        },
        {
          "country": "Netherlands",
          "city": "Amsterdam"
        },
        {
          "country": "Germany",
          "city": "Munich"
        }
      ]
    }
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
        $json = self::ARGS;

        $parsed = $this->serializer->deserialize($json, ArgumentsValueObject::class);

        $this->assertEquals(
            new ArgumentsValueObject(
                new User('Tony Stark', 42),
                ...[
                    new Place('New York', 'United States'),
                    new Place('Amsterdam', 'Netherlands'),
                    new Place('Munich', 'Germany'),
                ]
            ),
            $parsed
        );
    }
}
