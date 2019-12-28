<?php

declare(strict_types=1);

namespace Serializer;

abstract class Hydrator
{
    /** @var Serializer */
    private $serializer;

    abstract public function fromRawToHydrated(object $data): object;

    /**
     * @return mixed[] array
     */
    abstract public function fromHydratedToRaw(object $object): array;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serializer(): Serializer
    {
        return $this->serializer;
    }
}
