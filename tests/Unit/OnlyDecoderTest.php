<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPStan\Testing\TestCase;
use Serializer\Builder\Decoder\DecoderFactory;
use Serializer\Builder\Decoder\FileLoader\PipelineDecoderFileLoader;
use Serializer\Builder\Encoder\EncoderFactory;
use Serializer\Builder\Encoder\FileLoader\PipelineEncoderFileLoader;
use Serializer\Exception\PropertyHasNoGetter;
use Serializer\JsonSerializer;
use Serializer\Serializer;
use Test\Serializer\Fixture\Dto\OnlyDecoder;

class OnlyDecoderTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const FOO_BAR = <<<JSON
    {
      "foo": "Foo",
      "bar": 123.45
    }
    JSON;

    private Serializer $serializer;

    protected function setUp(): void
    {
        $encoder = new EncoderFactory(PipelineEncoderFileLoader::full(self::CACHE_DIR));
        $decoder = new DecoderFactory(PipelineDecoderFileLoader::full(self::CACHE_DIR));

        $this->serializer = new JsonSerializer($encoder, $decoder);
    }

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
