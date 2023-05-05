<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use Test\Serializer\Fixture\Dto\Order;
use Test\Serializer\JsonSerializerTestCase;

class ScalarListTest extends JsonSerializerTestCase
{
    private const JSON = <<<JSON
    {
      "tags": [
        "new",
        "original",
        "imported"
      ]
    }
    JSON;

    public function testGivenAJsonThenConvertIntoTheObject(): void
    {
        $json = self::JSON;

        $parsed = $this->serializer->deserialize($json, Order::class);

        $this->assertEquals(new Order(tags: ["new", "original", "imported"]), $parsed);
    }

    public function testGivenTheObjectThenConvertIntoAJson(): void
    {
        $json = self::JSON;

        $serialized = $this->serializer->serialize(new Order(tags: ["new", "original", "imported"]));

        $this->assertJsonStringEqualsJsonString($json, $serialized);
    }
}
