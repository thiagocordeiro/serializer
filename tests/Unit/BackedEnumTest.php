<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\TestCase;
use Serializer\Builder\Decoder\DecoderFactory;
use Serializer\Builder\Decoder\FileLoader\PipelineDecoderFileLoader;
use Serializer\Builder\Encoder\EncoderFactory;
use Serializer\Builder\Encoder\FileLoader\PipelineEncoderFileLoader;
use Serializer\JsonSerializer;
use Serializer\Serializer;
use Test\Serializer\Fixture\Dto\AccountType;
use Test\Serializer\Fixture\Dto\BankAccount;

class BackedEnumTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const VALUE_OBJECT_BODY = <<<JSON
    {
      "number": "12345-6",
      "type": "checking"
    }
    JSON;

    private const ENUM_ARRAY = <<<JSON
    {
      "types": [
        "checking",
        "saving",
        "saving",
        "checking",
        "saving"
      ]
    }
    JSON;

    private Serializer $serializer;

    protected function setUp(): void
    {
        $encoder = new EncoderFactory(PipelineEncoderFileLoader::full(self::CACHE_DIR));
        $decoder = new DecoderFactory(PipelineDecoderFileLoader::full(self::CACHE_DIR));

        $this->serializer = new JsonSerializer($encoder, $decoder);
    }

    public function testDeserializeBackedEnum(): void
    {
        $json = self::VALUE_OBJECT_BODY;

        $parsed = $this->serializer->deserialize($json, BankAccount::class);

        $this->assertEquals(new BankAccount('12345-6', AccountType::checking), $parsed);
    }

    public function testSerializeBackedEnum(): void
    {
        $object = new BankAccount('12345-6', AccountType::checking);

        $parsed = $this->serializer->serialize($object);

        $this->assertJsonStringEqualsJsonString(self::VALUE_OBJECT_BODY, $parsed);
    }
}
