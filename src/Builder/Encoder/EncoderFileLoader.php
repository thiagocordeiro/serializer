<?php

declare(strict_types=1);

namespace Serializer\Builder\Encoder;

use Serializer\Encoder;
use Serializer\Serializer;

interface EncoderFileLoader
{
    public function load(Serializer $serializer, string $class): ?Encoder;
}
