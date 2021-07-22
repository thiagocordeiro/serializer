<?php

declare(strict_types=1);

namespace Serializer\Builder\Decoder;

use Serializer\Decoder;
use Serializer\Serializer;

interface DecoderFileLoader
{
    public function load(Serializer $serializer, string $class): ?Decoder;
}
