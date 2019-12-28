<?php

declare(strict_types=1);

namespace Serializer;

interface Serializer
{
    /**
     * @return mixed[]|object|null
     */
    public function deserialize($data, string $class);

    /**
     * @param mixed[]|object|null $data
     * @return mixed[]|object|null
     */
    public function serialize($data);

    /**
     * @param mixed[]|object|null $data
     * @return mixed[]|object|null
     */
    public function deserializeData($data, string $class);

    /**
     * @param mixed $object
     * @return string
     */
    public function serializeData($data): ?array;
}
