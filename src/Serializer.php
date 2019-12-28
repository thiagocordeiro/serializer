<?php

declare(strict_types=1);

namespace Serializer;

interface Serializer
{
    /**
     * @param mixed $data
     * @return mixed[]|object|null
     */
    public function deserialize($data, string $class);

    /**
     * @param mixed[]|object|null $data
     * @return mixed
     */
    public function serialize($data);

    /**
     * @param mixed[]|object|null $data
     * @return mixed[]|object|null
     */
    public function deserializeData($data, string $class);

    /**
     * @param mixed $data
     * @return mixed[]
     */
    public function serializeData($data): ?array;
}
