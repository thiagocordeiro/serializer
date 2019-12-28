<?php

declare(strict_types=1);

namespace Test\Serializer\Unit;

use PHPUnit\Framework\TestCase;
use Serializer\ClassFactory;
use Serializer\Serializer;
use Test\Serializer\Fixture\DTO\Address;
use Test\Serializer\Fixture\DTO\Collection\UserCollection;
use Test\Serializer\Fixture\DTO\Place;
use Test\Serializer\Fixture\DTO\User;
use TypeError;

class SerializerTest extends TestCase
{
    private const CACHE_DIR = __DIR__ . '/../../var/cache';

    private const USER_1 = <<<JSON
{
  "name": "Arthur Dent",
  "age": 38,
  "height": 1.69
}
JSON;

    private const USER_2 = <<<JSON
{
  "name": "Chuck Norris",
  "age": 109,
  "height": 1.75,
  "address": {
    "street": "Times Square",
    "number": 500,
    "company": false,
    "place": {
      "country": "United States",
      "city": "New York"
    }
  }
}
JSON;

    private const USER_3 = <<<JSON
{
  "name": "Tony Stark",
  "age": 42
}

JSON;
    private const USER_4 = <<<JSON
{
  "name": "Kevin Bacon",
  "age": 42,
  "height": 1.73
}
JSON;

    private const USER_5 = <<<JSON
{
  "name": "Zinedine Zidane",
  "age": 40,
  "height": 1.84,
  "address": {
    "street": "Champs Elysees",
    "number": 444,
    "company": false
  }
}
JSON;

    /** @var Serializer */
    private $serializer;

    protected function setUp(): void
    {
        array_map(function (string $file) {
            unlink($file);
        }, glob(sprintf('%s/serializer/*.php', self::CACHE_DIR)));

        $this->serializer = new Serializer(
            new ClassFactory(self::CACHE_DIR)
        );
    }

    public function testWhenGivenValueThenParseIntoObject(): void
    {
        $json = self::USER_1;

        $parsed = $this->serializer->deserialize($json, User::class);

        $this->assertEquals(new User('Arthur Dent', 38, 1.69), $parsed);
    }

    public function testWhenGivenJsonWithNestedObjectsThenDeserialize(): void
    {
        $json = self::USER_2;

        $parsed = $this->serializer->deserialize($json, User::class);

        $this->assertEquals(
            new User(
                'Chuck Norris',
                109,
                1.75,
                new Address(
                    'Times Square',
                    500,
                    false,
                    new Place('New York', 'United States')
                )
            ),
            $parsed
        );
    }

    public function testWhenValueIsNotSetAndParamHasDefaultValueThenSetDefaultValue(): void
    {
        $json = self::USER_3;

        $parsed = $this->serializer->deserialize($json, User::class);

        $this->assertEquals(new User('Tony Stark', 42, 1.50), $parsed);
    }

    public function testWhenGivenJsonArrayThenParseIntoArrayOfObjects(): void
    {
        $json = sprintf('[%s,%s,%s]', self::USER_1, self::USER_3, self::USER_4);

        $parsed = $this->serializer->deserialize($json, User::class);

        $this->assertEquals([
            new User('Arthur Dent', 38, 1.69),
            new User('Tony Stark', 42, 1.50),
            new User('Kevin Bacon', 42, 1.73),
        ], $parsed);
    }

    public function testWhenGivenAnArrayOnAnParamThenParseObjects(): void
    {
        $json = sprintf('{"users": [%s,%s,%s]}', self::USER_1, self::USER_3, self::USER_4);

        $parsed = $this->serializer->deserialize($json, UserCollection::class);

        $this->assertEquals(new UserCollection([
            new User('Arthur Dent', 38, 1.69),
            new User('Tony Stark', 42, 1.50),
            new User('Kevin Bacon', 42, 1.73),
        ]), $parsed);
    }

    public function testWhenRequiredValueIsNotProvidedThenThrowException(): void
    {
        $json = self::USER_5;

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 4 passed to %s must be an instance of %s, null given, called in %s on line 18',
                'Test\Serializer\Fixture\DTO\Address::__construct()',
                'Test\Serializer\Fixture\DTO\Place',
                realpath(self::CACHE_DIR) . '/serializer/Test_Serializer_Fixture_DTO_Address_Factory.php'
            )
        );

        $this->serializer->deserialize($json, User::class);
    }
}
