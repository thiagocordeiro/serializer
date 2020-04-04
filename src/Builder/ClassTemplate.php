<?php

declare(strict_types=1);

namespace Serializer\Builder;

class ClassTemplate
{
    private const TEMPLATE = <<<STIRNG
<?php

declare(strict_types=1);

namespace Serializer\Parser;

use Serializer\Exception\MissingOrInvalidProperty;
use Serializer\Parser;
use TypeError;

class [cacheClassName] extends Parser
{
    /**
     * @return \[className]
     */
    public function decode(\$data, ?string \$propertyName = null): object
    {
        try {
            \$object = new \[className](
                [arguments]
            );
        } catch (TypeError \$e) {
            throw new MissingOrInvalidProperty(\$e, [[properties]]);
        }

        return \$object;
    }

    /**
     * @param \[className] \$object
     */
    public function encode(object \$object)
    {
        return [getters];
    }
    
    public function isCollection(): bool
    {
        return [isCollection];
    }
}
STIRNG;

    /** @var ClassDefinition */
    private $definition;

    /** @var string */
    private $factoryName;

    public function __construct(ClassDefinition $definition, string $factoryName)
    {
        $this->definition = $definition;
        $this->factoryName = $factoryName;
    }

    public function __toString(): string
    {
        $string = self::TEMPLATE;

        if ($this->definition->isValueObject()) {
            $string = str_replace('[cacheClassName]', $this->factoryName, $string);
            $string = str_replace('[className]', $this->definition->getName(), $string);
            $string = str_replace('[arguments]', '$data', $string);
            $string = str_replace('[properties]', '$propertyName', $string);
            $string = str_replace('[getters]', '(string) $object', $string);
            $string = str_replace('[isCollection]', 'false', $string);

            return $string;
        }

        $arguments = array_map(function (ClassProperty $param) {
            return $this->createArgument($param, $this->definition);
        }, $this->definition->getProperties());

        $properties = array_map(function (ClassProperty $param) {
            return sprintf("'%s'", $param->getName());
        }, $this->definition->getProperties());

        $getters = array_map(function (ClassProperty $param) {
            return $this->createGetter($param);
        }, $this->definition->getProperties());

        $sp1 = str_repeat(" ", 12);
        $sp2 = str_repeat(" ", 8);

        $string = str_replace('[cacheClassName]', $this->factoryName, $string);
        $string = str_replace('[className]', $this->definition->getName(), $string);
        $string = str_replace('[arguments]', trim(implode(",\n", $arguments)), $string);
        $string = str_replace('[properties]', trim(implode(", ", $properties)), $string);
        $string = str_replace('[getters]', "[\n" . $sp1 . trim(implode(",\n", $getters)) . ",\n" . $sp2 . "]", $string);
        $string = str_replace('[isCollection]', $this->definition->isCollection() ? 'true' : 'false', $string);

        return $string;
    }

    private function createArgument(ClassProperty $property, ClassDefinition $definition): string
    {
        if ($property->isScalar()) {
            return sprintf(
                "%s\$data->%s ?? %s",
                str_repeat(' ', 16),
                $property->getName(),
                $property->getDefaultValue()
            );
        }

        if ($definition->isCollection()) {
            return sprintf(
                "%s...\$this->serializer()->deserializeData(\$data ?? [], \%s::class)",
                str_repeat(' ', 16),
                $property->getType()
            );
        }

        return sprintf(
            "%s%s\$this->serializer()->deserializeData(\$data->%s ?? %s, \%s::class, '%s')",
            str_repeat(' ', 16),
            $property->isArgument() ? '...' : '',
            $property->getName(),
            $property->getDefaultValue(),
            $property->getType(),
            $property->getName()
        );
    }

    private function createGetter(ClassProperty $property): string
    {
        if ($property->isScalar()) {
            return sprintf(
                "%s'%s' => \$object->%s()",
                str_repeat(' ', 12),
                $property->getName(),
                $property->getGetter()
            );
        }

        return sprintf(
            "%s'%s' => \$this->serializer()->serializeData(\$object->%s())",
            str_repeat(' ', 12),
            $property->getName(),
            $property->getGetter()
        );
    }
}
