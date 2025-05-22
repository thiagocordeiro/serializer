<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\Attributes\Test;
use Test\Serializer\Fixture\Dto\PartiallyReadOnlyValue;
use Test\Serializer\JsonSerializerTestCase;

class PartiallyReadOnlyValueTest extends JsonSerializerTestCase
{
    private const VALUE_OBJECT_BODY = <<<JSON
    {
      "id": 123,
      "foo": "FOO",
      "bar": "BAR"
    }
    JSON;

    #[Test] public function givenThePayloadWhenClassIsReadOnlyThenParseValues(): void
    {
        $object = $this->serializer->deserialize(self::VALUE_OBJECT_BODY, PartiallyReadOnlyValue::class);

        $this->assertEquals(new PartiallyReadOnlyValue(123, 'FOO', 'BAR'), $object);
    }

    #[Test] public function givenTheObjectThenParseIntoPayload(): void
    {
        $object = new PartiallyReadOnlyValue(123, 'FOO', 'BAR');

        $json = $this->serializer->serialize($object);

        $this->assertJsonStringEqualsJsonString($json, self::VALUE_OBJECT_BODY);
    }
}
