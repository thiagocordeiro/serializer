<?php

declare(strict_types=1);

namespace Serializer\Builder;

use ReflectionClass as PHPReflectionClass;
use ReflectionMethod;
use Serializer\Exception\ClassMustHaveAConstructor;

class ReflectionClass extends PHPReflectionClass
{
    public function __construct(string $objectOrClass)
    {
        parent::__construct($objectOrClass);
    }

    public function getConstructor(): ReflectionMethod
    {
        $constructor = parent::getConstructor();

        if (null === $constructor) {
            throw new ClassMustHaveAConstructor($this->getName());
        }

        return $constructor;
    }
}
