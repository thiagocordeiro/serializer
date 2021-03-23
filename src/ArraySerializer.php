<?php

declare(strict_types=1);

namespace Serializer;

use JsonException;
use ReflectionException;
use Serializer\Exception\ClassMustHaveAConstructor;
use Serializer\Exception\UnableToLoadOrCreateCacheClass;

class ArraySerializer extends Serializer
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
        /**
         * convert a key-value array to std object
         */
        $json = json_encode($data, JSON_THROW_ON_ERROR);
        $object = json_decode($json, false, JSON_THROW_ON_ERROR);

        return $this->decode($object, $class);
    }

    /**
     * @inheritDoc
     * @throws ClassMustHaveAConstructor
     * @throws ReflectionException
     * @throws UnableToLoadOrCreateCacheClass
     */
    public function serialize($data)
    {
        $data = $this->encode($data);

        return $this->encode($data);
    }
}
