<?php

namespace Leaf\Cache;

use Psr\SimpleCache\CacheInterface;

class Cache implements CacheInterface
{
    protected CacheInterface $driver;

    public function __construct()
    {
        $driverName = \Leaf\Config::get('cache.driver');
        $this->driver = match ($driverName) {
            'file' => new Drivers\FileDriver(),
            'apcu' => new Drivers\ApcuDriver(),
            'redis' => new Drivers\RedisDriver(),
            default => throw new \Exception('Invalid cache driver'),
        };
    }

    public function get(string $key, mixed $default = null): mixed { return $this->driver->get($key, $default); }
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool { return $this->driver->set($key, $value, $ttl); }
    public function delete(string $key): bool { return $this->driver->delete($key); }
    public function clear(): bool { return $this->driver->clear(); }
    public function getMultiple(iterable $keys, mixed $default = null): iterable { return $this->driver->getMultiple($keys, $default); }
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool { return $this->driver->setMultiple($values, $ttl); }
    public function deleteMultiple(iterable $keys): bool { return $this->driver->deleteMultiple($keys); }
    public function has(string $key): bool { return $this->driver->has($key); }
}