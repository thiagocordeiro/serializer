<?php

declare(strict_types=1);

namespace Serializer\Builder;

class ClassDefinition
{
    /** @var string */
    private $name;

    /** @var ClassProperty[] */
    private $properties;

    /** @var bool */
    private $isValueObject;

    /** @var bool */
    private $isCollection;

    public function __construct(string $name, bool $isCollection, bool $isValueObject, ClassProperty ...$properties)
    {
        $this->name = $name;
        $this->isCollection = $isCollection;
        $this->isValueObject = $isValueObject;
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

    /**
     * @return ClassProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
