<?php

namespace Tcds\Io\Serializer\Unit\Metadata\Parser;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Tcds\Io\Generic\ArrayList;
use Tcds\Io\Serializer\Fixture\GenericStubs;
use Tcds\Io\Serializer\Fixture\Pair;
use Tcds\Io\Serializer\Fixture\ReadOnly\Address;
use Tcds\Io\Serializer\Metadata\ParamNode;
use Tcds\Io\Serializer\Metadata\Parser\Annotation;
use Tcds\Io\Serializer\Metadata\TypeNode;

class AnnotationTest extends TestCase
{
    #[Test] public function given_an_annotated_generic_type_then_get_generics(): void
    {
        $annotation = sprintf('%s<%s, %s>', Pair::class, 'string', Address::class);

        [$type, $generics] = Annotation::extractGenerics($annotation);

        $this->assertEquals(Pair::class, $type);
        $this->assertEquals(['string', Address::class], $generics);
    }

    #[Test] public function get_param_annotation(): void
    {
        $function = new ReflectionClass(Pair::class)->getConstructor() ?: throw new Exception('Class does not have constructor');

        $foo = Annotation::param($function, name: 'foo');
        $key = Annotation::param($function, name: 'key');
        $value = Annotation::param($function, name: 'value');

        $this->assertEquals(null, $foo);
        $this->assertEquals('K', $key);
        $this->assertEquals('V', $value);
    }

    #[Test] public function when_class_has_list_then_get_its_type_node(): void
    {
        $reflection = new ReflectionClass(ArrayList::class);

        $this->assertEquals(
            new TypeNode(
                type: 'list',
                params: [
                    'items' => new ParamNode(new TypeNode('string')),
                ],
            ),
            Annotation::generic($reflection, 'list<GenericItem>'),
        );
    }

    #[Test] public function when_class_has_generics_then_get_its_type_node(): void
    {
        $genericStubs = new ReflectionClass(GenericStubs::class);
//        $address = new ReflectionClass(Address::class);

        $this->assertEquals(
            new TypeNode(
                type: ArrayList::class,
                params: [
                    'items' => new ParamNode(new TypeNode('string')),
                ],
            ),
            Annotation::generic($genericStubs, 'ArrayList<string>'),
        );
//        $this->assertEquals(
//            new TypeNode(
//                type: Map::class,
//                params: [
//                    new TypeNode('string'),
//                    new TypeNode(Address::class),
//                ],
//            ),
//            Annotation::typeNodeOf($genericStubs, 'Map<string, Address>'),
//        );
//        $this->assertEquals(
//            new TypeNode(Place::class),
//            Annotation::typeNodeOf($address, 'Place'),
//        );
//        $this->assertEquals(
//            new TypeNode(Place::class),
//            Annotation::typeNodeOf($address, Place::class),
//        );
    }
}
