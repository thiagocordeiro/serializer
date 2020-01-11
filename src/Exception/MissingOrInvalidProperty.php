<?php

declare(strict_types=1);

namespace Serializer\Exception;

use Exception;

class MissingOrInvalidProperty extends Exception
{
    /**
     * @param string[] $properties
     */
    public function __construct(string $errorMessage, array $properties)
    {
        $message = $this->buildMessage($errorMessage, $properties);

        parent::__construct($message, 0, null);
    }

    /**
     * @param string[] $properties
     */
    private function buildMessage(string $errorMessage, array $properties): string
    {
        $property = $this->getArgument($errorMessage, $properties);

        if ($property === null) {
            return 'Invalid body';
        }

        $givenType = $this->getGivenType($errorMessage);

        if ($givenType === 'null') {
            return sprintf('Parameter "%s" is required', $property);
        }

        return sprintf('Parameter "%s" is invalid', $property);
    }

    /**
     * @param string[] $properties
     */
    private function getArgument(string $errorMessage, array $properties): ?string
    {
        preg_match('/Argument (.*) passed/', $errorMessage, $matches);
        $argument = $matches[1] ?? null;

        if (!$argument) {
            return null;
        }

        return $properties[$argument - 1] ?? null;
    }

    private function getGivenType(string $errorMessage): ?string
    {
        preg_match('/must be (.*), (.*) given/', $errorMessage, $matches);

        return $matches[2] ?? 'null';
    }
}
