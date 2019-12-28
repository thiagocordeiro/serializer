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

use Serializer\Hydrator;

class [cacheClassName] extends Hydrator
{
    /**
     * @return \[className]
     */
    public function fromRawToHydrated(object \$data): object
    {
        return new \[className](
            [arguments]
        );
    }

    /**
     * @param \[className] \$object
     */
    public function fromHydratedToRaw(object \$object): array
    {
        return [
            [getters]
        ];
    }
}
STIRNG;

        $arguments = array_map(function (ClassProperty $param) {
            return $this->createArgument($param);
        }, $this->definition->getProperties());

        $getters = array_map(function (ClassProperty $param) {
            return $this->createGetter($param);
        }, $this->definition->getProperties());

        $string = str_replace('[cacheClassName]', $this->factoryName, $string);
        $string = str_replace('[className]', $this->definition->getName(), $string);
        $string = str_replace('[arguments]', trim(implode(",\n", $arguments)), $string);
        $string = str_replace('[getters]', trim(implode(",\n", $getters)), $string);

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

        return sprintf(
            "%s\$this->serializer()->deserializeData(\$data->%s ?? %s, \%s::class)",
            str_repeat(' ', 12),
            $property->getName(),
            $property->getDefaultValue(),
            $property->getType()
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
