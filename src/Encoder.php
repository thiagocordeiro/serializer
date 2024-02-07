<?php

declare(strict_types=1);

namespace Serializer;

use BackedEnum;

/**
 * @template T of object
 */
abstract class Encoder
{
    private Serializer $serializer;

    /**
     * @return array<mixed>
     */
    abstract public function encode(object $object): array|string|int|float|bool|null;

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
     * @param BackedEnum|array<BackedEnum> $enum
     * @return string|int|array<int|string>
     */
    public function enum(BackedEnum|array $enum): string|int|array
    {
        if (is_array($enum)) {
            return array_map(fn($e) => $e->value, $enum);
        }

        return $enum->value;
    }
}
