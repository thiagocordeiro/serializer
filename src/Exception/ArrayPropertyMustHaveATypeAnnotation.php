<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Exception;
use ReflectionParameter;

class ArrayPropertyMustHaveATypeAnnotation extends Exception
{
    public function __construct(ReflectionParameter $param)
    {
        $class = $param->getDeclaringClass();

        parent::__construct(
            sprintf('Array property %s::%s must have an array annotation',
                $class->getName(),
                $param->getName()
            )
        );
    }
}
