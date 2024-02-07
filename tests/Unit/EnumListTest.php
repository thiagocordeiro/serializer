<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use Test\Serializer\Fixture\Dto\AccountType;
use Test\Serializer\Fixture\Dto\AccountTypeList;
use Test\Serializer\JsonSerializerTestCase;

class EnumListTest extends JsonSerializerTestCase
{
    private const ENUM_LIST = <<<JSON
    {
      "types": [
        "checking",
        "saving"
      ]
    }
    JSON;

    public function testAJsonOfEnumListThenConvertIntoAnObject(): void
    {
        $json = self::ENUM_LIST;

        $list = $this->serializer->deserialize($json, AccountTypeList::class);

        $this->assertEquals(new AccountTypeList(types: [AccountType::checking, AccountType::saving]), $list);
    }

    public function testGivenTheListOfEnumsThenParseIntoObject(): void
    {
        $object = new AccountTypeList(types: [AccountType::checking, AccountType::saving]);

        $json = $this->serializer->serialize($object);

        $this->assertJsonStringEqualsJsonString(self::ENUM_LIST, $json);
    }
}
