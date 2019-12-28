<?php

declare(strict_types=1);

namespace Serializer;

abstract class Deserializer
{
    /** @var Serializer */
    private $serializer;

    abstract public function parseObjectData(object $data): object;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    public function parseArrayData(array $data, string $class): array
    {
        return array_map(function (object $item) use ($class) {
            return $this->serializer->parseData($item, $class);
        }, $data);
    }

    public function serializer(): Serializer
    {
        return $this->serializer;
    }
}
