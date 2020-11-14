<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Throwable;

class InvalidDateTimeProperty extends SerializerException
{
    public function __construct(Throwable $error, string $property)
    {
        parent::__construct(sprintf('Parameter "%s" is invalid', $property), 0, $error);
    }
}
