<?php

declare(strict_types=1);

namespace Serializer;

interface SerializerFactory
{
    public function createEncoder(Serializer $serializer): Encoder;

    public function createDecoder(Serializer $serializer): Decoder;
}
