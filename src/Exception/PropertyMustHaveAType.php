<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Exception;
use ReflectionClass;
use ReflectionParameter;

class PropertyMustHaveAType extends Exception
{
    public function __construct(ReflectionParameter $param, ReflectionClass $class)
    {
        parent::__construct(
            sprintf(
                'Property %s::%s must have a type',
                $class->getName(),
                $param->getName()
            )
        );
    }
}
