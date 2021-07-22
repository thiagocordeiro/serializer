<?php

declare(strict_types=1);

namespace Serializer\Builder\Decoder\FileLoader;

use Serializer\Builder\Decoder\DecoderFileLoader;
use Serializer\Decoder;
use Serializer\Serializer;

class CustomDecoderFileLoader implements DecoderFileLoader
{
    /** @var string[] */
    private array $customDecoders;

    /**
     * @param array<string, string> $customDecoders
     */
    public function __construct(array $customDecoders)
    {
        $this->customDecoders = $customDecoders;
    }

    public function load(Serializer $serializer, string $class): ?Decoder
    {
        $class = $this->customDecoders[$class] ?? null;

        if (null === $class) {
            return null;
        }

        return new $class($serializer);
    }
}
