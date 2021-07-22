<?php

declare(strict_types=1);

namespace Serializer\Builder\Encoder\FileLoader;

use Serializer\Builder\Encoder\EncoderFileLoader;
use Serializer\Encoder;
use Serializer\Serializer;

class RequireEncoderFileLoader implements EncoderFileLoader
{
    private string $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function load(Serializer $serializer, string $class): ?Encoder
    {
        $factoryName = str_replace('\\', '_', $class) . 'Encoder';
        $filePath = sprintf('%s/Encoder/%s.php', $this->cacheDir, $factoryName);
        $encoder = sprintf('Serializer\Encoder\%sEncoder', str_replace('\\', '_', $class));

        if (false === class_exists($encoder) && false === file_exists($filePath)) {
            return null;
        }

        if (false === class_exists($encoder)) {
            require_once $filePath;
        }

        return new $encoder($serializer);
    }
}
