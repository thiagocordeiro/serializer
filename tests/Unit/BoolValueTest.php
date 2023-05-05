<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use Test\Serializer\Fixture\Dto\AnyBool;
use Test\Serializer\JsonSerializerTestCase;

class BoolValueTest extends JsonSerializerTestCase
{
    private const JSON = <<<JSON
    {
      "active": true,
      "blocked": false,
      "restrictions": false
    }
    JSON;

    public function testDeserializeBooleanGetters(): void
    {
        $json = self::JSON;

        $parsed = $this->serializer->deserialize($json, AnyBool::class);

        $this->assertEquals(new AnyBool(true, false, false), $parsed);
    }

    public function testSerializeBooleanGetters(): void
    {
        $object = new AnyBool(true, false, false);

        $parsed = $this->serializer->serialize($object);

        $this->assertJsonStringEqualsJsonString(self::JSON, $parsed);
    }
}
