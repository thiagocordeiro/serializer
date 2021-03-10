<?php

declare(strict_types=1);

namespace Serializer\Decoder;

use DateTime;
use Exception;
use Serializer\Decoder;
use Serializer\Exception\InvalidDateTimeProperty;
use Throwable;

class DateTimeDecoder extends Decoder
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function decode($data, ?string $propertyName = null): object
    {
        try {
            return new DateTime((string) $data);
        } catch (Throwable $e) {
            throw new InvalidDateTimeProperty($e, (string) $propertyName);
        }
    }
}
