<?php

namespace Tcds\Io\Serializer\Metadata;

use ReflectionParameter;
use Tcds\Io\Generic\ArrayList;
use Traversable;

readonly class Generic
{
    /**
     * @param list<string> $templates
     */
    public function __construct(
        public string $resolved,
        public ?string $annotated,
        public array $templates = [],
    ) {
    }

    /**
     * @param ArrayList<TypeNode> $templates
     */
    public static function from(
        ReflectionParameter $param,
        ArrayList $templates = new ArrayList([]),
    ): Generic {
        $native = $param->getType()?->getName();
        $annotated = self::annotationOf($param, $templates);
        $generics = self::genericsOf($param, $annotated, $templates);

        if ($native === Traversable::class) {
            $native = ArrayList::class;
            $generics = [$generics[1] ?? $generics[0]];
        }

        return new Generic(
            resolved: match (true) {
                self::isListArray($native, $generics) => 'list',
                self::isMapArray($native, $generics) => 'map',
                self::isGenericTemplate($param, $native, $annotated) => $annotated,
                self::isAnnotationShape($annotated) => $annotated,
                default => $native,
            },
            annotated: $annotated,
            templates: $generics,
        );
    }

    /**
     * @param ArrayList<TypeNode> $templates
     */
    private static function annotationOf(ReflectionParameter $param, ArrayList $templates): ?string
    {
        $type = self::extractAnnotation(
            docblock: $param->getDeclaringFunction()->getDocComment(),
            pattern: sprintf('/@param\s+([^\n]+?)\s+\$%s/s', $param->getName()),
        );

        if (!$type) {
            return null;
        }

        if (self::isAnnotationShape($type)) {
            $type = self::shapeFqnOf($param, $type);
        }

        $runtimeTime = self::toRuntimeType($param, $type);

        return self::templateToGeneric($param, $runtimeTime, $templates);
    }

    /**
     * @param ArrayList<TypeNode> $templates
     * @return list<string>
     */
    private static function genericsOf(ReflectionParameter $param, ?string $annotated, ArrayList $templates): array
    {
        $annotated ??= '';

        if (str_contains($annotated, '[]')) {
            $annotated = sprintf('array<%s>', str_replace('[]', '', $annotated));
        }

        $generics = self::extractAnnotation(docblock: $annotated, pattern: '/<(.*?)>/');
        $generics = explode(',', str_replace(' ', '', $generics));
        $generics = array_filter($generics);

        return array_map(
            fn(string $t) => self::fqnOf($param, self::templateToGeneric($param, $t, $templates)),
            $generics,
        );
    }

    /**
     * @param ArrayList<TypeNode> $templates
     */
    private static function templateToGeneric(ReflectionParameter $param, string $generic, ArrayList $templates): string
    {
        $docblock = $param->getDeclaringClass()->getDocComment() ?? '';
        preg_match_all('/@template\s+(\w*)/', $docblock, $matches);
        $docTypes = array_flip($matches[1] ?? []);

        foreach ($docTypes as $type => $index) {
            $docTypes[$type] = $templates->get($index)->type ?? $type;
        }

        return $docTypes[$generic] ?? $generic;
    }

    private static function fqnOf(ReflectionParameter $param, string $type): string
    {
        if (TypeNode::isResolvedType($type)) {
            return $type;
        }

        if (class_exists($type)) {
            return $type;
        }

        $source = file_get_contents($param->getDeclaringClass()->getFileName());
        $fqn = $param->getDeclaringClass()->getNamespaceName() . '\\' . $type;
        $pattern = sprintf("/use\s(.*?)%s;/", $type);

        if (preg_match($pattern, $source, $matches)) {
            $fqn = $matches[1] . $type;
        }

        return $fqn;
    }

    private static function shapeFqnOf(ReflectionParameter $param, string $shape): string
    {
        preg_match_all('/(\w+)\s*:\s*([^,\s}]+)/', $shape, $pairs, PREG_SET_ORDER);

        $result = [];

        foreach ($pairs as $pair) {
            $result[] = sprintf('%s: %s', $pair[1], self::fqnOf($param, $pair[2]));
        }

        $prefix = match (true) {
            str_starts_with('object{', $shape) => 'object',
            default => 'array',
        };

        return sprintf('%s{%s}', $prefix, join(', ', $result));
    }

    /**
     * @param list<string> $generics
     */
    private static function isListArray(string $type, array $generics): bool
    {
        return $type === 'array' && count($generics) === 1;
    }

    /**
     * @param list<string> $generics
     */
    private static function isMapArray(string $type, array $generics): bool
    {
        return $type === 'array' && count($generics) > 1;
    }

    private static function isGenericTemplate(ReflectionParameter $param, string $native, ?string $annotated): bool
    {
        if (!$annotated || !in_array($native, ['object', 'mixed'])) {
            return false;
        }

        $docblock = $param->getDeclaringClass()?->getDocComment() ?: '';
        preg_match_all('/@template\s+([A-Za-z_][A-Za-z0-9_]*)/', $docblock, $matches);

        return in_array($annotated, $matches[1] ?? []);
    }

    private static function toRuntimeType(ReflectionParameter $param, string $type): string
    {
        $docblock = $param->getDeclaringClass()?->getDocComment();

        $runtimeType = self::extractAnnotation(
            docblock: $docblock,
            pattern: sprintf("/@phpstan-type\s+%s(.*)\n/", $type),
        );

        return $runtimeType ?: $type;
    }

    private static function extractAnnotation(null|string|false $docblock, string $pattern): ?string
    {
        $docblock = trim($docblock ?: '');
        $docblock = preg_replace('/\/\*\*|\*\/|\*/', '', $docblock);
        $docblock = preg_replace('/\s*\n\s*/', ' ', $docblock);
        $docblock = join(PHP_EOL, array_map(fn(string $line) => "@$line", explode('@', $docblock)));
        preg_match($pattern, $docblock, $matches);

        return trim($matches[1] ?? '') ?: null;
    }

    private static function isAnnotationShape(?string $type): bool
    {
        return str_starts_with($type ?? '', 'array{') || str_starts_with($type ?? '', 'object{');
    }
}
