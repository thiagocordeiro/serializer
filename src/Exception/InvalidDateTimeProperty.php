<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Exception;
use Throwable;

class InvalidDateTimeProperty extends Exception
{
    public function __construct(Throwable $error, string $property)
    {
        parent::__construct(sprintf('Parameter "%s" is invalid', $property), 0, $error);
    }
}
