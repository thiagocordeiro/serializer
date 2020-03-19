<?php

declare(strict_types=1);

namespace Serializer;

abstract class Parser
{
    /** @var Serializer */
    private $serializer;

    /**
     * @param string|mixed|object $data
     * @return object
     */
    abstract public function decode($data, ?string $propertyName = null): object;

    /**
     * @return mixed
     */
    abstract public function encode(object $object);

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
