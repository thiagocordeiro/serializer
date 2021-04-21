<?php

declare(strict_types=1);

namespace Serializer\Encoder;

use DateTimeImmutable;
use DateTimeInterface;
use Serializer\Encoder;

class DateTimeImmutableEncoder extends Encoder
{
    /**
     * @inheritdoc
     * @param DateTimeImmutable $object
     */
    public function encode(object $object): array|string|int|float|bool|null
    {
        return $object->format(DateTimeInterface::ISO8601);
    }
}
