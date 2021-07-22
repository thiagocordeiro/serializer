<?php

declare(strict_types=1);

namespace Serializer\Builder\Decoder\FileLoader;

use Serializer\Builder\Decoder\DecoderFileLoader;
use Serializer\Decoder;
use Serializer\Serializer;
use Serializer\SerializerFactory;

class FactoryDecoderFileLoader implements DecoderFileLoader
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

    public function load(Serializer $serializer, string $class): ?Decoder
    {
        $factory = $this->factories[$class] ?? null;

        if (null === $factory) {
            return null;
        }

        assert($factory instanceof SerializerFactory);

        return $factory->createDecoder($serializer);
    }
}
