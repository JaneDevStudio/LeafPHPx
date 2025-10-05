<?php

namespace Leaf;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class NotFoundException extends \RuntimeException implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct("Service '{$id}' not found in container.");
    }
}

class ContainerException extends \RuntimeException implements ContainerExceptionInterface
{
    public function __construct(\Throwable $previous)
    {
        parent::__construct("Container error: " . $previous->getMessage(), 0, $previous);
    }
}

class Container implements ContainerInterface
{
    protected array $definitions = [];
    protected array $instances = [];
    protected array $resolving = [];  // 防循环依赖

    public function set(string $id, callable $factory): void
    {
        $this->definitions[$id] = $factory;
        unset($this->instances[$id]);  // 失效缓存，如果重设
    }

    public function get(string $id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->definitions[$id])) {
            throw new NotFoundException($id);
        }

        if (isset($this->resolving[$id])) {
            throw new ContainerException(new \RuntimeException("Circular dependency detected for '{$id}'."));
        }

        $this->resolving[$id] = true;

        try {
            // 临时调试：日志 factory 调用
            error_log("Container resolving '{$id}' via factory.");
            $instance = $this->definitions[$id]($this);
            if (!is_object($instance) && $id === 'db') {  // 特定检查 'db'
                error_log("Warning: 'db' factory returned non-object: " . gettype($instance));
            }
            $this->instances[$id] = $instance;
            return $instance;
        } catch (\Throwable $e) {
            error_log("Container factory for '{$id}' failed: " . $e->getMessage());
            throw new ContainerException($e);
        } finally {
            unset($this->resolving[$id]);
        }
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]) || isset($this->instances[$id]);
    }

    public function resolve(string $class)
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        if (isset($this->resolving[$class])) {
            throw new ContainerException(new \RuntimeException("Circular dependency in resolve '{$class}'."));
        }

        $this->resolving[$class] = true;

        try {
            $reflection = new ReflectionClass($class);
            if (!$reflection->isInstantiable()) {
                throw new ContainerException(new \RuntimeException("Class '{$class}' not instantiable."));
            }

            $constructor = $reflection->getConstructor();
            if (!$constructor) {
                $instance = $reflection->newInstance();
            } else {
                $params = $this->resolveConstructorParams($constructor);
                $instance = $reflection->newInstanceArgs($params);
            }

            // 可选：缓存 resolve 实例（如果想单例）
            // $this->instances[$class] = $instance;

            return $instance;
        } catch (ReflectionException $e) {
            throw new ContainerException($e);
        } catch (\Throwable $e) {
            throw new ContainerException($e);
        } finally {
            unset($this->resolving[$class]);
        }
    }

    protected function resolveConstructorParams(\ReflectionMethod $constructor): array
    {
        $params = [];
        foreach ($constructor->getParameters() as $param) {
            $paramValue = $this->resolveParam($param);
            $params[] = $paramValue;
        }
        return $params;
    }

    protected function resolveParam(ReflectionParameter $param)
    {
        $paramName = $param->getName();
        $type = $param->getType();

        if ($type && !$type->isBuiltin()) {
            $typeName = $type->getName();
            return $this->get($typeName);  // 递归 get
        }

        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }

        if ($param->allowsNull() && $type && $type->allowsNull()) {
            return null;
        }

        $typeStr = $type ? $type->getName() : 'unknown';
        throw new ContainerException(new \RuntimeException("Cannot resolve required parameter '{$paramName}' of type '{$typeStr}'."));
    }
}