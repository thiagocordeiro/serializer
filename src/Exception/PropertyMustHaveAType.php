<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Exception;
use ReflectionParameter;

class PropertyMustHaveAType extends Exception
{
    public function __construct(ReflectionParameter $param)
    {
        $class = $param->getDeclaringClass();

        parent::__construct(
            sprintf('Property %s::%s must have a type',
                $class->getName(),
                $param->getName()
            )
        );
    }
}
