<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Exception;
use ReflectionClass;

class PropertyHasNoGetter extends Exception
{
    public function __construct(ReflectionClass $class, string $getter)
    {
        parent::__construct(
            sprintf(
                'Class %s must have a method %s',
                $class->getName(),
                $getter
            )
        );
    }
}
