<?php

declare(strict_types=1);

namespace Test\Serializer\Unit\ReadOnly;

use Test\Serializer\Fixture\Dto\ReadOnly\Address;
use Test\Serializer\Fixture\Dto\ReadOnly\Place;
use Test\Serializer\Fixture\Dto\ReadOnly\User;
use Test\Serializer\JsonSerializerTestCase;
use Throwable;

class DataClassSerializerTest extends JsonSerializerTestCase
{
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
