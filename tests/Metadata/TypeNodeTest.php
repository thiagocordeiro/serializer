<?php

namespace Tcds\Io\Serializer\Metadata;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Generic\ArrayList;
use Tcds\Io\Generic\Map;
use Tcds\Io\Serializer\Exception\SerializerException;
use Tcds\Io\Serializer\Fixture\WithShape;
use Tcds\Io\Serializer\Fixture\GenericStubs;
use Tcds\Io\Serializer\Fixture\Pair;
use Tcds\Io\Serializer\SerializerTestCase;

class TypeNodeTest extends SerializerTestCase
{
    protected function setUp(): void
    {
        TypeNode::$nodes = [];
    }

    #[Test] public function single_scalar_types(): void
    {
        $this->assertTrue(TypeNode::isResolvedType('string'));
        $this->assertTrue(TypeNode::isResolvedType('int'));
        $this->assertTrue(TypeNode::isResolvedType('float'));
        $this->assertTrue(TypeNode::isResolvedType('string'));
        $this->assertTrue(TypeNode::isResolvedType('bool'));
        $this->assertTrue(TypeNode::isResolvedType('boolean'));
        $this->assertTrue(TypeNode::isResolvedType('mixed'));
    }

    #[Test] public function union_scalar_types(): void
    {
        $this->assertTrue(TypeNode::isResolvedType('string|int'));
        $this->assertTrue(TypeNode::isResolvedType('string|float'));
        $this->assertTrue(TypeNode::isResolvedType('bool|mixed'));
        $this->assertTrue(TypeNode::isResolvedType('boolean|int'));
        $this->assertTrue(TypeNode::isResolvedType('string&int'));
        $this->assertTrue(TypeNode::isResolvedType('int&float'));
    }

    #[Test] public function invalid_union_types(): void
    {
        $exception = $this->expectThrows(fn() => TypeNode::isResolvedType('TypeNode|string'));

        $this->assertEquals(
            new SerializerException('Non-scalar union types are not allowed'),
            $exception,
        );
    }

    #[Test] public function non_scalar_types(): void
    {
        $this->assertFalse(TypeNode::isResolvedType(Exception::class));
        $this->assertFalse(TypeNode::isResolvedType(TypeNode::class));
        $this->assertFalse(TypeNode::isResolvedType(ParamNode::class));
        $this->assertFalse(TypeNode::isResolvedType(ArrayList::class));
        $this->assertFalse(TypeNode::isResolvedType(Map::class));
    }

    #[Test] public function given_a_class_when_it_does_not_exist_then_throw_exception(): void
    {
        $exception = $this->expectThrows(fn() => TypeNode::from('Foo\TypeNode'));

        $this->assertEquals(
            new SerializerException('Type <Foo\TypeNode> is not scalar nor an existing class'),
            $exception,
        );
    }

    #[Test] public function type_node_of_type_node(): void
    {
        $this->assertEquals(
            new TypeNode(
                type: TypeNode::class,
                params: [
                    new ParamNode(name: 'type', type: new TypeNode('string'), generics: []),
                    new ParamNode(name: 'params', type: new TypeNode('list'), generics: [
                        new TypeNode(
                            type: ParamNode::class,
                            params: [
                                new ParamNode(name: 'name', type: new TypeNode('string'), generics: []),
                                new ParamNode(name: 'type', type: new TypeNode(TypeNode::class), generics: []),
                                new ParamNode(name: 'generics', type: new TypeNode('list'), generics: [
                                    new TypeNode(TypeNode::class),
                                ]),
                            ],
                        ),
                    ]),
                ],
            ),
            TypeNode::from(TypeNode::class),
        );
    }

    #[Test] public function type_node_of_param_node(): void
    {
        $this->assertEquals(
            new TypeNode(
                type: ParamNode::class,
                params: [
                    new ParamNode(
                        name: 'name',
                        type: new TypeNode('string'),
                        generics: [],
                    ),
                    new ParamNode(
                        name: 'type',
                        type: new TypeNode(TypeNode::class,
                            params: [
                                new ParamNode(name: 'type', type: new TypeNode('string'), generics: []),
                                new ParamNode(name: 'params', type: new TypeNode('list'), generics: [
                                    new TypeNode(type: ParamNode::class, params: []),
                                ]),
                            ],
                        ),
                        generics: [],
                    ),
                    new ParamNode(
                        name: 'generics',
                        type: new TypeNode('list'),
                        generics: [new TypeNode(TypeNode::class)],
                    ),
                ],
            ),
            TypeNode::from(ParamNode::class),
        );
    }

    #[Test] public function multiple_generics(): void
    {
        $this->assertEquals(
            new TypeNode(
                type: GenericStubs::class,
                params: [
                    new ParamNode(
                        name: 'arrayList',
                        type: new TypeNode(
                            type: ArrayList::class,
                            params: [
                                new ParamNode(
                                    name: 'items',
                                    type: new TypeNode('list'),
                                    generics: [
                                        new TypeNode(
                                            type: TypeNode::class,
                                            params: [],
                                        ),
                                    ],
                                ),
                            ],
                        ),
                        generics: [
                            new TypeNode(TypeNode::class, params: [
                                new ParamNode(
                                    name: 'type',
                                    type: new TypeNode('string'),
                                    generics: [],
                                ),
                                new ParamNode(
                                    name: 'params',
                                    type: new TypeNode('list'),
                                    generics: [
                                        new TypeNode(
                                            type: ParamNode::class,
                                            params: [
                                                new ParamNode(name: 'name', type: new TypeNode('string'), generics: []),
                                                new ParamNode(name: 'type', type: new TypeNode(TypeNode::class), generics: []),
                                                new ParamNode(name: 'generics', type: new TypeNode('list'), generics: [
                                                    new TypeNode(TypeNode::class),
                                                ]),
                                            ],
                                        ),
                                    ],
                                ),
                            ]),
                        ],
                    ),
                    new ParamNode(
                        name: 'traversable',
                        type: new TypeNode(ArrayList::class),
                        generics: [
                            new TypeNode(ParamNode::class),
                        ],
                    ),
                    new ParamNode(
                        name: 'map',
                        type: new TypeNode(
                            Map::class,
                            params: [
                                new ParamNode(
                                    name: 'entries',
                                    type: new TypeNode('map'),
                                    generics: [
                                        new TypeNode('string'),
                                        new TypeNode(TypeNode::class),
                                    ],
                                ),
                            ],
                        ),
                        generics: [
                            new TypeNode('string'),
                            new TypeNode(TypeNode::class),
                        ],
                    ),
                    new ParamNode(
                        name: 'pair',
                        type: new TypeNode(
                            type: Pair::class,
                            params: [
                                new ParamNode(name: 'key', type: new TypeNode('mixed'), generics: []),
                                new ParamNode(name: 'value', type: new TypeNode('object'), generics: []),
                            ],
                        ),
                        generics: [
                            new TypeNode(TypeNode::class),
                            new TypeNode(ParamNode::class),
                        ],
                    ),
                ],
            ),
            TypeNode::from(GenericStubs::class),
        );
    }

    #[Test] public function array_shape(): void
    {
        $this->assertEquals(
            new TypeNode(
                type: WithShape::class,
                params: [
                    new ParamNode(
                        name: 'data',
                        type: new TypeNode('map'),
                        generics: [],
                    ),
                    new ParamNode(
                        name: 'data',
                        type: new TypeNode('object'),
                        generics: [],
                    ),
                ],
            ),
            TypeNode::from(WithShape::class),
        );
    }
}
