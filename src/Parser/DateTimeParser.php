<?php

declare(strict_types=1);

namespace Serializer\Parser;

use DateTime;
use DateTimeInterface;
use Exception;
use Serializer\Exception\InvalidDateTimeProperty;
use Serializer\Parser;
use Throwable;

class DateTimeParser extends Parser
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

    /**
     * @param DateTime $object
     * @return mixed
     */
    public function encode(object $object)
    {
        return $object->format(DateTimeInterface::ISO8601);
    }
}
