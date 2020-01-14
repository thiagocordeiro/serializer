<?php

declare(strict_types=1);

namespace Serializer\Hydrator;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Serializer\Exception\InvalidDateTimeProperty;
use Serializer\Hydrator;
use Throwable;

class DateTimeImmutableHydrator extends Hydrator
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function fromRawToHydrated($data, ?string $propertyName = null): object
    {
        try {
            return new DateTimeImmutable((string) $data);
        } catch (Throwable $e) {
            throw new InvalidDateTimeProperty($e, (string) $propertyName);
        }
    }

    /**
     * @param DateTimeImmutable $object
     * @return mixed
     */
    public function fromHydratedToRaw(object $object)
    {
        return $object->format(DateTimeInterface::ISO8601);
    }
}
