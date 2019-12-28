<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Exception;
use ReflectionParameter;

class ArrayPropertyMustHaveAnArrayAnnotation extends Exception
{
    public function __construct(ReflectionParameter $param, string $type)
    {
        $class = $param->getDeclaringClass();

        parent::__construct(
            sprintf('Array property %s::%s must have an array annotation, use %s[] instead',
                $class->getName(),
                $param->getName(),
                $type
            )
        );
    }
}
