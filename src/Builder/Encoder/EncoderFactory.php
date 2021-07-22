<?php

declare(strict_types=1);

namespace Serializer\Builder\Encoder;

use Exception;
use Serializer\Encoder;
use Serializer\Serializer;

class EncoderFactory
{
    private EncoderFileLoader $loader;

    public function __construct(EncoderFileLoader $loader)
    {
        $this->loader = $loader;
    }

    public function createEncoder(Serializer $serializer, string $class): Encoder
    {
        $encoder = $this->loader->load($serializer, $class);
        assert($encoder instanceof Encoder, new Exception("Unable to create encoder for class `$class`"));

        return $encoder;
    }
}
