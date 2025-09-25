<?php

namespace Tcds\Io\Serializer\Metadata;

use ReflectionClass;
use Tcds\Io\Serializer\Exception\SerializerException;
use Tcds\Io\Serializer\Metadata\Parser\Annotation;
use Tcds\Io\Serializer\Metadata\Parser\ClassAnnotation;
use Tcds\Io\Serializer\Metadata\Parser\ClassParams;
use Tcds\Io\Serializer\Metadata\Parser\ParamType;

/**
 * @phpstan-type ParamName string|int
 * @phpstan-type TemplateName string
 */
final class TypeNode
{
    /**
     * @param string $type
     * @param array<ParamName, ParamNode> $params
     */
    public function __construct(
        public string $type,
        public array $params = [],
    ) {
    }

    /**
     * @param list<string> $generics
     */
    public static function from(string $type, array $generics = []): self
    {
        if (empty($generics)) {
            [$type, $generics] = Annotation::extractGenerics($type);
        }

        return match (true) {
            ParamType::isScalar($type),
            ParamType::isEnum($type) => run(function () use ($type): TypeNode {
                return new TypeNode($type);
            }),
            ParamType::isList($type, $generics) => run(function () use ($type, $generics): TypeNode {
                return new self(
                    type: $type,
                    params: [
                        'value' => new ParamNode(type: TypeNode::from($generics[0])),
                    ],
                );
            }),
            ParamType::isArray($type) => run(function () use ($type, $generics): TypeNode {
                return new TypeNode(
                    type: 'array',
                    params: array_map(
                        callback: fn(string $generic) => ParamNode::from($generic),
                        array: [
                            'key' => $generics[0] ?? 'mixed',
                            'value' => $generics[1] ?? 'mixed',
                        ],
                    ),
                );
            }),
            ParamType::isClass($type) => run(function () use ($type, $generics): TypeNode {
                return self::fromClass($type, $generics);
            }),
            default => run(function () use ($type) {
                throw new SerializerException("Cannot handle type <$type>");
            }),
        };
    }

    /**
     * @param array<ParamName, string> $generics
     */
    private static function fromClass(string $type, array $generics = []): self
    {
        $reflection = new ReflectionClass($type);
        $params = ClassParams::of(reflection: $reflection);
        $templates = ClassAnnotation::templates(reflection: $reflection);

        foreach (array_keys($templates) as $position => $template) {
            $templates[$template] = $generics[$position] ?? throw new SerializerException("No generic defined for template <$template>");
        }

        return new self(
            type: $type,
            params: array_map(function ($paramType) use ($templates, $generics) {
                [$paramType, $paramGenerics] = Annotation::extractGenerics($paramType);

                foreach ($paramGenerics as $index => $paramGeneric) {
                    $paramGenerics[$index] = $templates[$paramGeneric] ?? $paramGeneric;
                }

                return ParamNode::from($paramType, $paramGenerics);
            }, $params),
        );
    }

    public function __toString(): string
    {
        $params = array_map(
            callback: fn(ParamNode $param) => in_array($param->type->type, ['array', 'object'])
                ? "$param->type"
                : $param->type->type,
            array: $this->params,
        );

        return empty($this->params)
            ? $this->type
            : sprintf('%s[%s]', $this->type, join(', ', $params));
    }
}
