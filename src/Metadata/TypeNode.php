<?php

namespace Tcds\Io\Serializer\Metadata;

use ReflectionClass;
use ReflectionParameter;
use Tcds\Io\Generic\ArrayList;
use Tcds\Io\Serializer\Exception\SerializerException;

class TypeNode
{
    /** @var array<string, TypeNode> */
    public static array $nodes = [];

    /**
     * @param list<ParamNode> $params
     */
    public function __construct(
        public readonly string $type,
        public readonly array $params = [],
    ) {
    }

    /**
     * @param ArrayList<TypeNode> $templates
     */
    public static function from(string $type, ArrayList $templates = new ArrayList([])): self
    {
        if (self::isResolvedType($type)) {
            return new TypeNode($type);
        }

        if (!class_exists($type)) {
            throw new SerializerException("Type <$type> is not scalar nor an existing class");
        }

        if (array_key_exists($type, self::$nodes)) {
            return self::$nodes[$type];
        }

        // initialize node to avoid inner process to try
        self::$nodes[$type] = new TypeNode(type: $type);

        $reflection = new ReflectionClass($type);
        $params = new ArrayList($reflection
            ->getConstructor()
            ->getParameters())
            ->map(fn(ReflectionParameter $param) => ParamNode::from($param, $templates))
            ->items();

        return new TypeNode(
            type: $type,
            params: $params,
        );
    }

    public static function isResolvedType(string $type): bool
    {
        $simpleNodeTypes = ['int', 'float', 'string', 'bool', 'boolean', 'object', 'mixed', 'list', 'map'];
        $types = explode('|', str_replace('&', '|', $type));

        $notScalar = array_filter($types, fn($t) => !in_array($t, $simpleNodeTypes, true));

        if (count($types) > 1 && !empty($notScalar)) {
            throw new SerializerException('Non-scalar union types are not allowed');
        }

        return empty($notScalar);
    }
}
