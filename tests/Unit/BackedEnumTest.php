<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use Serializer\Exception\MissingOrInvalidProperty;
use Test\Serializer\Fixture\Dto\AccountType;
use Test\Serializer\Fixture\Dto\BankAccount;
use Test\Serializer\JsonSerializerTestCase;

class BackedEnumTest extends JsonSerializerTestCase
{
    private const VALUE_OBJECT_BODY = <<<JSON
    {
      "number": "12345-6",
      "type": "checking"
    }
    JSON;

    public function testDeserializeBackedEnum(): void
    {
        $json = self::VALUE_OBJECT_BODY;

        $parsed = $this->serializer->deserialize($json, BankAccount::class);

        $this->assertEquals(new BankAccount('12345-6', AccountType::CHECKING), $parsed);
    }

    public function testSerializeBackedEnum(): void
    {
        $object = new BankAccount('12345-6', AccountType::CHECKING);

        $parsed = $this->serializer->serialize($object);

        $this->assertJsonStringEqualsJsonString(self::VALUE_OBJECT_BODY, $parsed);
    }

    public function testInvalidBackedEnum(): void
    {
        $json = <<<JSON
        {
          "number": "12345-6",
          "type": "other"
        }
        JSON;

        $this->expectException(MissingOrInvalidProperty::class);
        $this->expectExceptionMessage('Value "other" is not valid for AccountType(checking, saving)');

        $this->serializer->deserialize($json, BankAccount::class);
    }
}
