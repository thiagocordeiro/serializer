<?php

declare(strict_types=1);

namespace Serializer;

use IteratorAggregate;
use ReflectionException;
use Serializer\Exception\ClassMustHaveAConstructor;
use Serializer\Exception\MissingOrInvalidProperty;
use Serializer\Exception\UnableToLoadOrCreateCacheClass;

abstract class Serializer
{
    /** @var Parser[] */
    private $factories = [];

    /** @var ClassFactory */
    private $classFactory;

    /**
     * @param mixed $data
     * @return mixed[]|object|null
     * @throws MissingOrInvalidProperty
     */
    abstract public function deserialize($data, string $class);

    /**
     * @param mixed[]|object|null $data
     * @return mixed
     */
    abstract public function serialize($data);

    public function __construct(ClassFactory $classFactory)
    {
        $this->classFactory = $classFactory;
    }

    /**
     * @param mixed[]|object|null $data
     * @return mixed[]|object|null
     * @throws ClassMustHaveAConstructor
     * @throws ReflectionException
     * @throws UnableToLoadOrCreateCacheClass
     * @throws MissingOrInvalidProperty
     */
    public function deserializeData($data, string $class, ?string $propertyName = null)
    {
        if (null === $data) {
            return null;
        }

        $factory = $this->loadOrCreateFactory($class);

        if ($factory->isCollection()) {
            return $factory->decode($data, $propertyName);
        }

        if (true === is_array($data)) {
            return array_map(function (object $item) use ($class) {
                return $this->deserializeData($item, $class);
            }, $data);
        }

        return $factory->decode($data, $propertyName);
    }

    /**
     * @param mixed $data
     * @return string[]|mixed[]|null
     * @throws ClassMustHaveAConstructor
     * @throws ReflectionException
     * @throws UnableToLoadOrCreateCacheClass
     */
    public function serializeData($data)
    {
        if (null === $data) {
            return null;
        }

        if ($data instanceof IteratorAggregate) {
            $data = iterator_to_array($data);
        }

        if (true === is_array($data)) {
            return array_map(function ($object) {
                return $this->serializeData($object);
            }, $data);
        }
        
        if (false === is_object($data)) {
            return $data;
        }

        $class = get_class($data);
        $factory = $this->loadOrCreateFactory($class);

        return $factory->encode($data);
    }

    /**
     * @throws ClassMustHaveAConstructor
     * @throws UnableToLoadOrCreateCacheClass
     * @throws ReflectionException
     */
    private function loadOrCreateFactory(string $class): Parser
    {
        if (false === isset($this->factories[$class])) {
            $this->factories[$class] = $this->classFactory->createInstance($this, $class);
        }

        return $this->factories[$class];
    }
}
