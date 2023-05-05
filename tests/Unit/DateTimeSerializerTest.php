<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use DateTime;
use DateTimeImmutable;
use Serializer\Exception\InvalidDateTimeProperty;
use Test\Serializer\Fixture\Dto\DateTimeValueObject;
use Test\Serializer\JsonSerializerTestCase;

class DateTimeSerializerTest extends JsonSerializerTestCase
{
    private const DATETIME = <<<JSON
    {
      "expiresAt": "2019-01-01T18:30:15+0000",
      "createdAt": "2019-01-01T12:12:01+0000"
    }
    JSON;

    private const DATETIME_NULLABLE = <<<JSON
    {
      "expiresAt": "2019-01-01T18:30:15+0000",
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
      "expiresAt": "2019-01-01T18:30:15+0000",
      "createdAt": "invalid-datetime"
    }
    JSON;

    public function testWhenGivenJsonThenParseIntoObject(): void
    {
        $json = self::DATETIME;

        $parsed = $this->serializer->deserialize($json, DateTimeValueObject::class);

        $this->assertEquals(
            new DateTimeValueObject(
                new DateTime('2019-01-01 18:30:15'),
                new DateTimeImmutable('2019-01-01 12:12:01'),
            ),
            $parsed,
        );
    }

    public function testWhenGivenJsonWithNullableValueThenParseIntoObject(): void
    {
        $json = self::DATETIME_NULLABLE;

        $parsed = $this->serializer->deserialize($json, DateTimeValueObject::class);

        $this->assertEquals(
            new DateTimeValueObject(
                new DateTime('2019-01-01 18:30:15'),
                null,
            ),
            $parsed,
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

    public function testWhenGivenADateTimeThenConvertToString(): void
    {
        $data = new DateTimeValueObject(
            new DateTime('2019-01-01 18:30:15'),
            new DateTimeImmutable('2019-01-01 12:12:01'),
        );

        $json = $this->serializer->serialize($data);

        $this->assertJsonStringEqualsJsonString(self::DATETIME, $json);
    }
}
