<?php

declare(strict_types=1);

namespace Serializer\Exception;

class PropertyHasNoGetter extends SerializerException
{
    public function __construct(string $class, string $getter, bool $boolean = false)
    {
        $message = $boolean ? '%s::%s has no boolean getter (is... or has...)' : '%s::%s has no getter';

        parent::__construct(sprintf($message, $class, $getter));
    }
}
