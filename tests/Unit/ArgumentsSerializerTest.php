<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use Test\Serializer\Fixture\Dto\ArgumentsValueObject;
use Test\Serializer\Fixture\Dto\Place;
use Test\Serializer\Fixture\Dto\User;
use Test\Serializer\JsonSerializerTestCase;

class ArgumentsSerializerTest extends JsonSerializerTestCase
{
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
                ],
            ),
            $parsed,
        );
    }
}
