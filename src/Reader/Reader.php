<?php

namespace Tcds\Io\Serializer\Reader;

use Tcds\Io\Serializer\ObjectMapper;

/**
 * @template T
 */
interface Reader
{
    /**
     * @param mixed $data
     * @param list<string> $trace
     * @return T
     */
    public function __invoke(mixed $data, ObjectMapper $mapper, string $type, array $trace);
}
