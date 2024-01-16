<?php

declare(strict_types=1);

namespace Test\Serializer\Unit\Exception;

use PHPUnit\Framework\TestCase;
use Serializer\Exception\MissingOrInvalidProperty;
use TypeError;
use ValueError;

class MissingOrInvalidPropertyTest extends TestCase
{
    /**
     * @dataProvider typeErrorMessagesDataProvider
     */
    public function testGivenErrorMessageThenCreateMissingRequiredParamMessage(string $error, string $expected): void
    {
        $exception = new MissingOrInvalidProperty(new TypeError($error), ['name', 'street', 'city']);

        $this->assertEquals($expected, $exception->getMessage());
    }

    /**
     * @dataProvider enumErrorMessagesDataProvider
     */
    public function testInvalidBackedEnumMessage(string $error): void
    {
        $expected = 'Value "other" is not valid for AccountType(checking, saving)';

        $exception = new MissingOrInvalidProperty(new ValueError($error), ['name', 'street', 'city']);

        $this->assertEquals($expected, $exception->getMessage());
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function typeErrorMessagesDataProvider(): array
    {
        $message = implode(', ', [
            'Argument %s passed to %s::__construct() must be of the type string, %s given',
            'called in /foo/bar/App_User_CreateUser_Factory.php on line 20',
        ]);

        return [
            '1st Argument required' => [
                sprintf($message, 1, 'App\User\CreateUser', 'null'),
                sprintf('Parameter "%s" is required', 'name'),
            ],
            '2nd Argument required' => [
                sprintf($message, 2, 'App\User\CreateUser', 'null'),
                sprintf('Parameter "%s" is required', 'street'),
            ],
            '3rd Argument required' => [
                sprintf($message, 3, 'App\User\CreateUser', 'null'),
                sprintf('Parameter "%s" is required', 'city'),
            ],

            '1st Argument invalid' => [
                sprintf($message, 1, 'App\User\CreateUser', 'string'),
                sprintf('Parameter "%s" is invalid', 'name'),
            ],
            '2nd Argument invalid' => [
                sprintf($message, 2, 'App\User\CreateUser', 'object'),
                sprintf('Parameter "%s" is invalid', 'street'),
            ],
            '3rd Argument invalid' => [
                sprintf($message, 3, 'App\User\CreateUser', 'array'),
                sprintf('Parameter "%s" is invalid', 'city'),
            ],
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function enumErrorMessagesDataProvider(): array
    {
        return [
            'without quotation' => [
                '"other" is not a valid backing value for enum Test\Serializer\Fixture\Dto\AccountType',
            ],
            'with quotation' => [
                '"other" is not a valid backing value for enum "Test\Serializer\Fixture\Dto\AccountType"',
            ],
        ];
    }
}
