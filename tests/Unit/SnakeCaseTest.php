<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use DateTimeImmutable;
use Test\Serializer\Fixture\Dto\SnakeUser;
use Test\Serializer\JsonSerializerTestCase;

final class SnakeCaseTest extends JsonSerializerTestCase
{
    private const JSON = <<<JSON
    {
      "user_name": "Arthur Dent",
      "started_at": "2024-02-16T17:05:05+0000"
    }
    JSON;

    private SnakeUser $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new SnakeUser('Arthur Dent', new DateTimeImmutable('2024-02-16 17:05:05'));
    }

    public function testGivenTheJsonThenParseIntoObject(): void
    {
        $json = self::JSON;

        $parsed = $this->serializer->deserialize($json, SnakeUser::class);

        $this->assertEquals($this->user, $parsed);
    }

    public function testGivenTheObjectThenParseIntoJson(): void
    {
        $object = $this->user;

        $parsed = $this->serializer->serialize($object);

        $this->assertJsonStringEqualsJsonString(self::JSON, $parsed);
    }
}
