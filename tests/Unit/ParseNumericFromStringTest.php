<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\TestCase;
use Serializer\Builder\Decoder\DecoderFactory;
use Serializer\Builder\Decoder\FileLoader\PipelineDecoderFileLoader;
use Serializer\Builder\Encoder\EncoderFactory;
use Serializer\Builder\Encoder\FileLoader\PipelineEncoderFileLoader;
use Serializer\JsonSerializer;
use Test\Serializer\Fixture\Dto\SearchQuery;

class ParseNumericFromStringTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const JSON = <<<JSON
    {
      "limit": "5"
    }
    JSON;

    private JsonSerializer $serializer;

    protected function setUp(): void
    {
        $encoder = new EncoderFactory(PipelineEncoderFileLoader::full(self::CACHE_DIR));
        $decoder = new DecoderFactory(PipelineDecoderFileLoader::full(self::CACHE_DIR));

        $this->serializer = new JsonSerializer($encoder, $decoder);
    }

    public function testWhenStringIsGivenForAnIntegerParamThenCreateObject(): void
    {
        $json = self::JSON;

        $parsed = $this->serializer->deserialize($json, SearchQuery::class);

        $this->assertEquals(new SearchQuery(customer: null, creationDate: null, limit: 5), $parsed);
    }
}
