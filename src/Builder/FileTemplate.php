<?php

declare(strict_types=1);

namespace Serializer\Builder;

interface FileTemplate
{
    public function __toString(): string;
}
