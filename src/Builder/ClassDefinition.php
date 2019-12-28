<?php

declare(strict_types=1);

namespace Serializer\Builder;

class ClassDefinition
{
    /** @var string */
    private $name;

    /** @var ClassProperty[] */
    private $properties;

    /**
     * @param ClassProperty[] $properties
     */
    public function __construct(string $name, array $properties)
    {
        $this->name = $name;
        $this->properties = $properties;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ClassProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
