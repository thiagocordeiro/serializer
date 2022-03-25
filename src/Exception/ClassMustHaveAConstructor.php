<?php

declare(strict_types=1);

namespace Serializer\Exception;

class ClassMustHaveAConstructor extends SerializerException
{
    public function __construct(string $class)
    {
        parent::__construct(
            sprintf('Class %s must have a constructor', $class),
        );
    }
}
