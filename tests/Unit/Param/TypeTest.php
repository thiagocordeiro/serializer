<?php

namespace Serializer\Param;

function file_get_contents(string $name): string
{
    if ($name === 'Foo\Bar.php') {
        return <<<STRING
        use Biz\Baz\Place;
        STRING;
    }

    return \file_get_contents($name);
}

namespace Tcds\Io\Serializer\Unit\Param;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionNamedType;
use ReflectionParameter;
use Tcds\Io\Serializer\Metadata\Generic;
use Tcds\Io\Serializer\SerializerTestCase;

class TypeTest extends SerializerTestCase
{
    private ReflectionParameter&MockObject $param;
    private ReflectionNamedType&MockObject $type;
    private ReflectionFunctionAbstract&MockObject $function;
    private ReflectionClass&MockObject $class;

    protected function setUp(): void
    {
        $this->param = $this->createMock(ReflectionParameter::class);
        $this->type = $this->createMock(ReflectionNamedType::class);
        $this->function = $this->createMock(ReflectionFunctionAbstract::class);
        $this->class = $this->createMock(ReflectionClass::class);
    }

    #[Test] public function generic_list_param(): void
    {
        $this->setupParam(
            name: 'stops',
            type: 'array',
            docBlock: '@param list<Place> $stops',
            class: 'Foo\\Bar',
        );

        $type = Generic::from($this->param);

        $this->assertEquals(
            new Generic(resolved: 'list', annotated: 'list<Place>', templates: ['Biz\Baz\Place']),
            $type,
        );
    }

    #[Test] public function generic_map_param(): void
    {
        $this->setupParam(
            name: 'stops',
            type: 'array',
            docBlock: '@param array<string, Place> $stops',
            class: 'Foo\\Bar',
        );

        $type = Generic::from($this->param);

        $this->assertEquals(
            new Generic(resolved: 'map', annotated: 'array<string, Place>', templates: ['string', 'Biz\Baz\Place']),
            $type,
        );
    }

    /**
     * @noinspection PhpSameParameterValueInspection
     */
    private function setupParam(string $name, string $type, string $docBlock, string $class): void
    {
        $this->param->method('getType')->willReturn($this->type);
        $this->param->method('getName')->willReturn($name);
        $this->param->method('getDeclaringFunction')->willReturn($this->function);
        $this->param->method('getDeclaringClass')->willReturn($this->class);

        $this->type->method('getName')->willReturn($type);

        $this->function->method('getDocComment')->willReturn($docBlock);
        $this->class->method('getName')->willReturn($class);
        $this->class->method('getFileName')->willReturn("$class.php");
        $this->class->method('getNamespaceName')->willReturn("Foo");
    }
}
