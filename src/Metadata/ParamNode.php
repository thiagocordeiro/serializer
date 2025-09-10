<?php

namespace Tcds\Io\Serializer\Metadata;

use ReflectionParameter;
use Tcds\Io\Generic\ArrayList;
use Traversable;

readonly class ParamNode
{
    /**
     * @param array<TypeNode> $generics
     */
    public function __construct(
        public string $name,
        public TypeNode $type,
        public array $generics,
    ) {
    }

    public function isTraversable(): bool
    {
        return $this->type->type === 'list'
            || $this->type->type === Traversable::class
            || class_implements($this->type->type, Traversable::class);
    }

    /**
     * @param ArrayList<TypeNode> $templates
     */
    public static function from(
        ReflectionParameter $param,
        ArrayList $templates = new ArrayList([]),
    ): self {
        $generic = Generic::from($param, $templates);
        $templates = array_map(fn(string $template) => TypeNode::from($template), $generic->templates);

        return new self(
            name: $param->name,
            type: TypeNode::from($generic->resolved, listOf(...$templates)),
            generics: $templates,
        );
    }
}
