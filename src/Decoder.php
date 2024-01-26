<?php

declare(strict_types=1);

namespace Serializer;

use BackedEnum;

/**
 * @template T of object
 */
abstract class Decoder
{
    private Serializer $serializer;

    /**
     * @return T
     */
    abstract public function decode(mixed $data, ?string $propertyName = null): object;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serializer(): Serializer
    {
        return $this->serializer;
    }

    public function isCollection(): bool
    {
        return false;
    }

    /**
     * @param class-string<BackedEnum> $enum
     * @return BackedEnum
     */
    protected function enum(string $enum, int|string $value): object
    {
        $filtered = array_filter(
            array: $enum::cases(),
            callback: fn($case) => strtolower("$case->value") === strtolower("$value"),
        );

        return reset($filtered) ?: $enum::from($value);
    }
}
