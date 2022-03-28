<?php

declare(strict_types=1);

namespace Serializer\Builder;

use BackedEnum;
use Exception;
use ReflectionClass;
use Throwable;
use UnitEnum;

class EnumAnalyzer
{
    /**
     * @throws Throwable
     */
    public static function analyze(string $class): ClassDefinition
    {
        $reflection = new ReflectionClass($class);

        if (false === $reflection->implementsInterface(BackedEnum::class)) {
            throw new Exception(sprintf(
                "Unable to parse non-backed enums, change to `enum %s: string|int { ... }` instead",
                $reflection->getName(),
            ));
        }

        return new ClassDefinition(
            name: $reflection->getName(),
            isCollection: false,
            isValueObject: false,
            isEnum: true,
            properties: [],
        );
    }

    /**
     * @param class-string $class
     */
    public static function isEnum(string $class): bool
    {
        if (false === class_exists($class)) {
            return false;
        }

        $ref = new ReflectionClass($class);

        return $ref->implementsInterface(BackedEnum::class) || $ref->implementsInterface(UnitEnum::class);
    }
}
