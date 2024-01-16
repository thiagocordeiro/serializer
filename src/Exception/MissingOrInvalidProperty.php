<?php

declare(strict_types=1);

namespace Serializer\Exception;

use BackedEnum;
use TypeError;
use ValueError;

class MissingOrInvalidProperty extends SerializerException
{
    /**
     * @param string[] $properties
     */
    public function __construct(TypeError|ValueError $error, array $properties)
    {
        $message = $this->buildMessage($error->getMessage(), $properties);

        parent::__construct($message, 0, $error);
    }

    /**
     * @param string[] $properties
     */
    private function buildMessage(string $errorMessage, array $properties): string
    {
        if (str_contains($errorMessage, 'is not a valid backing value for enum')) {
            return $this::invalidEnumMessage($errorMessage);
        }

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
        /**
         * php >= 8 parser
         */
        preg_match('#\(\$(.*?)\)#', $errorMessage, $matches);

        if ($matches[1] ?? null) {
            return $matches[1];
        }

        /**
         * php < 8
         */
        preg_match('/Argument (.*) passed/', $errorMessage, $matches);
        $argument = $matches[1] ?? null;

        if (!$argument) {
            return null;
        }

        $position = (int)$argument;

        return $properties[$position - 1] ?? null;
    }

    private function getGivenType(string $errorMessage): string
    {
        preg_match('/must be (.*), (.*) given/', $errorMessage, $matches);

        return $matches[2] ?? 'null';
    }

    private function invalidEnumMessage(string $message): string
    {
        /** @var string $value */
        /** @var class-string<BackedEnum> $class */
        [$value, $class] = array_map(
            fn(string $piece) => trim($piece, " \""),
            explode('is not a valid backing value for enum', $message),
        );

        $type = substr($class, (int)strrpos($class, '\\') + 1);
        $cases = join(', ', array_map(fn(BackedEnum $enum) => $enum->value, $class::cases()));

        return sprintf('Value "%s" is not valid for %s(%s)', $value, $type, $cases);
    }
}
