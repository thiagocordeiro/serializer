<?php

declare(strict_types=1);

namespace Serializer\Builder\Encoder\FileLoader;

use Serializer\Builder\Encoder\EncoderFileLoader;
use Serializer\Encoder;
use Serializer\Serializer;

class PipelineEncoderFileLoader implements EncoderFileLoader
{
    /** @var array<EncoderFileLoader> */
    private array $loaders;

    public function __construct(EncoderFileLoader ...$loaders)
    {
        $this->loaders = $loaders;
    }

    public function load(Serializer $serializer, string $class): ?Encoder
    {
        foreach ($this->loaders as $loader) {
            $encoder = $loader->load($serializer, $class);

            if (null === $encoder) {
                continue;
            }

            return $encoder;
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
            new CustomEncoderFileLoader($customEncoders),
            new FactoryEncoderFileLoader($factories),
            new RequireEncoderFileLoader($cacheDir),
            new CreateEncoderFileLoader($cacheDir),
        );
    }

    /**
     * @param array<string, string> $customEncoders
     * @param array<object> $factories
     */
    public static function light(string $cacheDir, array $customEncoders = [], array $factories = []): self
    {
        return new self(
            new CustomEncoderFileLoader($customEncoders),
            new FactoryEncoderFileLoader($factories),
            new RequireEncoderFileLoader($cacheDir),
        );
    }
}
