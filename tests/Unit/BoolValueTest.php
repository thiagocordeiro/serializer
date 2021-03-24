<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\TestCase;
use Serializer\DecoderFactory;
use Serializer\EncoderFactory;
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

    /** @var JsonSerializer */
    private $serializer;

    protected function setUp(): void
    {
        $encoder = new EncoderFactory(self::CACHE_DIR, true);
        $decoder = new DecoderFactory(self::CACHE_DIR, true);

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
