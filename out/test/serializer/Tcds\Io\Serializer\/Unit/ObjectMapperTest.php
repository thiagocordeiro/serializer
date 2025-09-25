<?php

declare(strict_types=0);

namespace Tcds\Io\Serializer\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Serializer\Exception\UnableToParseValue;
use Tcds\Io\Serializer\Fixture\ReadOnly\AccountHolder;
use Tcds\Io\Serializer\Fixture\ReadOnly\Address;
use Tcds\Io\Serializer\Fixture\ReadOnly\LatLng;
use Tcds\Io\Serializer\Fixture\ReadOnly\Response;
use Tcds\Io\Serializer\Mapper\Reader;
use Tcds\Io\Serializer\ObjectMapper;
use Tcds\Io\Serializer\Runtime\RuntimeReader;
use Tcds\Io\Serializer\SerializerTestCase;

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

    #[Test] public function given_an_object_with_map_param_then_handle_value(): void
    {
        $mapper = new ObjectMapper($this->reader, []);
        $data = Response::data();

        $response = $mapper->readValue(Response::class, $data);

        $this->assertEquals(Response::firstPage(), $response);
    }

    #[Test] public function given_a_map_array_then_handle_value(): void
    {
        $mapper = new ObjectMapper($this->reader, []);
        $type = generic('map', ['string', Address::class]);

        $response = $mapper->readValue($type, [
            'main' => Address::mainAddressData(),
            'other' => Address::otherAddressData(),
        ]);

        $this->assertEquals(
            [
                'main' => Address::mainAddress(),
                'other' => Address::otherAddress(),
            ],
            $response,
        );
    }
}
