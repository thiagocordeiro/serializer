<?php

declare(strict_types=1);

namespace Serializer\Builder\Decoder;

use Exception;
use Serializer\Decoder;
use Serializer\Serializer;

class DecoderFactory
{
    private DecoderFileLoader $loader;

    public function __construct(DecoderFileLoader $loader)
    {
        $this->loader = $loader;
    }

    public function createDecoder(Serializer $serializer, string $class): Decoder
    {
        $decoder = $this->loader->load($serializer, $class);
        assert($decoder instanceof Decoder, new Exception("Unable to create decoder for class `$class`"));

        return $decoder;
    }
}
