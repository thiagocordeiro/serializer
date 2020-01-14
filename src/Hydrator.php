<?php

declare(strict_types=1);

namespace Serializer;

abstract class Hydrator
{
    /** @var Serializer */
    private $serializer;

    /**
     * @param string|object $data
     * @return object
     */
    abstract public function fromRawToHydrated($data, ?string $propertyName = null): object;

    /**
     * @return mixed
     */
    abstract public function fromHydratedToRaw(object $object);

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serializer(): Serializer
    {
        return $this->serializer;
    }
}
