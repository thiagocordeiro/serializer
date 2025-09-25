<?php

namespace Tcds\Io\Serializer\Metadata\Parser;

use ReflectionClass;

class ClassTemplates
{
    /**
     * @template T
     * @param class-string<T> $class
     * @return array<string, class-string<mixed>>
     */
    public static function of(string $class): array
    {
        $reflection = new ReflectionClass($class);

        $docblock = $reflection->getDocComment() ?? '';
        preg_match_all('/@template\s+(\w+)(?:\s+of\s+(\w+))?/', $docblock, $matches);
        dd($matches);

        return $matches[1] ?? [];
    }
}
