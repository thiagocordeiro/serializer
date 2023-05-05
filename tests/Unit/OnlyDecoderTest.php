<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use Serializer\Exception\PropertyHasNoGetter;
use Test\Serializer\Fixture\Dto\OnlyDecoder;
use Test\Serializer\JsonSerializerTestCase;

class OnlyDecoderTest extends JsonSerializerTestCase
{
    private const FOO_BAR = <<<JSON
    {
      "foo": "Foo",
      "bar": 123.45
    }
    JSON;

    public function testOnlyDecoder(): void
    {
        $json = self::FOO_BAR;

        $parsed = $this->serializer->deserialize($json, OnlyDecoder::class);

        $this->assertEquals(['foo' => 'Foo', 'bar' => 123.45], $parsed->toArray());
    }

    public function testEncodeShouldThrowException(): void
    {
        $object = new OnlyDecoder('foo', 123.45);

        $this->expectExceptionObject(new PropertyHasNoGetter(OnlyDecoder::class, 'foo'));

        $this->serializer->serialize($object);
    }
}
