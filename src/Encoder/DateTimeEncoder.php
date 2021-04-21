<?php

declare(strict_types=1);

namespace Serializer\Encoder;

use DateTime;
use DateTimeInterface;
use Serializer\Encoder;

class DateTimeEncoder extends Encoder
{
    /**
     * @inheritdoc
     * @param DateTime $object
     */
    public function encode(object $object): array|string|int|float|bool|null
    {
        return $object->format(DateTimeInterface::ISO8601);
    }
}
