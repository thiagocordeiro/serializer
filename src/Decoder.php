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
     * @param int|string|array<int|string> $value
     * @return BackedEnum|array<int|string|mixed>|null
     */
    protected function enum(string $enum, int|string|array|null $value): object|array|null
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($value)) {
            return array_map(fn($v) => $this->enum($enum, $v), $value);
        }

        $filtered = array_filter(
            array: $enum::cases(),
            callback: fn($case) => strtolower("$case->value") === strtolower("$value"),
        );

        return reset($filtered) ?: $enum::from($value);
    }
}
