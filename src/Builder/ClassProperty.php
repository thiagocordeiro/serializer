<?php

declare(strict_types=1);

namespace Serializer\Builder;

class ClassProperty
{
    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $defaultValue;

    public function __construct(string $name, string $type, string $defaultValue)
    {
        $this->name = $name;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        if ($this->isArray()) {
            return str_replace('[]', '', $this->type);
        }

        return $this->type;
    }

    public function getDefaultValue(): string
    {
        if ($this->isString()) {
            return sprintf("'%s'", $this->defaultValue);
        }

        return $this->defaultValue;
    }

    public function isScalar(): bool
    {
        return in_array($this->type, ['int', 'float', 'string', 'bool']);
    }

    public function isArray(): bool
    {
        return strpos($this->type, '[]') !== false;
    }

    public function isString(): bool
    {
        return $this->type === 'string';
    }
}
