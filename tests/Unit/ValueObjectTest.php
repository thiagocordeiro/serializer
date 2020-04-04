<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Serializer\ClassFactory;
use Serializer\JsonSerializer;
use Serializer\Serializer;
use Test\Serializer\Fixture\Dto\CreateUser;
use Test\Serializer\Fixture\Vo\Age;
use Test\Serializer\Fixture\Vo\Email;
use Test\Serializer\Fixture\Vo\Height;
use Test\Serializer\Fixture\Vo\IpAddress;

class ValueObjectTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const VALUE_OBJECT_BODY = <<<JSON
    {
      "name": "Arthur Dent",
      "ipAddress": "127.0.0.1",
      "email": "serializer@test.com",
      "age": 20,
      "height": 1.75
    }
JSON;

    private const INVALID_IP = <<<JSON
    {
      "name": "Arthur Dent",
      "ipAddress": "900.800.700.600",
      "email": "serializer@test.com",
      "age": 20,
      "height": 1.75
    }
JSON;

    private const INVALID_EMAIL = <<<JSON
    {
      "name": "Arthur Dent",
      "ipAddress": "127.0.0.1",
      "email": "serializer@test",
      "age": 20,
      "height": 1.75
    }
JSON;

    private const INVALID_AGE = <<<JSON
    {
      "name": "Arthur Dent",
      "ipAddress": "127.0.0.1",
      "email": "serializer@test.com",
      "age": 1,
      "height": 1.75
    }
JSON;

    private const INVALID_HEIGHT = <<<JSON
    {
      "name": "Arthur Dent",
      "ipAddress": "127.0.0.1",
      "email": "serializer@test.com",
      "age": 20,
      "height": 0.01
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
            new CreateUser(
                'Arthur Dent',
                new IpAddress('127.0.0.1'),
                new Email('serializer@test.com'),
                new Age(20),
                new Height(1.75)
            ),
            $parsed
        );
    }

    public function testWhenDtoContainsValueObjectPropertyThenEncodeIt(): void
    {
        $dto = new CreateUser(
            'Arthur Dent',
            new IpAddress('127.0.0.1'),
            new Email('serializer@test.com'),
            new Age(20),
            new Height(1.75)
        );

        $parsed = $this->serializer->serialize($dto, CreateUser::class);

        $this->assertJsonStringEqualsJsonString(self::VALUE_OBJECT_BODY, $parsed);
    }

    /**
     * @dataProvider invalidValueDataProvider
     */
    public function testInvalidValueOnBody(string $body, string $message): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->serializer->deserialize($body, CreateUser::class);
    }

    public function invalidValueDataProvider(): array
    {
        return [
            'Invalid ip address' => ['body' => self::INVALID_IP, 'message' => 'Invalid ip address'],
            'Invalid ip email' => ['body' => self::INVALID_EMAIL, 'message' => 'Invalid email address'],
            'Age out of range' => ['body' => self::INVALID_AGE, 'message' => 'Age out of range'],
            'Invalid height' => ['body' => self::INVALID_HEIGHT, 'message' => 'No humans should have less then 2cm'],
        ];
    }
}
