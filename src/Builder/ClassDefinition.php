<?php

declare(strict_types=1);

namespace Serializer\Builder;

class ClassDefinition
{
    /** @var string */
    private $name;

    /** @var ClassProperty[] */
    private $properties;

    public function __construct(string $name, array $properties)
    {
        $this->name = $name;
        $this->properties = $properties;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
