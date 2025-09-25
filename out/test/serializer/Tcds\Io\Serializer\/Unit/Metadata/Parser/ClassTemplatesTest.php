<?php

namespace Tcds\Io\Serializer\Unit\Metadata\Parser;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Generic\ArrayList;
use Tcds\Io\Serializer\Fixture\Pair;
use Tcds\Io\Serializer\Fixture\ReadOnly\Address;
use Tcds\Io\Serializer\Metadata\Parser\ClassTemplates;
use Tcds\Io\Serializer\SerializerTestCase;

class ClassTemplatesTest extends SerializerTestCase
{
    protected function setUp(): void
    {
        $this->parser = new ClassTemplates();
    }

    #[Test] public function templates_of_pair(): void
    {
        $class = Pair::class;

        $templates = ClassTemplates::of($class);

        $this->assertEquals(['K', 'V'], $templates);
    }

    #[Test] public function templates_of_array_list(): void
    {
        $class = ArrayList::class;

        $templates = ClassTemplates::of($class);

        $this->assertEquals(['GenericItem'], $templates);
    }

    #[Test] public function templates_of_address(): void
    {
        $class = Address::class;

        $templates = ClassTemplates::of($class);

        $this->assertEquals([], $templates);
    }
}
