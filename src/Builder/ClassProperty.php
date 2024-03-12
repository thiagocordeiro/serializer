<?php

declare(strict_types=1);

namespace Serializer\Builder;

use BackedEnum;
use Serializer\Exception\PropertyHasNoGetter;

class ClassProperty
{
    private string $class;
    private string $name;
    private string $type;
    private mixed $defaultValue;
    private string $getter;
    private bool $isArgument;

    public function __construct(
        string $class,
        string $name,
        string $type,
        mixed $defaultValue,
        bool $isArgument,
        string $getter,
    ) {
        $this->class = $class;
        $this->name = $name;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
        $this->isArgument = $isArgument;
        $this->getter = $getter;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        if ($this->isScalar() && $this->isArray()) {
            return 'array';
        }

        if ($this->isArray()) {
            return str_replace('[]', '', $this->type);
        }

        return $this->type;
    }

    public function getDefaultValue(): string
    {
        if (null === $this->defaultValue) {
            return 'null';
        }

        if ([] === $this->defaultValue) {
            return '[]';
        }

        if ($this->isString() && is_string($this->defaultValue)) {
            return sprintf("'%s'", $this->defaultValue);
        }

        if ($this->defaultValue instanceof BackedEnum) {
            return sprintf("'%s'", $this->defaultValue->value);
        }

        return (string) $this->defaultValue;
    }

    public function isEnum(): bool
    {
        return EnumAnalyzer::isEnum(
            str_replace('[]', '', $this->type),
        );
    }

    public function isArgument(): bool
    {
        return $this->isArgument;
    }

    public function getGetter(): string
    {
        if ($this->getter === '') {
            throw new PropertyHasNoGetter($this->class, $this->name);
        }

        return $this->getter;
    }

    public function isScalar(): bool
    {
        $type = str_replace('[]', '', $this->type);

        return in_array($type, ['int', 'float', 'string', 'bool'], true);
    }

    public function isArray(): bool
    {
        return str_contains($this->type, '[]');
    }

    public function isString(): bool
    {
        return $this->type === 'string';
    }
}
