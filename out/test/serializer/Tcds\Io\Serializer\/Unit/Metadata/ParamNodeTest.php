<?php

namespace Tcds\Io\Serializer\Unit\Metadata;

use ArrayObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Generic\ArrayList;
use Tcds\Io\Generic\Map;
use Tcds\Io\Serializer\Fixture\GenericStubs;
use Tcds\Io\Serializer\Fixture\Pair;
use Tcds\Io\Serializer\Metadata\XParamNode;
use Tcds\Io\Serializer\Metadata\XTypeNode;
use Tcds\Io\Serializer\SerializerTestCase;
use Traversable;

class ParamNodeTest extends SerializerTestCase
{
//    /**
//     * @param array<string> $generics
//     */
//    #[DataProvider('typeNodeParamsDataset')]
//    #[Test] public function multiple_traversable_types(ParamNode $param, string $type, $isTraversable, array $generics): void
//    {
//        $this->assertEquals(
//            [$type, $isTraversable, $generics],
//            [$param->type, $param->isTraversable(), $param->generics],
//        );
//    }

    #[Test] public function type_node_params(): void
    {
        $node = XTypeNode::from(XTypeNode::class);

        $this->assertFalse($node->params[0]->isTraversable());
        $this->assertTrue($node->params[1]->isTraversable());
    }

    public static function typeNodeParamsDataset(): array
    {
        $stubs = XTypeNode::from(GenericStubs::class);
        $node = XTypeNode::from(XTypeNode::class);

        return [
            'ListStubs::arrayList' => [
                'param' => $stubs->params[0],
                'type' => ArrayList::class,
                'isTraversable' => true,
                'generics' => [XTypeNode::class],
            ],
            'ListStubs::traversable' => [
                'param' => $stubs->params[1],
                'type' => Traversable::class,
                'isTraversable' => true,
                'generics' => [XParamNode::class],
            ],
            'ListStubs::arrayObject' => [
                'param' => $stubs->params[2],
                'type' => ArrayObject::class,
                'isTraversable' => true,
                'generics' => ['string'],
            ],
            'ListStubs::map' => [
                'param' => $stubs->params[3],
                'type' => Map::class,
                'isTraversable' => true,
                'generics' => ['string', XTypeNode::class],
            ],
            'ListStubs::pair' => [
                'param' => $stubs->params[4],
                'type' => Pair::class,
                'isTraversable' => false,
                'generics' => [XTypeNode::class, XParamNode::class],
            ],
            'TypeNode::type' => [
                'param' => $node->params[0],
                'type' => 'string',
                'isTraversable' => false,
                'generics' => [],
            ],
            'TypeNode::params' => [
                'param' => $node->params[1],
                'type' => 'list',
                'isTraversable' => true,
                'generics' => [
                    XParamNode::class,
                ],
            ],
        ];
    }
}
