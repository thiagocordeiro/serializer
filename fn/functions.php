<?php

/**
 * @param list<string> $generics
 */
function generic(string $type, array $generics): string
{
    return empty($generics)
        ? $type
        : sprintf('%s<%s>', $type, join(', ', $generics));
}
