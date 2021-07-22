<?php

declare(strict_types=1);

namespace Serializer\Builder\Decoder\FileLoader;

use Serializer\Builder\Decoder\DecoderFileLoader;
use Serializer\Decoder;
use Serializer\Serializer;

class PipelineDecoderFileLoader implements DecoderFileLoader
{
    /** @var array<DecoderFileLoader> */
    private array $loaders;

    public function __construct(DecoderFileLoader ...$loaders)
    {
        $this->loaders = $loaders;
    }

    public function load(Serializer $serializer, string $class): ?Decoder
    {
        foreach ($this->loaders as $loader) {
            $decoder = $loader->load($serializer, $class);

            if (null === $decoder) {
                continue;
            }

            return $decoder;
        }

        return null;
    }

    /**
     * @param array<string, string> $customEncoders
     * @param array<object> $factories
     */
    public static function full(string $cacheDir, array $customEncoders = [], array $factories = []): self
    {
        return new self(
            new CustomDecoderFileLoader($customEncoders),
            new FactoryDecoderFileLoader($factories),
            new RequireDecoderFileLoader($cacheDir),
            new CreateDecoderFileLoader($cacheDir),
        );
    }

    /**
     * @param array<string, string> $customEncoders
     * @param array<object> $factories
     */
    public static function light(string $cacheDir, array $customEncoders = [], array $factories = []): self
    {
        return new self(
            new CustomDecoderFileLoader($customEncoders),
            new FactoryDecoderFileLoader($factories),
            new RequireDecoderFileLoader($cacheDir),
        );
    }
}
