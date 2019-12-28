<?php

declare(strict_types=1);

namespace Serializer\Builder;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Serializer\Exception\ArrayPropertyMustHaveAnArrayAnnotation;
use Serializer\Exception\ArrayPropertyMustHaveATypeAnnotation;
use Serializer\Exception\ClassMustHaveAConstructor;
use Serializer\Exception\PropertyMustHaveAType;

class ClassAnalyzer
{
    /** @var string */
    private $className;

    /** @var ReflectionClass */
    private $class;

    /** @var ReflectionMethod */
    private $constructor;


    public function __construct(string $className)
    {
        $class = new ReflectionClass($className);
        $constructor = $class->getConstructor();

        if (!$constructor instanceof ReflectionMethod) {
            throw new ClassMustHaveAConstructor($className);
        }

        $this->className = $className;
        $this->class = $class;
        $this->constructor = $constructor;
    }

    public function analyze(): ClassDefinition
    {
        $properties = array_map(function (ReflectionParameter $param) {
            return $this->createProperty($param);
        }, $this->constructor->getParameters());

        return new ClassDefinition($this->className, $properties);
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
            throw new PropertyMustHaveAType($param, $this->class);
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
        return $type === 'array';
    }

    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     */
    private function searchArrayType(ReflectionParameter $param): string
    {
        $type = $this->searchTypeOnDocComment($param);
        $namespace = $this->searchNamespace($type);

        return sprintf('%s%s[]', $namespace, $type);
    }

    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     */
    private function searchTypeOnDocComment(ReflectionParameter $param): string
    {
        $docComment = $this->constructor->getDocComment() ?: '';
        $pattern = sprintf('/\@param(.*)\$%s/', $param->getName());

        preg_match($pattern, $docComment, $matches);
        $type = trim($matches[1] ?? '');

        if ('' === $type) {
            throw new ArrayPropertyMustHaveATypeAnnotation($param, $this->class);
        }

        if (false === strpos($type, '[]')) {
            throw new ArrayPropertyMustHaveAnArrayAnnotation($param, $this->class, $type);
        }

        return str_replace('[]', '', $type);
    }

    private function searchNamespace(string $type): string
    {
        $parts = explode('\\', $type);
        $subNs = reset($parts);

        if ('' === $subNs) {
            return trim($type, '\\');
        }

        $file = file($this->class->getFileName() ?: '') ?: [];
        $lines = array_slice($file, 0, (int) $this->class->getStartLine());

        $pattern = sprintf('/use(.*)%s;/', $subNs);
        preg_match($pattern, implode(PHP_EOL, $lines), $matches);

        $match = trim($matches[0] ?? '');
        $namespace = trim($matches[1] ?? '');

        if ('' === $namespace && '' === $match && '' !== $subNs) {
            $namespace = sprintf('%s\\', $this->class->getNamespaceName());
        }

        return $namespace;
    }
}
