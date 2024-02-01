<?php

declare(strict_types=1);

namespace Serializer\Builder;

use IteratorAggregate;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Serializer\Exception\ArrayPropertyMustHaveAnArrayAnnotation;
use Serializer\Exception\ArrayPropertyMustHaveATypeAnnotation;
use Serializer\Exception\ClassMustHaveAConstructor;
use Serializer\Exception\IterableMustHaveOneParameterOnly;
use Serializer\Exception\PropertyHasNoGetter;
use Serializer\Exception\PropertyMustHaveAType;
use Serializer\Exception\ValueObjectMustHaveScalarValue;
use Throwable;

class ClassAnalyzer
{
    private string $className;

    private ReflectionClass $class;

    private ?ReflectionMethod $constructor;

    /**
     * @throws ClassMustHaveAConstructor
     * @throws ReflectionException
     */
    private function __construct(string $name)
    {
        $class = new ReflectionClass($name);

        $this->className = $name;
        $this->class = $class;
        $this->constructor = $class->getConstructor();
    }

    /**
     * @throws ClassMustHaveAConstructor
     * @throws Throwable
     */
    public static function analyze(string $class): ClassDefinition
    {
        $self = new self($class);
        $isCollection = $self->class->implementsInterface(IteratorAggregate::class);
        $isValueObject = $self->class->hasMethod('__toString') && $self->constructor?->getNumberOfParameters() === 1;

        $properties = [];
        $params = $self->constructor?->getParameters() ?? [];

        foreach ($params as $param) {
            $properties[] = $self->createProperty($param, $isCollection, $isValueObject);
        }

        return new ClassDefinition(
            name: $self->className,
            isCollection: $isCollection,
            isValueObject: $isValueObject,
            isEnum: false,
            properties: $properties,
        );
    }

    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     * @throws PropertyMustHaveAType
     * @throws PropertyHasNoGetter
     * @throws IterableMustHaveOneParameterOnly
     * @throws ValueObjectMustHaveScalarValue
     */
    private function createProperty(ReflectionParameter $param, bool $isCollection, bool $isValueObject): ClassProperty
    {
        $name = $param->getName();
        $type = $this->searchParamType($param);
        $defaultValue = ($param->isDefaultValueAvailable() ? (string) $param->getDefaultValue() : null) ?: null;
        $getter = $isValueObject ? '__toString' : $this->searchParamGetter($param, $type, $isCollection);
        $isArgument = $param->isVariadic();

        $property = new ClassProperty(
            class: $this->className,
            name: $name,
            type: $type,
            defaultValue: $defaultValue,
            isArgument: $isArgument,
            getter: $getter,
        );

        if ($isValueObject && false === $property->isScalar()) {
            throw new ValueObjectMustHaveScalarValue($property, $this->class);
        }

        return $property;
    }

    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     * @throws PropertyMustHaveAType
     */
    private function searchParamType(ReflectionParameter $param): string
    {
        $refType = $param->getType();
        assert($refType instanceof ReflectionNamedType);
        $type = $refType->getName();

        if ('' === $type) {
            throw new PropertyMustHaveAType($param, $this->class);
        }

        if ($type === 'array') {
            return $this->searchArrayType($param);
        }

        return $type;
    }

    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     */
    private function searchArrayType(ReflectionParameter $param): string
    {
        $type = $this->searchTypeOnDocComment($param);

        if ($this->isScalar($type)) {
            return sprintf('%s[]', $type);
        }

        $namespace = $this->searchNamespace($type);

        return sprintf('%s%s[]', $namespace, $type);
    }

    /**
     * @throws ArrayPropertyMustHaveATypeAnnotation
     * @throws ArrayPropertyMustHaveAnArrayAnnotation
     */
    private function searchTypeOnDocComment(ReflectionParameter $param): string
    {
        $docComment = $this->constructor?->getDocComment() ?: '';
        $pattern = sprintf('/\@param(.*)\$%s/', $param->getName());

        preg_match($pattern, $docComment, $matches);
        $type = trim($matches[1] ?? '');

        if ('' === $type) {
            throw new ArrayPropertyMustHaveATypeAnnotation($param, $this->class);
        }

        if (!str_contains($type, '[]')) {
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

    /**
     * @throws PropertyHasNoGetter
     * @throws IterableMustHaveOneParameterOnly
     * @throws ReflectionException
     */
    private function searchParamGetter(ReflectionParameter $param, string $type, bool $isCollection): string
    {
        if ($isCollection) {
            return $this->getIteratorGetter();
        }

        if ($this->isPublicProperty($param)) {
            return $param->name;
        }

        if ($type === 'bool') {
            return $this->searchGetterForBoolean($param);
        }

        $getter = sprintf('get%s', ucfirst($param->getName()));

        if ($this->class->hasMethod($getter)) {
            return "$getter()";
        }

        if ($this->class->hasMethod($param->getName())) {
            return "{$param->getName()}()";
        }

        return '';
    }

    /**
     * @throws ReflectionException
     */
    private function isPublicProperty(ReflectionParameter $param): bool
    {
        $name = $param->name;

        if (false === $this->class->hasProperty($name)) {
            return false;
        }

        $prop = $this->class->getProperty($name);

        return $prop->isPublic();
    }

    /**
     * @throws PropertyHasNoGetter
     */
    private function searchGetterForBoolean(ReflectionParameter $param): string
    {
        $name = $param->getName();

        $isPrefix = sprintf('is%s', ucfirst($name));

        if (true === $this->class->hasMethod($isPrefix)) {
            return "$isPrefix()";
        }

        $hasPrefix = sprintf('has%s', ucfirst($name));

        if (true === $this->class->hasMethod($hasPrefix)) {
            return "$hasPrefix()";
        }

        $wasPrefix = sprintf('was%s', ucfirst($name));

        if (true === $this->class->hasMethod($wasPrefix)) {
            return "$wasPrefix()";
        }

        throw new PropertyHasNoGetter($this->className, $name, true);
    }

    /**
     * @throws IterableMustHaveOneParameterOnly
     */
    private function getIteratorGetter(): string
    {
        $params = $this->constructor?->getParameters() ?? [];

        if (count($params) !== 1) {
            throw new IterableMustHaveOneParameterOnly($this->class->getName(), count($params));
        }

        return 'getIterator()';
    }

    public function isScalar(string $type): bool
    {
        return in_array($type, ['int', 'float', 'string', 'bool'], true);
    }
}
