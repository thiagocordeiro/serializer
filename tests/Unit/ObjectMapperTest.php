<?php

namespace Test\Serializer\Unit;

use PHPUnit\Framework\Attributes\Test;
use Serializer\Exception\UnableToParseValue;
use Serializer\Reader\Reader;
use Serializer\Reader\RuntimeReader;
use Serializer\ObjectMapper;
use Test\Serializer\Fixture\Dto\ReadOnly\AccountHolder;
use Test\Serializer\Fixture\Dto\ReadOnly\LatLng;
use Test\Serializer\SerializerTestCase;

class ObjectMapperTest extends SerializerTestCase
{
    private array $data;
    private object $object;
    private Reader $mapper;

    protected function setUp(): void
    {
        $this->data = AccountHolder::data();
        $this->object = AccountHolder::thiagoCordeiro();
        $this->mapper = new RuntimeReader();
    }

    #[Test] public function given_a_data_then_parse_into_the_object(): void
    {
        $mapper = new ObjectMapper($this->mapper, []);

        $accountHolder = $mapper->readValue(AccountHolder::class, $this->data);

        $this->assertEquals($this->object, $accountHolder);
    }

    #[Test] public function given_a_invalid_value_then_throw_unable_to_parse(): void
    {
        $mapper = new ObjectMapper($this->mapper, []);
        $data = $this->data;
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
        $data = $this->data;
        $data['address']['place']['position'] = '-26.9013, -48.6655';

        $mapper = new ObjectMapper(
            defaultTypeMapper: $this->mapper,
            typeMappers: [
                LatLng::class => [
                    'reader' => fn(string $value) => new LatLng(...explode(',', $value)),
                ],
            ],
        );

        $accountHolder = $mapper->readValue(AccountHolder::class, $data);

        $this->assertEquals($this->object, $accountHolder);
    }
}
