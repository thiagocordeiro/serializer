<?php

declare(strict_types=1);

namespace Serializer;

use ReflectionException;
use Serializer\Builder\ClassAnalyzer;
use Serializer\Builder\ClassTemplate;
use Serializer\Builder\ReflectionClass;
use Serializer\Exception\ClassMustHaveAConstructor;
use Serializer\Exception\UnableToLoadOrCreateCacheClass;

class ClassFactory
{
    /** @var string */
    private $cacheDir;

    /** @var bool */
    private $checkTimestamp;

    /** @var array<string, string> */
    private $customFactories;

    /**
     * @param array<string, string> $customFactories
     */
    public function __construct(string $cacheDir, bool $checkTimestamp = false, array $customFactories = [])
    {
        $this->cacheDir = sprintf('%s/serializer', rtrim($cacheDir, '/'));
        $this->checkTimestamp = $checkTimestamp;
        $this->customFactories = $customFactories;
    }

    /**
     * @throws ClassMustHaveAConstructor
     * @throws UnableToLoadOrCreateCacheClass
     * @throws ReflectionException
     */
    public function createInstance(Serializer $serializer, string $class): Parser
    {
        $customClass = $this->customFactories[$class] ?? null;

        if ($customClass) {
            return new $customClass($serializer);
        }

        $factoryClass = sprintf('Serializer\Parser\%sParser', str_replace('\\', '', $class));

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
        $factoryName = str_replace('\\', '', $class) . 'Parser';
        $filePath = sprintf('%s/%s.php', $this->cacheDir, $factoryName);

        if (false === is_file($filePath) || $this->isOutdated($class, $filePath)) {
            $this->createClassFile($class, $filePath, $factoryName);
        }

        require_once $filePath;
    }

    /**
     * @throws ClassMustHaveAConstructor
     * @throws ReflectionException
     */
    private function createClassFile(string $class, string $filePath, string $factoryName): void
    {
        $definition = (new ClassAnalyzer($class))->analyze();
        $template = new ClassTemplate($definition, $factoryName);

        is_dir($this->cacheDir) ?: mkdir($this->cacheDir, 0777, true);
        file_put_contents($filePath, (string) $template);
    }

    /**
     * @throws ReflectionException
     */
    private function isOutdated(string $class, string $cacheFilename): bool
    {
        if (false === $this->checkTimestamp) {
            return false;
        }

        $classInfo = new ReflectionClass($class);
        $classPath = $classInfo->getFileName() ?: '';

        $classTime = filemtime($classPath);
        $cacheTime = filemtime($cacheFilename);

        return $classTime > $cacheTime;
    }
}
