<?php

declare(strict_types=1);

namespace Serializer\Builder\Decoder\FileLoader;

use Serializer\Builder\Decoder\DecoderFileLoader;
use Serializer\Decoder;
use Serializer\Serializer;

class RequireDecoderFileLoader implements DecoderFileLoader
{
    private string $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function load(Serializer $serializer, string $class): ?Decoder
    {
        $factoryName = str_replace('\\', '_', $class) . 'Decoder';
        $filePath = sprintf('%s/Decoder/%s.php', $this->cacheDir, $factoryName);
        $decoder = sprintf('Serializer\Decoder\%sDecoder', str_replace('\\', '_', $class));

        if (false === class_exists($decoder) && false === file_exists($filePath)) {
            return null;
        }

        if (false === class_exists($decoder)) {
            require_once $filePath;
        }

        return new $decoder($serializer);
    }
}
