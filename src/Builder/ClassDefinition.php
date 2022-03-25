<?php

declare(strict_types=1);

namespace Serializer\Builder;

class ClassDefinition
{
    private string $name;
    private bool $isValueObject;
    private bool $isCollection;
    private bool $isEnum;

    /** @var ClassProperty[] */
    private array $properties;

    /**
     * @param ClassProperty[] $properties
     */
    public function __construct(string $name, bool $isCollection, bool $isValueObject, bool $isEnum, array $properties)
    {
        $this->name = $name;
        $this->isCollection = $isCollection;
        $this->isValueObject = $isValueObject;
        $this->isEnum = $isEnum;
        $this->properties = $properties;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isValueObject(): bool
    {
        return $this->isValueObject;
    }

    public function isCollection(): bool
    {
        return $this->isCollection;
    }

    public function isEnum(): bool
    {
        return $this->isEnum;
    }

    /**
     * @return ClassProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
