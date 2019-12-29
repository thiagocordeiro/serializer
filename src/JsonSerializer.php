<?php

declare(strict_types=1);

namespace Serializer;

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
     */
    public function deserialize($data, string $class)
    {
        return $this->deserializeData(
            json_decode($data, false),
            $class
        );
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
