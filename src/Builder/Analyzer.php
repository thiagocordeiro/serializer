<?php

declare(strict_types=1);

namespace Serializer\Builder;

use Serializer\Exception\ClassMustHaveAConstructor;
use Throwable;

class Analyzer
{
    /**
     * @throws ClassMustHaveAConstructor
     * @throws Throwable
     */
    public static function analyze(string $class): ClassDefinition
    {
        return EnumAnalyzer::isEnum($class) ? EnumAnalyzer::analyze($class) : ClassAnalyzer::analyze($class);
    }
}
