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
        return $this->decode($data, $class);
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
