<?php

declare(strict_types=1);

namespace Serializer\Builder\Encoder\FileLoader;

use Serializer\Builder\Encoder\EncoderFileLoader;
use Serializer\Encoder;
use Serializer\Serializer;
use Serializer\SerializerFactory;

class FactoryEncoderFileLoader implements EncoderFileLoader
{
    /** @var array<object> */
    private array $factories;

    /**
     * @param array<object> $factories
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    public function load(Serializer $serializer, string $class): ?Encoder
    {
        $factory = $this->factories[$class] ?? null;

        if (null === $factory) {
            return null;
        }

        assert($factory instanceof SerializerFactory);

        return $factory->createEncoder($serializer);
    }
}
