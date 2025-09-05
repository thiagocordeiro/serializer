<?php

namespace Serializer\Param;

use ReflectionParameter;
use Serializer\Exception\ArrayPropertyMustHaveAnArrayAnnotation;
use Serializer\Exception\ArrayPropertyMustHaveATypeAnnotation;

class ArrayType
{
    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     */
    public static function from(ReflectionParameter $param): string
    {
        $docblock = $param->getDeclaringFunction()->getDocComment() ?: '';
        $pattern = sprintf('/\@param(.*)\$%s/', $param->getName());

        preg_match($pattern, $docblock, $matches);
        $type = trim($matches[1] ?? '');

        if ('' === $type) {
            throw new ArrayPropertyMustHaveATypeAnnotation($param, $param->getDeclaringClass());
        }

        $arrayType = self::getTypeFromArray($type);

        if (self::isScalar($arrayType)) {
            return $arrayType;
        }

        if ($arrayType === null) {
            throw new ArrayPropertyMustHaveAnArrayAnnotation($param, $param->getDeclaringClass(), $type);
        }

        return self::withFqn($param, $arrayType);
    }

    private static function getTypeFromArray(string $type): ?string
    {
        if (str_contains($type, '[]')) {
            return str_replace('[]', '', $type);
        }

        if (!preg_match('/array<(.*?)>|list<(.*?)>/', $type, $match)) {
            return null;
        }

        $matched = $match[1] ?: $match[2] ?? '';
        $types = explode(',', str_replace(' ', '', $matched));

        return $types[1] ?? $types[0] ?? null;
    }

    private static function withFqn(ReflectionParameter $param, string $type): string
    {
        $source = file_get_contents($param->getDeclaringClass()->getFileName());
        $fqn = $param->getDeclaringClass()->getNamespaceName() . '\\' . $type;
        $pattern = sprintf("/use\s(.*?)%s;/", $type);

        if (preg_match($pattern, $source, $matches)) {
            $fqn = $matches[1] . $type;
        }

        return $fqn;
    }

    public static function isScalar(string $type): bool
    {
        return in_array($type, ['int', 'float', 'string', 'bool'], true);
    }
}
