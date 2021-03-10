<?php

declare(strict_types=1);

namespace Serializer\Encoder;

use DateTimeImmutable;
use DateTimeInterface;
use Serializer\Encoder;

class DateTimeImmutableEncoder extends Encoder
{
    /**
     * @param DateTimeImmutable $object
     * @return mixed
     */
    public function encode(object $object)
    {
        return $object->format(DateTimeInterface::ISO8601);
    }
}
