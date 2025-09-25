<?php

namespace Tcds\Io\Serializer\Unit\Metadata;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Generic\ArrayList;
use Tcds\Io\Generic\Map;
use Tcds\Io\Serializer\Exception\SerializerException;
use Tcds\Io\Serializer\Fixture\GenericStubs;
use Tcds\Io\Serializer\Fixture\Pair;
use Tcds\Io\Serializer\Fixture\ReadOnly\Address;
use Tcds\Io\Serializer\Fixture\ReadOnly\LatLng;
use Tcds\Io\Serializer\Fixture\ReadOnly\Place;
use Tcds\Io\Serializer\Fixture\ReadOnly\User;
use Tcds\Io\Serializer\Fixture\WithShape;
use Tcds\Io\Serializer\Metadata\ParamNode;
use Tcds\Io\Serializer\Metadata\TypeNode;
use Tcds\Io\Serializer\SerializerTestCase;
use Traversable;

class TypeNodeTest extends SerializerTestCase
{
    #[Test] public function scalar_nodes(): void
    {
        $this->assertEquals(new TypeNode('string'), TypeNode::from('string'));
        $this->assertEquals(new TypeNode('int'), TypeNode::from('int'));
        $this->assertEquals(new TypeNode('float'), TypeNode::from('float'));
        $this->assertEquals(new TypeNode('bool'), TypeNode::from('bool'));
    }

    #[Test] public function when_generics_are_missing_for_templates_then_throw_exception(): void
    {
        $missingKeyGeneric = $this->expectThrows(fn() => TypeNode::from(Pair::class));
        $this->assertEquals(new SerializerException('No generic defined for template <K>'), $missingKeyGeneric);

        $missingKeyGeneric = $this->expectThrows(fn() => TypeNode::from(Pair::class, ['string']));
        $this->assertEquals(new SerializerException('No generic defined for template <V>'), $missingKeyGeneric);
    }

    #[Test] public function parse_type(): void
    {
        $type = Address::class;

        $node = TypeNode::from($type);

        $this->assertEquals(
            new TypeNode(
                type: Address::class,
                params: [
                    'street' => new ParamNode(new TypeNode('string')),
                    'number' => new ParamNode(new TypeNode('int')),
                    'main' => new ParamNode(new TypeNode('bool')),
                    'place' => new ParamNode(Place::node()),
                ],
            ),
            $node,
        );

        $this->assertEquals(
            sprintf('%s[%s, %s, %s, %s]', Address::class, 'string', 'int', 'bool', Place::class),
            "$node",
        );
    }

    #[Test] public function given_x_when_x_then(): void
    {
        $type = sprintf('%s<%s>', ArrayList::class, Address::class);

        $node = TypeNode::from($type);
    }

    #[Test] public function parse_with_lists(): void
    {
        $type = GenericStubs::class;

        $node = TypeNode::from($type);

        $this->assertEquals(
            new TypeNode(
                type: GenericStubs::class,
                params: [
                    'addresses' => new ParamNode(
                        new TypeNode(
                            type: ArrayList::class,
                            params: [
                                'items' => new ParamNode(
                                    type: new TypeNode(
                                        type: 'list',
                                        params: [
                                            'value' => new ParamNode(type: Address::node()),
                                        ],
                                    ),
                                ),
                            ],
                        ),
                    ),
                    'users' => new ParamNode(
                        new TypeNode(
                            type: Traversable::class,
                            params: [
                                'value' => new ParamNode(type: User::node()),
                            ],
                        ),
                    ),
                    'positions' => new ParamNode(
                        type: new TypeNode(
                            type: Map::class,
                            params: [
                                'entries' => new ParamNode(
                                    type: new TypeNode(
                                        type: 'array',
                                        params: [
                                            'key' => new ParamNode(type: new TypeNode('string')),
                                            'value' => new ParamNode(type: LatLng::node()),
                                        ],
                                    ),
                                ),
                            ],
                        ),
                    ),
                    // 'accounts' => new ParamNode(new TypeNode('')),
                ],
            ),
            $node,
        );
        // $this->assertEquals(sprintf('%s[%s]', ArrayList::class, 'string'), "$node");
    }

    #[Test] public function parse_with_inner_generics(): void
    {
        $type = ArrayList::class;

        $node = TypeNode::from($type, ['string']);

        $this->assertEquals(
            new TypeNode(
                type: ArrayList::class,
                params: [
                    'items' => new ParamNode(new TypeNode('string')),
                ],
            ),
            $node,
        );

        $this->assertEquals(sprintf('%s[%s]', ArrayList::class, 'string'), "$node");
    }

    #[Test] public function parse_type_with_generics(): void
    {
        $type = Pair::class;
        $generics = ['string', Address::class];

        $node = TypeNode::from($type, $generics);

        $this->assertEquals(
            new TypeNode(
                type: Pair::class,
                params: [
                    'key' => new ParamNode(new TypeNode(type: 'string')),
                    'value' => new ParamNode(Address::node()),
                ],
            ),
            $node,
        );

        $this->assertEquals(sprintf('%s[%s, %s]', Pair::class, 'string', Address::class), "$node");
    }

    #[Test] public function parse_type_with_shapes(): void
    {
        $type = WithShape::class;
        $generics = ['string', Address::class];

        $node = TypeNode::from($type, $generics);

        $this->assertEquals(
            new TypeNode(
                type: WithShape::class,
                params: [
                    'data' => new ParamNode(
                        type: new TypeNode(
                            type: 'array',
                            params: [
                                'user' => new ParamNode(User::node()),
                                'address' => new ParamNode(Address::node()),
                                'description' => new ParamNode(new TypeNode('string')),
                            ],
                        ),
                    ),
                    'payload' => new ParamNode(
                        type: new TypeNode(
                            type: 'object',
                            params: [
                                'user' => new ParamNode(User::node()),
                                'address' => new ParamNode(Address::node()),
                                'description' => new ParamNode(new TypeNode('string')),
                            ],
                        ),
                    ),
                ],
            ),
            $node,
        );

        // //'%s[%s[%s, %s, %s], %s[%s, %s, %s]]'
        // 'WithShape[array[User Address, string], object[User, Address, string]]',
        $this->assertEquals(
            sprintf(
                '%s[%s, %s]',
                WithShape::class,
                sprintf('%s[%s, %s, %s]', 'array', User::class, Address::class, 'string'),
                sprintf('%s[%s, %s, %s]', 'object', User::class, Address::class, 'string'),
            ),
            "$node",
        );
    }
}
