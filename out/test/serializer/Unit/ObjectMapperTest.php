<?php

declare(strict_types=0);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\Attributes\Test;
use Serializer\Exception\UnableToParseValue;
use Serializer\ObjectMapper;
use Serializer\Reader\Reader;
use Serializer\Reader\RuntimeReader;
use Test\Serializer\Fixture\Dto\ReadOnly\AccountHolder;
use Test\Serializer\Fixture\Dto\ReadOnly\LatLng;
use Test\Serializer\Fixture\Dto\ReadOnly\Response;
use Test\Serializer\SerializerTestCase;

class ObjectMapperTest extends SerializerTestCase
{
    private Reader $reader;

    protected function setUp(): void
    {
        $this->reader = new RuntimeReader();
    }

    #[Test] public function given_a_data_then_parse_into_the_object(): void
    {
        $data = AccountHolder::data();
        $mapper = new ObjectMapper($this->reader, []);

        $accountHolder = $mapper->readValue(AccountHolder::class, $data);

        $this->assertEquals(AccountHolder::thiagoCordeiro(), $accountHolder);
    }

    #[Test] public function given_a_invalid_value_then_throw_unable_to_parse(): void
    {
        $data = AccountHolder::data();
        $mapper = new ObjectMapper($this->reader, []);
        $data['address']['place']['position'] = '-26.9013, -48.6655';

        $exception = $this->expectThrows(fn() => $mapper->readValue(AccountHolder::class, $data));

        $this->assertEquals(
            new UnableToParseValue(
                ['address', 'place', 'position'],
                [
                    'lat' => 'float',
                    'lng' => 'float',
                ],
                '-26.9013, -48.6655',
            ),
            $exception,
        );
    }

    #[Test] public function given_custom_reader_then_parse_into_the_object(): void
    {
        $data = AccountHolder::data();
        $data['address']['place']['position'] = '-26.9013, -48.6655';

        $mapper = new ObjectMapper(
            defaultTypeReader: $this->reader,
            typeMappers: [
                LatLng::class => [
                    'reader' => fn(string $value) => new LatLng(...explode(',', $value)),
                ],
            ],
        );

        $accountHolder = $mapper->readValue(AccountHolder::class, $data);

        $this->assertEquals(AccountHolder::thiagoCordeiro(), $accountHolder);
    }

    #[Test] public function given__when__then(): void
    {
        $mapper = new ObjectMapper($this->reader, []);
        $data = Response::data();

        $response = $mapper->readValue(Response::class, $data);

        $this->assertEquals(Response::firstPage(), $response);
    }
}
