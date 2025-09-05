<?php

namespace Serializer;

use Serializer\Reader\Reader;

/**
 * @phpstan-type MapperType string|class-string<mixed>
 * @phpstan-type ReaderFn Reader::__invoke
 */
readonly class ObjectMapper
{
    /**
     * @template T
     * @param array<MapperType, array{
     *     reader: Reader|ReaderFn,
     * }> $typeMappers
     */
    public function __construct(
        private Reader $defaultTypeMapper,
        private array $typeMappers = [],
    ) {
    }

    /**
     * @template T
     * @return T
     */
    final public function readValue(string $type, mixed $data, array $trace = [])
    {
        $mapper = $this->typeMappers[$type]['reader'] ?? $this->defaultTypeMapper;

        return $mapper($data, $this, $type, $trace);
    }
}
