<?php

namespace Tcds\Io\Serializer\Reader;

use Override;
use Tcds\Io\Serializer\Exception\UnableToParseValue;
use Tcds\Io\Serializer\ObjectMapper;
use Tcds\Io\Serializer\Param\ParamSpecification;
use Tcds\Io\Serializer\Param\ParamSpecificationRepository;
use Tcds\Io\Serializer\Param\RuntimeParamSpecificationRepository;
use TypeError;

/**
 * @template T
 */
readonly class RuntimeReader implements Reader
{
    public function __construct(
        private ParamSpecificationRepository $params = new RuntimeParamSpecificationRepository(),
    ) {
    }

    #[Override] public function __invoke(mixed $data, ObjectMapper $mapper, string $type, array $trace)
    {
        $specifications = $this->params->of($type);
        $normalized = [];

        if (is_scalar($data) && count($specifications) === 1) {
            $param = $specifications[0];

            $data = [$param->name => $data];
        }

        foreach ($specifications as $specification) {
            $prop = $specification->name;
            $value = $data[$specification->name] ?? null;
            $innerTrace = [...$trace, $prop];

            try {
                $normalized[$prop] = $this->paramMapper($mapper, $specification, $value, $innerTrace);
            } catch (TypeError) {
                throw new UnableToParseValue($innerTrace, $specification->definition(), $value);
            }
        }

        return new $type(...$normalized);
    }

    /**
     * @param list<string> $trace
     */
    private function paramMapper(ObjectMapper $mapper, ParamSpecification $spec, mixed $data, array $trace): mixed
    {
        return match (true) {
            $spec->isBoolean => filter_var($data, FILTER_VALIDATE_BOOL),
            $spec->isList => array_map(fn($v) => $this->paramMapper(
                mapper: $mapper,
                spec: $spec->listType(),
                data: $v,
                trace: $trace,
            ), $data),
            $spec->isEnum => $spec->enumFrom($data),
            $spec->isClass => $mapper->readValue($spec->type->resolved, $data, $trace),
            default => $data,
        };
    }
}
