<?php

namespace Tcds\Io\Serializer\Unit\Metadata;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Generic\ArrayList;
use Tcds\Io\Generic\Map;
use Tcds\Io\Serializer\Exception\SerializerException;
use Tcds\Io\Serializer\Fixture\AccountType;
use Tcds\Io\Serializer\Fixture\GenericStubs;
use Tcds\Io\Serializer\Fixture\Pair;
use Tcds\Io\Serializer\Fixture\ReadOnly\Address;
use Tcds\Io\Serializer\Fixture\ReadOnly\BankAccount;
use Tcds\Io\Serializer\Fixture\ReadOnly\LatLng;
use Tcds\Io\Serializer\Fixture\ReadOnly\User;
use Tcds\Io\Serializer\Fixture\WithShape;
use Tcds\Io\Serializer\Metadata\XTypeNode;
use Tcds\Io\Serializer\SerializerTestCase;

class XTypeNodeTest extends SerializerTestCase
{
    protected function setUp(): void
    {
        XTypeNode::$nodes = [];
    }

    #[Test] public function single_scalar_types(): void
    {
        $this->assertTrue(XTypeNode::isResolvedType('string'));
        $this->assertTrue(XTypeNode::isResolvedType('int'));
        $this->assertTrue(XTypeNode::isResolvedType('float'));
        $this->assertTrue(XTypeNode::isResolvedType('string'));
        $this->assertTrue(XTypeNode::isResolvedType('bool'));
        $this->assertTrue(XTypeNode::isResolvedType('boolean'));
        $this->assertTrue(XTypeNode::isResolvedType('mixed'));
    }

    #[Test] public function union_scalar_types(): void
    {
        $this->assertTrue(XTypeNode::isResolvedType('string|int'));
        $this->assertTrue(XTypeNode::isResolvedType('string|float'));
        $this->assertTrue(XTypeNode::isResolvedType('bool|mixed'));
        $this->assertTrue(XTypeNode::isResolvedType('boolean|int'));
        $this->assertTrue(XTypeNode::isResolvedType('string&int'));
        $this->assertTrue(XTypeNode::isResolvedType('int&float'));
    }

    #[Test] public function invalid_union_types(): void
    {
        $exception = $this->expectThrows(fn() => XTypeNode::isResolvedType('TypeNode|string'));

        $this->assertEquals(
            new SerializerException('Non-scalar union types are not allowed'),
            $exception,
        );
    }

    #[Test] public function non_scalar_types(): void
    {
        $this->assertFalse(XTypeNode::isResolvedType(Exception::class));
        $this->assertFalse(XTypeNode::isResolvedType(XTypeNode::class));
        $this->assertFalse(XTypeNode::isResolvedType(ParamNode::class));
        $this->assertFalse(XTypeNode::isResolvedType(ArrayList::class));
        $this->assertFalse(XTypeNode::isResolvedType(Map::class));
    }

    #[Test] public function given_a_class_when_it_does_not_exist_then_throw_exception(): void
    {
        $exception = $this->expectThrows(fn() => XTypeNode::from('Foo\TypeNode'));

        $this->assertEquals(
            new SerializerException('Type <Foo\TypeNode> is not scalar nor an existing class'),
            $exception,
        );
    }

    #[Test] public function type_node_of_type_node(): void
    {
        $node = XTypeNode::from(XTypeNode::class);

        $this->assertEquals(
            new XTypeNode(
                type: XTypeNode::class,
                params: [
                    new ParamNode(
                        name: 'type',
                        type: new XTypeNode('string'),
                        generics: [],
                    ),
                    new ParamNode(
                        name: 'params',
                        type: new XTypeNode('list'),
                        generics: [
                            XTypeNode::lazy(XTypeNode::class),
                        ],
                    ),
                ],
            ),
            $node,
        );
    }

    #[Test] public function type_node_of_param_node(): void
    {
        $this->assertEquals(
            new XTypeNode(
                type: ParamNode::class,
                params: [
                    new ParamNode(
                        name: 'name',
                        type: XTypeNode::lazy('string'),
                        generics: [],
                    ),
                    new ParamNode(
                        name: 'type',
                        type: XTypeNode::lazy(XTypeNode::class),
                        generics: [],
                    ),
                    new ParamNode(
                        name: 'generics',
                        type: XTypeNode::lazy('string'),
                        generics: [XTypeNode::lazy(XTypeNode::class)],
                    ),
                ],
            ),
            XTypeNode::from(ParamNode::class),
        );
    }

    #[Test] public function multiple_generics(): void
    {
        $node = XTypeNode::from(GenericStubs::class);
        $this->loadLazyType($node, 10);

        $this->assertEquals(
            new XTypeNode(
                type: GenericStubs::class,
                params: [
                    new ParamNode(
                        name: 'addresses',
                        type: new XTypeNode(
                            type: ArrayList::class,
                            params: [
                                new ParamNode(
                                    name: 'items',
                                    type: new XTypeNode('list'),
                                    generics: [
                                        Address::node(),
                                    ],
                                ),
                            ],
                        ),
                        generics: [
                            Address::node(),
                        ],
                    ),
                    new ParamNode(
                        name: 'users',
                        type: new XTypeNode(
                            type: ArrayList::class,
                            params: [
                                new ParamNode(
                                    name: 'items',
                                    type: new XTypeNode('list'),
                                    generics: [
                                        User::node(),
                                    ],
                                ),
                            ],
                        ),
                        generics: [
                            User::node(),
                        ],
                    ),
                    new ParamNode(
                        name: 'positions',
                        type: new XTypeNode(
                            type: Map::class,
                            params: [
                                new ParamNode(
                                    name: 'entries',
                                    type: new XTypeNode('map'),
                                    generics: [
                                        new XTypeNode('string'),
                                        LatLng::node(),
                                    ],
                                ),
                            ],
                        ),
                        generics: [
                            new XTypeNode('string'),
                            LatLng::node(),
                        ],
                    ),
                    new ParamNode(
                        name: 'accounts',
                        type: new XTypeNode(
                            type: Pair::class,
                            params: [
                                new ParamNode(name: 'key', type: new XTypeNode('mixed')),
                                new ParamNode(name: 'value', type: new XTypeNode('object')),
                            ],
                        ),
                        generics: [
                            new XTypeNode(AccountType::class),
                            BankAccount::node(),
                        ],
                    ),
                ],
            ),
            $node,
        );
    }

    #[Test] public function array_shape(): void
    {
        $this->assertEquals(
            new XTypeNode(
                type: WithShape::class,
                params: [
                    new ParamNode(
                        name: 'data',
                        type: new XTypeNode('map'),
                        generics: [],
                    ),
                    new ParamNode(
                        name: 'data',
                        type: new XTypeNode('object'),
                        generics: [],
                    ),
                ],
            ),
            XTypeNode::from(WithShape::class),
        );
    }

    private function loadType(XTypeNode $node, bool $withParams, bool $withGenerics): void
    {
        initializeLazyObject($node);

        foreach ($node->params as $param) {
            if ($withParams) {
                initializeLazyObject($param->type);
            }

            if (!$withGenerics) {
                foreach ($param->generics as $generic) {
                    $this->loadType($generic, withParams: false, withGenerics: false);
                }
            }
        }
    }

    private function loadLazyType(XTypeNode $node, int $depth): void
    {
        initializeLazyObject($node);
        $depth--;

        foreach ($node->params as $param) {
            if ($depth >= 0) {
                $this->loadLazyType($param->type, $depth);
            }

            if ($depth >= 0) {
                foreach ($param->generics as $generic) {
                    $this->loadLazyType($generic, $depth);
                }
            }
        }
    }
}
