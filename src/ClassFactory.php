<?php

declare(strict_types=1);

namespace Serializer;

use ReflectionClass;
use ReflectionException;
use Serializer\Builder\ClassAnalyzer;
use Serializer\Builder\ClassTemplate;
use Serializer\Exception\ClassMustHaveAConstructor;
use Serializer\Exception\UnableToLoadOrCreateCacheClass;

class ClassFactory
{
    /** @var string */
    private $cacheFolder;

    /** @var bool */
    private $checkTimestamp;

    public function __construct(string $cachePath, bool $checkTimestamp = false)
    {
        $this->cacheFolder = sprintf('%s/serializer', rtrim($cachePath, '/'));
        $this->checkTimestamp = $checkTimestamp;
    }

    /**
     * @throws ClassMustHaveAConstructor
     * @throws ReflectionException
     * @throws UnableToLoadOrCreateCacheClass
     */
    public function createInstance(Serializer $serializer, string $class): Hydrator
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

    /**
     * @throws ClassMustHaveAConstructor
     * @throws ReflectionException
     */
    private function require(string $class): void
    {
        $factoryName = str_replace('\\', '_', $class) . '_Factory';
        $filePath = sprintf('%s/%s.php', $this->cacheFolder, $factoryName);

        if (false === is_file($filePath) || $this->isOutdated($class, $filePath)) {
            $this->createClassFile($class, $filePath, $factoryName);
        }

        require_once $filePath;
    }

    /**
     * @throws ClassMustHaveAConstructor
     */
    private function createClassFile(string $class, string $filePath, string $factoryName): void
    {
        $definition = (new ClassAnalyzer($class))->analyze();
        $template = new ClassTemplate($definition, $factoryName);

        is_dir($this->cacheFolder) ?: mkdir($this->cacheFolder, 0777, true);
        file_put_contents($filePath, (string) $template);
    }

    /**
     * @throws ReflectionException
     */
    private function isOutdated(string $class, string $cachePath): bool
    {
        if (false === $this->checkTimestamp) {
            return false;
        }

        $classPath = (new ReflectionClass($class))->getFileName() ?: '';

        $classTime = filemtime($classPath);
        $cacheTime = filemtime($cachePath);

        return $classTime > $cacheTime;
    }
}
