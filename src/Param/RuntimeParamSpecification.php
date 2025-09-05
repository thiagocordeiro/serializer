<?php

namespace Serializer\Param;

use BackedEnum;
use Override;

class RuntimeParamSpecification implements ParamSpecification
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly bool $isList,
        public readonly mixed $default,
        private readonly ParamSpecificationRepository $specifications = new RuntimeParamSpecificationRepository(),
    ) {
    }

    public bool $isClass {
        get => class_exists($this->type);
    }

    public bool $isEnum {
        get => enum_exists($this->type);
    }

    public bool $isBoolean {
        get => $this->type === 'bool';
    }

    #[Override] public function copyWith(
        ?string $name = null,
        ?string $type = null,
        ?bool $isList = null,
        mixed $default = null,
    ): self {
        return new self(
            $name ?? $this->name,
            $type ?? $this->type,
            $isList ?? $this->isList,
            $default ?? $this->default,
        );
    }

    #[Override] public function enumCases(): array
    {
        /** @var BackedEnum $type */
        $type = $this->type;

        return $type::cases();
    }

    #[Override] public function enumFrom(int|string $data)
    {
        /** @var BackedEnum $type */
        $type = $this->type;

        return $type::tryFrom($data);
    }

    #[Override] public function definition(): mixed
    {
        $type = $this->type ?? null;

        return match (true) {
            $this->isList => [$this->copyWith(isList: false)->definition()],
            $this->isEnum => $this->enumCases(),
            $this->isClass => array_map(fn($s) => $s->definition(), $this->specifications->of($type)),
            default => $this->type,
        };
    }
}
