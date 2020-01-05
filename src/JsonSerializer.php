<?php

declare(strict_types=1);

namespace Serializer;

use ReflectionException;
use Serializer\Exception\ClassMustHaveAConstructor;
use Serializer\Exception\NotAValidJson;
use Serializer\Exception\UnableToLoadOrCreateCacheClass;

class JsonSerializer extends Serializer
{
    /**
     * @inheritDoc
     * @return mixed[]|object|null
     * @throws ClassMustHaveAConstructor
     * @throws UnableToLoadOrCreateCacheClass
     * @throws ReflectionException
     * @throws NotAValidJson
     */
    public function deserialize($data, string $class)
    {
        $json = json_decode($data, false);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new NotAValidJson($data);
        }

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
