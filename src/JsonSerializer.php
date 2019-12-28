<?php

declare(strict_types=1);

namespace Serializer;

class JsonSerializer implements Serializer
{
    /** @var Hydrator[] */
    private $factories = [];

    /** @var ClassFactory */
    private $classFactory;

    public function __construct(ClassFactory $classFactory)
    {
        $this->classFactory = $classFactory;
    }

    /**
     * @inheritDoc
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
     */
    public function serialize($data)
    {
        return json_encode($this->serializeData($data)) ?: '';
    }

    /**
     * @inheritDoc
     */
    public function deserializeData($data, string $class)
    {
        if (null === $data) {
            return null;
        }

        if (true === is_array($data)) {
            return array_map(function (object $item) use ($class) {
                return $this->deserializeData($item, $class);
            }, $data);
        }

        $factory = $this->loadOrCreateFactory($class);

        return $factory->fromRawToHydrated($data);
    }

    /**
     * @inheritDoc
     */
    public function serializeData($data): ?array
    {
        if (null === $data) {
            return null;
        }

        if (true === is_array($data)) {
            return array_map(function ($object): ?array {
                return $this->serializeData($object);
            }, $data);
        }

        $class = get_class($data);

        $factory = $this->loadOrCreateFactory($class);

        return $factory->fromHydratedToRaw($data);
    }

    private function loadOrCreateFactory(string $class): Hydrator
    {
        if (false === isset($this->factories[$class])) {
            $this->factories[$class] = $this->classFactory->createInstance($this, $class);
        }

        return $this->factories[$class];
    }
}
