<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\TestCase;
use Serializer\Builder\Decoder\DecoderFactory;
use Serializer\Builder\Decoder\FileLoader\PipelineDecoderFileLoader;
use Serializer\Builder\Encoder\EncoderFactory;
use Serializer\Builder\Encoder\FileLoader\PipelineEncoderFileLoader;
use Serializer\JsonSerializer;
use Test\Serializer\Fixture\Dto\AnyBool;

class BoolValueTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const JSON = <<<JSON
    {
      "active": true,
      "blocked": false,
      "restrictions": false
    }
    JSON;

    private JsonSerializer $serializer;

    protected function setUp(): void
    {
        $encoder = new EncoderFactory(PipelineEncoderFileLoader::full(self::CACHE_DIR));
        $decoder = new DecoderFactory(PipelineDecoderFileLoader::full(self::CACHE_DIR));

        $this->serializer = new JsonSerializer($encoder, $decoder);
    }

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
