<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\Attributes\Test;
use Test\Serializer\Fixture\Dto\AccountType;
use Test\Serializer\Fixture\Dto\DefaultValues;
use Test\Serializer\JsonSerializerTestCase;

class DefaultValuesTest extends JsonSerializerTestCase
{
    #[Test] public function givenThePayloadWhenClassIsReadOnlyThenParseValues(): void
    {
        $json = <<<JSON
        {
        }
        JSON;

        $object = $this->serializer->deserialize($json, DefaultValues::class);

        $this->assertEquals(
            new DefaultValues(
                bool: true,
                int: 15,
                string: 'foo-bar',
                float: 100.99,
                array: [],
                type: AccountType::SAVING,
            ),
            $object,
        );
    }

    #[Test] public function givenTheObjectThenParseIntoPayload(): void
    {
        $object = new DefaultValues(
            bool: false,
            int: 20,
            string: 'something-else',
            float: 88.54,
            array: [10, 30, 60],
            type: AccountType::CHECKING,
        );

        $json = $this->serializer->serialize($object);

        $this->assertJsonStringEqualsJsonString(
            $json,
            <<<JSON
            {
                "bool": false,
                "int": 20,
                "string": "something-else",
                "float": 88.54,
                "array": [10, 30, 60],
                "type": "checking"
            }
            JSON,
        );
    }
}
