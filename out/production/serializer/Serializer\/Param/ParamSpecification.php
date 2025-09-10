<?php

namespace Serializer\Param;

use BackedEnum;

/**
 * @phpstan-type Definition string|array<string, Definition>
 */
interface ParamSpecification
{
    public string $name { get; }
    public string $type { get; }
    public ?string $genericType { get; }
    public bool $isList { get; }
    public mixed $default { get; }
    public bool $isClass { get; }
    public bool $isEnum { get; }
    public bool $isBoolean { get; }

    public function toGenericType(): self;

    /**
     * @template E of BackedEnum
     * @return list<E>
     */
    public function enumCases(): array;

    /**
     * @template E of BackedEnum
     * @return E|null
     */
    public function enumFrom(int|string $data);

    /**
     * @return Definition
     */
    public function definition();
}
