<?php

declare(strict_types=1);

namespace Serializer;

use ReflectionException;
use Serializer\Builder\ClassAnalyzer;
use Serializer\Builder\DecoderTemplate;
use Serializer\Builder\ReflectionClass;
use Serializer\Exception\ClassMustHaveAConstructor;
use Serializer\Exception\UnableToLoadOrCreateCacheClass;

class DecoderFactory
{
    private string $cacheDir;
    private bool $checkTimestamp;

    /** @var array<string, string> */
    private array $customDecoders;

    /** @var array<string, SerializerFactory> */
    private array $factories;

    /**
     * @param array<string, string> $customDecoders
     * @param array<string, SerializerFactory> $factories
     */
    public function __construct(
        string $cacheDir,
        bool $checkTimestamp = false,
        array $customDecoders = [],
        array $factories = []
    ) {
        $this->cacheDir = sprintf('%s/serializer', rtrim($cacheDir, '/'));
        $this->checkTimestamp = $checkTimestamp;
        $this->customDecoders = $customDecoders;
        $this->factories = $factories;
    }

    /**
     * @throws ClassMustHaveAConstructor
     * @throws UnableToLoadOrCreateCacheClass
     * @throws ReflectionException
     */
    public function createDecoder(Serializer $serializer, string $class): Decoder
    {
        $customClass = $this->customDecoders[$class] ?? null;

        if ($customClass) {
            return new $customClass($serializer);
        }

        $factory = $this->factories[$class] ?? null;

        if (null !== $factory) {
            assert($factory instanceof SerializerFactory);

            return $factory->createDecoder($serializer);
        }

        $decoder = sprintf('Serializer\Decoder\%sDecoder', str_replace('\\', '', $class));

        if (false === class_exists($decoder)) {
            $this->require($class);
        }

        if (false === class_exists($decoder)) {
            throw new UnableToLoadOrCreateCacheClass($decoder);
        }

        return new $decoder($serializer);
    }

    /**
     * @throws ClassMustHaveAConstructor
     * @throws ReflectionException
     */
    private function require(string $class): void
    {
        $factoryName = str_replace('\\', '', $class) . 'Decoder';
        $filePath = sprintf('%s/Decoder/%s.php', $this->cacheDir, $factoryName);

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
        $template = new DecoderTemplate($definition, $factoryName);
        $dirname = dirname($filePath);

        is_dir($dirname) ?: mkdir($dirname, 0777, true);
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
