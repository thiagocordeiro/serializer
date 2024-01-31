<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use Test\Serializer\Fixture\Dto\EmptyObject;
use Test\Serializer\JsonSerializerTestCase;

class ConstructEmptyObjectTest extends JsonSerializerTestCase
{
    private const JSON = <<<JSON
    []
    JSON;

    public function testDeserializeBooleanGetters(): void
    {
        $json = self::JSON;

        $parsed = $this->serializer->deserialize($json, EmptyObject::class);

        $this->assertEquals(new EmptyObject(), $parsed);
    }
}
