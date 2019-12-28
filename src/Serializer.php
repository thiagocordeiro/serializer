<?php

declare(strict_types=1);

namespace Serializer;

use Throwable;

class Serializer
{
    /** @var Deserializer[] */
    private $factories = [];

    /** @var ClassFactory */
    private $classFactory;

    public function __construct(ClassFactory $classFactory)
    {
        $this->classFactory = $classFactory;
    }

    /**
     * @return mixed[]|object|null
     * @throws Throwable
     */
    public function deserialize(string $json, string $class)
    {
        $data = json_decode($json, false);

        return $this->parseData($data, $class);
    }

    /**
     * @param mixed[]|object|null $data
     * @return mixed[]|object|null
     * @throws Throwable
     */
    public function parseData($data, string $class)
    {
        if (null === $data) {
            return null;
        }

        if (false === isset($this->factories[$class])) {
            $this->factories[$class] = $this->classFactory->createInstance($this, $class);
        }

        $factory = $this->factories[$class];

        if (true === is_array($data)) {
            return $factory->parseArrayData($data, $class);
        }

        return $factory->parseObjectData($data);
    }
}
