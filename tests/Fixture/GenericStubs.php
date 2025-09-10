<?php

declare(strict_types=1);

namespace Tcds\Io\Serializer\Fixture;

use Tcds\Io\Generic\ArrayList;
use Tcds\Io\Generic\Map;
use Tcds\Io\Serializer\Metadata\ParamNode;
use Tcds\Io\Serializer\Metadata\TypeNode;
use Traversable;

readonly class GenericStubs
{
    /**
     * @param ArrayList<TypeNode> $arrayList
     * @param Traversable<ParamNode> $traversable
     * @param Map<string, TypeNode> $map
     * @param Pair<TypeNode, ParamNode> $pair
     */
    public function __construct(
        ArrayList $arrayList,
        Traversable $traversable,
        Map $map,
        Pair $pair,
    ) {
    }
}
