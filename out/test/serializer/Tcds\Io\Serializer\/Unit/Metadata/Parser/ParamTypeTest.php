<?php

namespace Tcds\Io\Serializer\Unit\Metadata\Parser;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Tcds\Io\Serializer\Fixture\Pair;
use Tcds\Io\Serializer\Fixture\ReadOnly\Address;
use Tcds\Io\Serializer\Fixture\ReadOnly\Place;
use Tcds\Io\Serializer\Metadata\Parser\ParamType;
use Tcds\Io\Serializer\Metadata\TypeNode;

class ParamTypeTest extends TestCase
{
    #[Test] public function given_a_param_of_generic_class_then_get_its_generic_type(): void
    {
        $class = Pair::class;

        [$key, $value] = new ReflectionClass($class)
            ->getConstructor()
            ->getParameters();

        $this->assertEquals(new TypeNode('K'), ParamType::of($key));
        $this->assertEquals(new TypeNode('V'), ParamType::of($value));
    }

    #[Test] public function given_a_param_of_a_class_then_get_its_type(): void
    {
        $class = Address::class;

        [$street, $number, $main, $place] = new ReflectionClass($class)
            ->getConstructor()
            ->getParameters();

        $this->assertEquals(new TypeNode('string'), ParamType::of($street));
        $this->assertEquals(new TypeNode('int'), ParamType::of($number));
        $this->assertEquals(new TypeNode('bool'), ParamType::of($main));
        $this->assertEquals(new TypeNode(Place::class), ParamType::of($place));
    }
}
