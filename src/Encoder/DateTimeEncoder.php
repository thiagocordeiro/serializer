<?php

declare(strict_types=1);

namespace Serializer\Encoder;

use DateTime;
use DateTimeInterface;
use Serializer\Encoder;

class DateTimeEncoder extends Encoder
{
    /**
     * @param DateTime $object
     * @return mixed
     */
    public function encode(object $object)
    {
        return $object->format(DateTimeInterface::ISO8601);
    }
}
