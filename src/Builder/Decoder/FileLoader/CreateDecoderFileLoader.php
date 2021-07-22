<?php

declare(strict_types=1);

namespace Serializer\Builder\Decoder\FileLoader;

use Serializer\Builder\ClassAnalyzer;
use Serializer\Builder\Decoder\DecoderFileLoader;
use Serializer\Builder\DecoderTemplate;
use Serializer\Decoder;
use Serializer\Serializer;

class CreateDecoderFileLoader implements DecoderFileLoader
{
    private string $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function load(Serializer $serializer, string $class): ?Decoder
    {
        $this->createFile($class);

        $requirer = new RequireDecoderFileLoader($this->cacheDir);

        return $requirer->load($serializer, $class);
    }

    private function createFile(string $class): void
    {
        umask(0002);
        $factoryName = str_replace('\\', '_', $class) . 'Decoder';
        $filePath = sprintf('%s/Decoder/%s.php', $this->cacheDir, $factoryName);

        $definition = (new ClassAnalyzer($class))->analyze();
        $template = new DecoderTemplate($definition, $factoryName);
        $dirname = dirname($filePath);

        if (false === is_dir($dirname)) {
            mkdir($dirname, 0775, true);
        }

        file_put_contents($filePath, (string) $template);
    }
}
