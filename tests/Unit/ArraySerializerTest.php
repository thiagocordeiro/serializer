<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\TestCase;
use Serializer\ArraySerializer;
use Serializer\Builder\Decoder\DecoderFactory;
use Serializer\Builder\Decoder\FileLoader\PipelineDecoderFileLoader;
use Serializer\Builder\Encoder\EncoderFactory;
use Serializer\Builder\Encoder\FileLoader\PipelineEncoderFileLoader;
use Serializer\Exception\MissingOrInvalidProperty;
use Test\Serializer\Fixture\Dto\Address;
use Test\Serializer\Fixture\Dto\Collection\UserCollection;
use Test\Serializer\Fixture\Dto\Place;
use Test\Serializer\Fixture\Dto\User;

class ArraySerializerTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const USER_1 = [
        "name" => "Arthur Dent",
        "age" => 38,
        "height" => 1.69,
        "address" => null,
    ];

    private const USER_2 = [
        "name" => "Chuck Norris",
        "age" => 109,
        "height" => 1.75,
        "address" => [
            "street" => "Times Square",
            "number" => 500,
            "company" => false,
            "place" => [
                "country" => "United States",
                "city" => "New York",
            ],
        ],
    ];

    private const USER_3 = [
        "name" => "Tony Stark",
        "age" => 42,
    ];

    private const USER_4 = [
        "name" => "Kevin Bacon",
        "age" => 42,
        "height" => 1.73,
        "address" => null,
    ];

    private const USER_5 = [
        "name" => "Zinedine Zidane",
        "age" => 40,
        "height" => 1.84,
        "address" => [
            "street" => "Champs Elysees",
            "number" => 444,
            "company" => false,
        ],
    ];

    private ArraySerializer $serializer;

    protected function setUp(): void
    {
        $encoder = new EncoderFactory(PipelineEncoderFileLoader::full(self::CACHE_DIR));
        $decoder = new DecoderFactory(PipelineDecoderFileLoader::full(self::CACHE_DIR));

        $this->serializer = new ArraySerializer($encoder, $decoder);
    }

    public function testWhenGivenAnArrayThenParseIntoObject(): void
    {
        $data = self::USER_1;

        $parsed = $this->serializer->deserialize($data, User::class);

        $this->assertEquals(new User('Arthur Dent', 38, 1.69), $parsed);
    }

    public function testWhenGivenJsonWithNestedObjectsThenDeserialize(): void
    {
        $data = self::USER_2;

        $parsed = $this->serializer->deserialize($data, User::class);

        $this->assertEquals(
            new User(
                'Chuck Norris',
                109,
                1.75,
                new Address('Times Square', 500, false, new Place('New York', 'United States')),
            ),
            $parsed,
        );
    }

    public function testWhenValueIsNotSetAndParamHasDefaultValueThenSetDefaultValue(): void
    {
        $data = self::USER_3;

        $parsed = $this->serializer->deserialize($data, User::class);

        $this->assertEquals(new User('Tony Stark', 42, 1.50), $parsed);
    }

    public function testWhenGivenJsonArrayThenParseIntoArrayOfObjects(): void
    {
        $data = [self::USER_1, self::USER_3, self::USER_4];

        $parsed = $this->serializer->deserialize($data, User::class);

        $this->assertEquals([
            new User('Arthur Dent', 38, 1.69),
            new User('Tony Stark', 42, 1.50),
            new User('Kevin Bacon', 42, 1.73),
        ], $parsed);
    }

    public function testWhenGivenAnArrayOnAParamThenParseObjects(): void
    {
        $data = ['users' => [self::USER_1, self::USER_3, self::USER_4]];

        $parsed = $this->serializer->deserialize($data, UserCollection::class);

        $this->assertEquals(new UserCollection([
            new User('Arthur Dent', 38, 1.69),
            new User('Tony Stark', 42, 1.50),
            new User('Kevin Bacon', 42, 1.73),
        ]), $parsed);
    }

    public function testWhenRequiredValueIsNotProvidedThenThrowException(): void
    {
        $data = self::USER_5;

        $this->expectException(MissingOrInvalidProperty::class);
        $this->expectExceptionMessage('Parameter "place" is required');

        $this->serializer->deserialize($data, User::class);
    }

    public function testWhenGivenObjectThenParseIntoJson(): void
    {
        $object = new User('Arthur Dent', 38, 1.69);

        $serialized = $this->serializer->serialize($object);

        $this->assertEquals(self::USER_1, $serialized);
    }

    public function testWhenGivenObjectsWithNestedObjectsThenSerialize(): void
    {
        $object = new User(
            'Chuck Norris',
            109,
            1.75,
            new Address('Times Square', 500, false, new Place('New York', 'United States')),
        );

        $serialized = $this->serializer->serialize($object);

        $this->assertEquals(self::USER_2, $serialized);
    }

    public function testWhenGivenObjectArrayThenParseIntoJson(): void
    {
        $array = [
            new User('Arthur Dent', 38, 1.69),
            new User('Kevin Bacon', 42, 1.73),
        ];

        $serialized = $this->serializer->serialize($array);

        $this->assertEquals([self::USER_1, self::USER_4], $serialized);
    }

    public function testWhenGivenAnArrayOnAParamThenParseJson(): void
    {
        $collection = new UserCollection([
            new User('Arthur Dent', 38, 1.69),
            new User('Kevin Bacon', 42, 1.73),
        ]);

        $serialized = $this->serializer->serialize($collection);

        $this->assertEquals(['users' => [self::USER_1, self::USER_4]], $serialized);
    }
}
