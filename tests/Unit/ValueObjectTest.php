<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\TestCase;
use Serializer\ClassFactory;
use Serializer\JsonSerializer;
use Serializer\Serializer;
use Test\Serializer\Fixture\Dto\CreateUser;
use Test\Serializer\Fixture\Vo\Email;
use Test\Serializer\Fixture\Vo\IpAddress;

class ValueObjectTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const VALUE_OBJECT_BODY = <<<JSON
    {
      "name": "Arthur Dent",
      "ipAddress": "127.0.0.1",
      "email": "serializer@test.com"
    }
JSON;

    /** @var Serializer */
    private $serializer;

    protected function setUp(): void
    {
        $this->serializer = new JsonSerializer(
            new ClassFactory(self::CACHE_DIR, true)
        );
    }

    public function testWhenBodyContainsValueObjectPropertyThenParseIt(): void
    {
        $json = self::VALUE_OBJECT_BODY;

        $parsed = $this->serializer->deserialize($json, CreateUser::class);

        $this->assertEquals(
            new CreateUser('Arthur Dent', new IpAddress('127.0.0.1'), new Email('serializer@test.com')),
            $parsed
        );
    }

    public function testWhenDtoContainsValueObjectPropertyThenEncodeIt(): void
    {
        $dto = new CreateUser(
            'Arthur Dent',
            new IpAddress('127.0.0.1'),
            new Email('serializer@test.com')
        );

        $parsed = $this->serializer->serialize($dto, CreateUser::class);

        $this->assertJsonStringEqualsJsonString(self::VALUE_OBJECT_BODY, $parsed);
    }
}
