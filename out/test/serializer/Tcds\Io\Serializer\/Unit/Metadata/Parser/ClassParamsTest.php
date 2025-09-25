<?php

namespace Tcds\Io\Serializer\Unit\Metadata\Parser;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Generic\ArrayList;
use Tcds\Io\Generic\Map;
use Tcds\Io\Serializer\Fixture\AccountType;
use Tcds\Io\Serializer\Fixture\GenericStubs;
use Tcds\Io\Serializer\Fixture\Pair;
use Tcds\Io\Serializer\Fixture\ReadOnly\Address;
use Tcds\Io\Serializer\Fixture\ReadOnly\BankAccount;
use Tcds\Io\Serializer\Fixture\ReadOnly\LatLng;
use Tcds\Io\Serializer\Fixture\ReadOnly\Place;
use Tcds\Io\Serializer\Fixture\ReadOnly\User;
use Tcds\Io\Serializer\Fixture\WithShape;
use Tcds\Io\Serializer\Metadata\Parser\ClassParams;
use Tcds\Io\Serializer\Metadata\TypeNode;
use Traversable;

class ClassParamsTest extends TestCase
{
    #[Test] public function params_of_pair(): void
    {
        $class = Pair::class;

        $params = ClassParams::of(class: $class);

        $this->assertEquals(
            expected: [
                'key' => new TypeNode('K'),
                'value' => new TypeNode('V'),
            ],
            actual: $params,
        );
    }

    #[Test] public function params_of_array_list(): void
    {
        $class = ArrayList::class;

        $params = ClassParams::of(class: $class);

        $this->assertEquals(
            expected: [
                'items' => new TypeNode('list', [new TypeNode('GenericItem')]),
            ],
            actual: $params,
        );
    }

    #[Test] public function params_of_address(): void
    {
        $class = Address::class;

        $params = ClassParams::of(class: $class);

        $this->assertEquals(
            expected: [
                'street' => new TypeNode('string'),
                'number' => new TypeNode('int'),
                'main' => new TypeNode('bool'),
                'place' => new TypeNode(Place::class),
            ],
            actual: $params,
        );
    }

    #[Test] public function params_of_generic_stubs(): void
    {
        $class = GenericStubs::class;

        $params = ClassParams::of(class: $class);

        $this->assertEquals(
            expected: [
                'addresses' => new TypeNode(ArrayList::class, [new TypeNode(Address::class)]),
                'users' => new TypeNode(Traversable::class, [new TypeNode(User::class)]),
                'positions' => new TypeNode(Map::class, [new TypeNode('string'), new TypeNode(LatLng::class)]),
                'accounts' => new TypeNode(Pair::class, [new TypeNode(AccountType::class), new TypeNode(BankAccount::class)]),
            ],
            actual: $params,
        );
    }

    #[Test] public function params_of_class_with_shaped(): void
    {
        $type = WithShape::class;

        $params = ClassParams::of(class: $type);

        $this->assertEquals(
            expected: [
                'data' => new TypeNode(
                    sprintf(
                        'array{ user: %s, address: %s, description: %s }',
                        User::class,
                        Address::class,
                        'string',
                    ),
                    params: [],
                ),
                'payload' => new TypeNode(
                    type: sprintf(
                        'object{ user: %s, address: %s, description: %s }',
                        User::class,
                        Address::class,
                        'string',
                    ),
                    params: [],
                ),
            ],
            actual: $params,
        );
    }
}
