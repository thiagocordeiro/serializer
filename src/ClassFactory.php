<?php

declare(strict_types=1);

namespace Serializer;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class ClassFactory
{
    private const TYPE_ARRAY = 'array';
    private const TYPE_BOOL = 'bool';
    private const TYPE_FLOAT = 'float';
    private const TYPE_INT = 'int';
    private const TYPE_STRING = 'string';

    /** @var string */
    private $cacheFolder;

    public function __construct(string $cachePath)
    {
        $this->cacheFolder = sprintf('%s/serializer', rtrim($cachePath, '/'));
    }

    /**
     * @throws
     */
    public function createInstance(Serializer $serializer, string $class): Deserializer
    {
        $factoryClass = sprintf('Serializer\Cache\%s_Factory', str_replace('\\', '_', $class));

        if (false === class_exists($factoryClass)) {
            $this->require($class);
        }

        if (false === class_exists($factoryClass)) {
            throw new Exception('Unable to create/load cache');
        }

        return new $factoryClass($serializer);
    }

    /**
     * @throws ReflectionException
     */
    private function require(string $class): void
    {
        $factoryName = str_replace('\\', '_', $class) . '_Factory';
        $filePath = sprintf('%s/%s.php', $this->cacheFolder, $factoryName);

        if (false === is_file($filePath)) {
            $this->createClassFile($class, $filePath, $factoryName);
        }

        require_once $filePath;
    }

    /**
     * @throws ReflectionException
     */
    private function createClassFile(string $class, string $filePath, string $factoryName): void
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        is_dir($this->cacheFolder) ?: mkdir($this->cacheFolder, 0777, true);

        $arguments = array_map(function (ReflectionParameter $param) {
            return $this->createArgument($param);
        }, $constructor->getParameters());

        $template = file_get_contents(__DIR__ . '/template/template.php.txt');

        $template = str_replace('[cacheClassName]', $factoryName, $template);
        $template = str_replace('[className]', $class, $template);
        $template = str_replace('[arguments]', trim(implode(",\n", $arguments)), $template);

        file_put_contents($filePath, $template);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    private function createArgument(ReflectionParameter $param): string
    {
        $type = (string) $param->getType();
        $defaultValue = ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : 'null') ?: 'null';

        if (true === $this->isScalar($type)) {
            return sprintf("%s\$data->%s ?? %s", str_repeat(' ', 12), $param->getName(), $defaultValue);
        }

        if ($type === self::TYPE_ARRAY) {
            $type = $this->getArrayType($param);

            return vsprintf("%s\$this->parseArrayData(\$data->%s ?? %s, \%s::class)", [
                str_repeat(' ', 12),
                $param->getName(),
                $defaultValue,
                $type,
            ]);
        }

        return vsprintf("%s\$this->serializer()->parseData(\$data->%s ?? %s, \%s::class)", [
            str_repeat(' ', 12),
            $param->getName(),
            $defaultValue,
            $type,
        ]);
    }

    /**
     * @throws Exception
     */
    private function getArrayType(ReflectionParameter $param): string
    {
        $type = $this->findTypeOnDocComment($param);
        $namespace = $this->findNamespace($param->getDeclaringClass(), $type);

        return sprintf('%s%s', $namespace, $type);
    }

    /**
     * @throws Exception
     */
    private function findTypeOnDocComment(ReflectionParameter $param): string
    {
        $pattern = sprintf('/\@param(.*)\$%s/', $param->getName());

        $class = $param->getDeclaringClass();
        $constructor = $class->getConstructor();

        preg_match($pattern, $constructor->getDocComment(), $matches);
        $type = trim($matches[1] ?? '');

        if ('' === $type) {
            throw new Exception('Traversable property must have an array annotation');
        }

        if (false === strpos($type, '[]')) {
            throw new Exception(sprintf('Traversable property must have an array annotation, use %s[] instead', $type));
        }

        return str_replace('[]', '', $type);
    }

    private function findNamespace(ReflectionClass $class, string $type): string
    {
        $parts = explode('\\', $type);
        $subNs = reset($parts);

        if ('' === $subNs) {
            return trim($type, '\\');
        }

        $lines = array_slice(file($class->getFileName()), 0, $class->getStartLine());

        $pattern = sprintf('/use(.*)%s;/', $subNs);
        preg_match($pattern, implode(PHP_EOL, $lines), $matches);

        $match = trim($matches[0] ?? '');
        $namespace = trim($matches[1] ?? '');

        if ('' === $namespace && '' === $match && '' !== $subNs) {
            $namespace = sprintf('%s\\', $class->getNamespaceName());
        }

        return $namespace;
    }

    private function isScalar(string $type): bool
    {
        return in_array($type, [self::TYPE_INT, self::TYPE_FLOAT, self::TYPE_STRING, self::TYPE_BOOL]);
    }
}
