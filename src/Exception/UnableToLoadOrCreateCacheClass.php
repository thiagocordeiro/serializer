<?php

declare(strict_types=1);

namespace Serializer\Exception;

class UnableToLoadOrCreateCacheClass extends SerializerException
{
    public function __construct(string $factoryClass)
    {
        parent::__construct(
            sprintf('Unable to load or create %s class', $factoryClass),
        );
    }
}
