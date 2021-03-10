<?php

declare(strict_types=1);

namespace Serializer\Decoder;

use DateTimeImmutable;
use Exception;
use Serializer\Decoder;
use Serializer\Exception\InvalidDateTimeProperty;
use Throwable;

class DateTimeImmutableDecoder extends Decoder
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function decode($data, ?string $propertyName = null): object
    {
        try {
            return new DateTimeImmutable((string) $data);
        } catch (Throwable $e) {
            throw new InvalidDateTimeProperty($e, (string) $propertyName);
        }
    }
}
