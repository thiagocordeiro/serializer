<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\Attributes\Test;
use Test\Serializer\Fixture\Dto\ReadOnlyValue;
use Test\Serializer\JsonSerializerTestCase;

class ReadOnlyValueTest extends JsonSerializerTestCase
{
    private const VALUE_OBJECT_BODY = <<<JSON
    {
      "foo": "FOO",
      "bar": "BAR"
    }
    JSON;

    #[Test] public function givenThePayloadWhenClassIsReadOnlyThenParseValues(): void
    {
        $object = $this->serializer->deserialize(self::VALUE_OBJECT_BODY, ReadOnlyValue::class);

        $this->assertEquals(new ReadOnlyValue('FOO', 'BAR'), $object);
    }

    #[Test] public function givenTheObjectThenParseIntoPayload(): void
    {
        $object = new ReadOnlyValue('FOO', 'BAR');

        $json = $this->serializer->serialize($object);

        $this->assertJsonStringEqualsJsonString($json, self::VALUE_OBJECT_BODY);
    }
}
