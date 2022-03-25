<?php

declare(strict_types=1);

namespace Serializer\Exception;

use ReflectionClass;
use Serializer\Builder\ClassProperty;

class ValueObjectMustHaveScalarValue extends SerializerException
{
    public function __construct(ClassProperty $param, ReflectionClass $class)
    {
        parent::__construct(
            sprintf(
                'Value object %s must have a scalar property, %s given',
                $class->getName(),
                $param->getType(),
            ),
        );
    }
}
