<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use Test\Serializer\Fixture\Dto\SearchQuery;
use Test\Serializer\JsonSerializerTestCase;

class ParseNumericFromStringTest extends JsonSerializerTestCase
{
    private const JSON = <<<JSON
    {
      "limit": "5"
    }
    JSON;

    public function testWhenStringIsGivenForAnIntegerParamThenCreateObject(): void
    {
        $json = self::JSON;

        $parsed = $this->serializer->deserialize($json, SearchQuery::class);

        $this->assertEquals(new SearchQuery(customer: null, creationDate: null, limit: 5), $parsed);
    }
}
