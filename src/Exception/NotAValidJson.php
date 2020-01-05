<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Exception;

class NotAValidJson extends Exception
{
    public function __construct(string $data)
    {
        parent::__construct(
            sprintf('Given data has an invalid json: %s', $data)
        );
    }
}
