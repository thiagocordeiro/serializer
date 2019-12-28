<?php

declare(strict_types=1);

namespace Serializer;

use Serializer\Builder\ClassAnalyzer;
use Serializer\Builder\ClassTemplate;
use Serializer\Exception\UnableToLoadOrCreateCacheClass;

class ClassFactory
{
    /** @var string */
    private $cacheFolder;

    public function __construct(string $cachePath)
    {
        $this->cacheFolder = sprintf('%s/serializer', rtrim($cachePath, '/'));
    }

    public function createInstance(Serializer $serializer, string $class): Deserializer
    {
        $factoryClass = sprintf('Serializer\Cache\%s_Factory', str_replace('\\', '_', $class));

        if (false === class_exists($factoryClass)) {
            $this->require($class);
        }

        if (false === class_exists($factoryClass)) {
            throw new UnableToLoadOrCreateCacheClass($factoryClass);
        }

        return new $factoryClass($serializer);
    }

    private function require(string $class): void
    {
        $factoryName = str_replace('\\', '_', $class) . '_Factory';
        $filePath = sprintf('%s/%s.php', $this->cacheFolder, $factoryName);

        if (false === is_file($filePath)) {
            $this->createClassFile($class, $filePath, $factoryName);
        }

        require_once $filePath;
    }

    private function createClassFile(string $class, string $filePath, string $factoryName): void
    {
        $definition = (new ClassAnalyzer($class))->analyze();
        $template = new ClassTemplate($definition, $factoryName);

        is_dir($this->cacheFolder) ?: mkdir($this->cacheFolder, 0777, true);
        file_put_contents($filePath, (string) $template);
    }
}
