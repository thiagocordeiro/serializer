<?php

namespace Tcds\Io\Serializer\Fixture;

use Tcds\Io\Serializer\Metadata\ParamNode;
use Tcds\Io\Serializer\Metadata\TypeNode;

readonly class WithShape
{
    /**
     * @param array{
     *     type: TypeNode,
     *     param: ParamNode,
     *     description: string,
     * } $data
     * @param object{
     *     type: TypeNode,
     *     param: ParamNode,
     *     description: string,
     * } $payload
     */
    public function __construct(public array $data, public object $payload)
    {
    }
}
