<?php

declare(strict_types=1);

namespace Serializer;

use JsonException;
use ReflectionException;
use Serializer\Exception\ClassMustHaveAConstructor;
use Serializer\Exception\UnableToLoadOrCreateCacheClass;

class JsonSerializer extends Serializer
{
    /**
     * @inheritDoc
     * @return mixed[]|object|null
     * @throws ClassMustHaveAConstructor
     * @throws UnableToLoadOrCreateCacheClass
     * @throws ReflectionException
     * @throws JsonException
     */
    public function deserialize($data, string $class)
    {
        $json = json_decode($data, false, 512, JSON_THROW_ON_ERROR);

        return $this->deserializeData($json, $class);
    }

    /**
     * @inheritDoc
     * @throws ClassMustHaveAConstructor
     * @throws ReflectionException
     * @throws UnableToLoadOrCreateCacheClass
     */
    public function serialize($data)
    {
        return json_encode($this->serializeData($data)) ?: '';
    }
}
