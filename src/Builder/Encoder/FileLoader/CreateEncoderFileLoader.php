<?php

declare(strict_types=1);

namespace Serializer\Builder\Encoder\FileLoader;

use Serializer\Builder\ClassAnalyzer;
use Serializer\Builder\Encoder\EncoderFileLoader;
use Serializer\Builder\EncoderTemplate;
use Serializer\Encoder;
use Serializer\Serializer;

class CreateEncoderFileLoader implements EncoderFileLoader
{
    private string $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function load(Serializer $serializer, string $class): ?Encoder
    {
        $this->createFile($class);

        $requirer = new RequireEncoderFileLoader($this->cacheDir);

        return $requirer->load($serializer, $class);
    }

    private function createFile(string $class): void
    {
        umask(0002);
        $factoryName = str_replace('\\', '_', $class) . 'Encoder';
        $filePath = sprintf('%s/Encoder/%s.php', $this->cacheDir, $factoryName);

        $definition = (new ClassAnalyzer($class))->analyze();
        $template = new EncoderTemplate($definition, $factoryName);
        $dirname = dirname($filePath);

        if (false === is_dir($dirname)) {
            mkdir($dirname, 0775, true);
        }

        file_put_contents($filePath, (string) $template);
    }
}
