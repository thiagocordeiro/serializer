<?php

declare(strict_types=1);

namespace Serializer\Builder;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Serializer\Exception\ArrayPropertyMustHaveAnArrayAnnotation;
use Serializer\Exception\ArrayPropertyMustHaveATypeAnnotation;
use Serializer\Exception\PropertyMustHaveAType;

class ClassAnalyzer
{
    private const TYPE_ARRAY = 'array';

    /**
     * @throws
     */
    public function analyze(string $class): ClassDefinition
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        $properties = array_map(function (ReflectionParameter $param) {
            return $this->createProperty($param);
        }, $constructor->getParameters());

        return new ClassDefinition($class, $properties);
    }

    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     * @throws PropertyMustHaveAType
     * @throws ReflectionException
     */
    private function createProperty(ReflectionParameter $param): ClassProperty
    {
        $name = $param->getName();
        $type = $this->searchParamType($param);
        $defaultValue = ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : 'null') ?: 'null';

        return new ClassProperty($name, $type, (string) $defaultValue);
    }

    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     * @throws PropertyMustHaveAType
     */
    private function searchParamType(ReflectionParameter $param): string
    {
        $type = (string) $param->getType();

        if ('' === $type) {
            throw new PropertyMustHaveAType($param);
        }

        if (true === $this->isScalar($type)) {
            return $type;
        }

        if (true === $this->isArray($type)) {
            return $this->searchArrayType($param);
        }

        return $type;
    }

    private function isScalar(string $type): bool
    {
        return in_array($type, ['int', 'float', 'string', 'bool']);
    }

    private function isArray(string $type): bool
    {
        return $type === self::TYPE_ARRAY;
    }

    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     */
    private function searchArrayType(ReflectionParameter $param): string
    {
        $type = $this->searchTypeOnDocComment($param);
        $namespace = $this->searchNamespace($param->getDeclaringClass(), $type);

        return sprintf('%s%s[]', $namespace, $type);
    }

    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     */
    private function searchTypeOnDocComment(ReflectionParameter $param): string
    {
        $pattern = sprintf('/\@param(.*)\$%s/', $param->getName());

        $class = $param->getDeclaringClass();
        $constructor = $class->getConstructor();

        preg_match($pattern, $constructor->getDocComment(), $matches);
        $type = trim($matches[1] ?? '');

        if ('' === $type) {
            throw new ArrayPropertyMustHaveATypeAnnotation($param);
        }

        if (false === strpos($type, '[]')) {
            throw new ArrayPropertyMustHaveAnArrayAnnotation($param, $type);
        }

        return str_replace('[]', '', $type);
    }

    private function searchNamespace(ReflectionClass $class, string $type): string
    {
        $parts = explode('\\', $type);
        $subNs = reset($parts);

        if ('' === $subNs) {
            return trim($type, '\\');
        }

        $lines = array_slice(file($class->getFileName()), 0, $class->getStartLine());

        $pattern = sprintf('/use(.*)%s;/', $subNs);
        preg_match($pattern, implode(PHP_EOL, $lines), $matches);

        $match = trim($matches[0] ?? '');
        $namespace = trim($matches[1] ?? '');

        if ('' === $namespace && '' === $match && '' !== $subNs) {
            $namespace = sprintf('%s\\', $class->getNamespaceName());
        }

        return $namespace;
    }
}
