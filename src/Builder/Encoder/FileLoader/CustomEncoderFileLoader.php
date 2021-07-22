<?php

declare(strict_types=1);

namespace Serializer\Builder\Encoder\FileLoader;

use Serializer\Builder\Encoder\EncoderFileLoader;
use Serializer\Encoder;
use Serializer\Serializer;

class CustomEncoderFileLoader implements EncoderFileLoader
{
    /** @var string[] */
    private array $customEncoders;

    /**
     * @param array<string, string> $customEncoders
     */
    public function __construct(array $customEncoders)
    {
        $this->customEncoders = $customEncoders;
    }

    public function load(Serializer $serializer, string $class): ?Encoder
    {
        $class = $this->customEncoders[$class] ?? null;

        if (null === $class) {
            return null;
        }

        return new $class($serializer);
    }
}
