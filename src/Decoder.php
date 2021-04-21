<?php

declare(strict_types=1);

namespace Serializer;

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
}
