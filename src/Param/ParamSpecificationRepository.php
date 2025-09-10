<?php

namespace Tcds\Io\Serializer\Param;

/**
 * @phpstan-type ParamName string
 */
interface ParamSpecificationRepository
{
    /**
     * @template T
     * @param class-string<T> $class
     * @return array<ParamName, ParamSpecification>
     */
    public function of(string $class): array;
}
