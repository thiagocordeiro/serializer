<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Serializer\ClassFactory;
use Serializer\Exception\InvalidDateTimeProperty;
use Serializer\JsonSerializer;
use Serializer\Serializer;
use Test\Serializer\Fixture\ValueObject\DateTimeValueObject;

class DateTimeSerializerTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const DATETIME = <<<JSON
{
    "expiresAt": "2019-01-01 18:30:15",
    "createdAt": "2019-01-01 12:12:01"
}
JSON;

    private const DATETIME_NULLABLE = <<<JSON
{
    "expiresAt": "2019-01-01 18:30:15",
    "createdAt": null
}
JSON;

    private const DATETIME_INVALID_1 = <<<JSON
{
    "expiresAt": "invalid-datetime",
    "createdAt": null
}
JSON;

    private const DATETIME_INVALID_2 = <<<JSON
{
    "expiresAt": "2019-01-01 18:30:15",
    "createdAt": "invalid-datetime"
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

    public function testWhenGivenJsonThenParseIntoObject(): void
    {
        $json = self::DATETIME;

        $parsed = $this->serializer->deserialize($json, DateTimeValueObject::class);

        $this->assertEquals(
            new DateTimeValueObject(
                new DateTime('2019-01-01 18:30:15'),
                new DateTimeImmutable('2019-01-01 12:12:01')
            ),
            $parsed
        );
    }

    public function testWhenGivenJsonWithNullableValueThenParseIntoObject(): void
    {
        $json = self::DATETIME_NULLABLE;

        $parsed = $this->serializer->deserialize($json, DateTimeValueObject::class);

        $this->assertEquals(
            new DateTimeValueObject(
                new DateTime('2019-01-01 18:30:15'),
                null
            ),
            $parsed
        );
    }

    public function testWhenGivenJsonWithInvalidDateTimeImmutableThenThrowException(): void
    {
        $this->expectException(InvalidDateTimeProperty::class);
        $this->expectExceptionMessage('Parameter "expiresAt" is invalid');

        $this->serializer->deserialize(self::DATETIME_INVALID_1, DateTimeValueObject::class);
    }

    public function testWhenGivenJsonWithInvalidDateTimeThenThrowException(): void
    {
        $this->expectException(InvalidDateTimeProperty::class);
        $this->expectExceptionMessage('Parameter "createdAt" is invalid');

        $this->serializer->deserialize(self::DATETIME_INVALID_2, DateTimeValueObject::class);
    }
}
