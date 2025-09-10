<?php

namespace Serializer\Param;

use Override;
use ReflectionClass;
use ReflectionParameter;
use Tcds\Io\Generic\ArrayList;

/**
 * @phpstan-import-type ParamName from ParamSpecificationRepository
 */
class RuntimeParamSpecificationRepository implements ParamSpecificationRepository
{
    /**
     * @template T
     * @param class-string<T> $class
     * @return array<ParamName, ParamSpecification>
     */
    #[Override] public function of(string $class): array
    {
        return new ArrayList(new ReflectionClass($class)->getConstructor()->getParameters())
            ->indexedBy(fn(ReflectionParameter $param) => $param->name)
            ->mapValues($this->specification(...))
            ->entries();
    }

    private function specification(ReflectionParameter $param): ParamSpecification
    {
        $type = Type::of($param);

        return new RuntimeParamSpecification(
            name: $param->name,
            type: $type->type,
            genericType: $type->generic,
            default: $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
        );
    }
}
