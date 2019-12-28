<?php

declare(strict_types=1);

namespace Serializer\Builder;

class ClassTemplate
{
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
        $string = <<<STIRNG
<?php

declare(strict_types=1);

namespace Serializer\Cache;

use Serializer\Deserializer;
use Serializer\Serializer;

class [cacheClassName] extends Deserializer
{
    public function parseObjectData(object \$data): object
    {
        return new \[className](
            [arguments]
        );
    }
}
STIRNG;

        $arguments = array_map(function (ClassProperty $param) {
            return $this->createArgument($param);
        }, $this->definition->getProperties());

        $string = str_replace('[cacheClassName]', $this->factoryName, $string);
        $string = str_replace('[className]', $this->definition->getName(), $string);
        $string = str_replace('[arguments]', trim(implode(",\n", $arguments)), $string);

        return $string;
    }

    private function createArgument(ClassProperty $property): string
    {
        if ($property->isScalar()) {
            return sprintf(
                "%s\$data->%s ?? %s",
                str_repeat(' ', 12),
                $property->getName(),
                $property->getDefaultValue()
            );
        }

        if ($property->isArray()) {
            return vsprintf("%s\$this->parseArrayData(\$data->%s ?? %s, \%s::class)", [
                str_repeat(' ', 12),
                $property->getName(),
                $property->getDefaultValue(),
                $property->getType(),
            ]);
        }

        return vsprintf("%s\$this->serializer()->parseData(\$data->%s ?? %s, \%s::class)", [
            str_repeat(' ', 12),
            $property->getName(),
            $property->getDefaultValue(),
            $property->getType(),
        ]);
    }
}
