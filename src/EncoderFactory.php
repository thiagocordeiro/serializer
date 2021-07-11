<?php

declare(strict_types=1);

namespace Serializer;

use ReflectionException;
use Serializer\Builder\ClassAnalyzer;
use Serializer\Builder\EncoderTemplate;
use Serializer\Builder\ReflectionClass;
use Serializer\Exception\ClassMustHaveAConstructor;
use Serializer\Exception\UnableToLoadOrCreateCacheClass;

class EncoderFactory
{
    private string $cacheDir;
    private bool $checkTimestamp;
    private ?string $fileUser;
    private ?string $fileGroup;

    /** @var array<string, string> */
    private array $customEncoders;

    /** @var array<string, SerializerFactory> */
    private array $factories;

    /**
     * @param array<string, string> $customEncoders
     * @param array<string, SerializerFactory> $factories
     */
    public function __construct(
        string $cacheDir,
        bool $checkTimestamp = false,
        array $customEncoders = [],
        array $factories = [],
        ?string $fileUser = null,
        ?string $fileGroup = null,
    ) {
        $this->cacheDir = sprintf('%s/serializer', rtrim($cacheDir, '/'));
        $this->checkTimestamp = $checkTimestamp;
        $this->customEncoders = $customEncoders;
        $this->factories = $factories;
        $this->fileUser = $fileUser;
        $this->fileGroup = $fileGroup;
    }

    /**
     * @throws ClassMustHaveAConstructor
     * @throws UnableToLoadOrCreateCacheClass
     * @throws ReflectionException
     */
    public function createEncoder(Serializer $serializer, string $class): Encoder
    {
        $customClass = $this->customEncoders[$class] ?? null;

        if ($customClass) {
            return new $customClass($serializer);
        }

        $factory = $this->factories[$class] ?? null;

        if (null !== $factory) {
            assert($factory instanceof SerializerFactory);

            return $factory->createEncoder($serializer);
        }

        $encoder = sprintf('Serializer\Encoder\%sEncoder', str_replace('\\', '', $class));

        if (false === class_exists($encoder)) {
            $this->require($class);
        }

        if (false === class_exists($encoder)) {
            throw new UnableToLoadOrCreateCacheClass($encoder);
        }

        return new $encoder($serializer);
    }

    /**
     * @throws ClassMustHaveAConstructor
     * @throws ReflectionException
     */
    private function require(string $class): void
    {
        $factoryName = str_replace('\\', '', $class) . 'Encoder';
        $filePath = sprintf('%s/Encoder/%s.php', $this->cacheDir, $factoryName);

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
        $template = new EncoderTemplate($definition, $factoryName);
        $dirname = dirname($filePath);

        if (false === is_dir($dirname)) {
            mkdir($dirname, 0777, true);
            $this->fixPermission($dirname);
        }

        file_put_contents($filePath, (string) $template);
        $this->fixPermission($filePath);
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

    private function fixPermission(string $path): void
    {
        $this->fixUserPermission($path);
        $this->fixGroupPermission($path);
    }

    private function fixUserPermission(string $path): void
    {
        if (null === $this->fileUser) {
            return;
        }

        chown($path, $this->fileUser);
    }

    private function fixGroupPermission(string $path): void
    {
        if (null === $this->fileGroup) {
            return;
        }

        chgrp($path, $this->fileGroup);
    }
}
