<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Exception;

class IterableMustHaveOneParameterOnly extends Exception
{
    public function __construct(string $class, int $count)
    {
        parent::__construct(
            sprintf('Iterable %s must have one parameter only, %s found', $class, $count)
        );
    }
}
