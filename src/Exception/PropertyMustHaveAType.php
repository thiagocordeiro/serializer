<?php

declare(strict_types=1);

namespace Serializer\Exception;

use ReflectionClass;
use ReflectionParameter;

class PropertyMustHaveAType extends SerializerException
{
    public function __construct(ReflectionParameter $param, ReflectionClass $class)
    {
        parent::__construct(
            sprintf(
                'Property %s::%s must have a type',
                $class->getName(),
                $param->getName(),
            ),
        );
    }
}
