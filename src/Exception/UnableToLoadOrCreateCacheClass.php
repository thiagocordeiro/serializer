<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Exception;

class UnableToLoadOrCreateCacheClass extends Exception
{
    public function __construct(string $factoryClass)
    {
        parent::__construct(
            sprintf('Unable to load or create %s class', $factoryClass)
        );
    }
}
